<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/DetalleContrato.php';
require_once __DIR__ . '/ValidadorFechasContrato.php';

class ContratoAlquiler
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    // LISTAR TODOS LOS CONTRATOS
    public function listar(): array
    {
        $sql = "SELECT ca.*,
                       c.nombre AS cli_nombre,
                       c.apellido AS cli_apellido,
                       c.documento,
                       (SELECT GROUP_CONCAT(
                                  CONCAT(e.codigo, ' - ', e.nombre)
                                  ORDER BY e.codigo SEPARATOR ', '
                               )
                        FROM detalle_contrato dc
                        INNER JOIN equipo e ON e.id_equipo = dc.id_equipo
                        WHERE dc.id_contrato = ca.id_contrato) AS equipos
                FROM contrato_alquiler ca
                INNER JOIN cliente c ON c.id_cliente = ca.id_cliente
                ORDER BY ca.id_contrato DESC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // REGISTRAR CONTRATO DE ALQUILER
    public function registrar(array $datos): array
    {
        try {

            // 1. VALIDAR EL USUARIO Y CLIENTE
            $idUsuario = filter_var(
                $datos['id_usuario'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            $idCliente = filter_var(
                $datos['id_cliente'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if (
                $idUsuario === false || $idUsuario === null ||
                $idCliente === false || $idCliente === null
            ) {
                throw new InvalidArgumentException(
                    'Debes seleccionar un cliente e iniciar sesion.'
                );
            }

            // 2. VALIDAR LOS EQUIPOS SELECCIONADOS
            $seleccionados = $datos['id_equipos'] ?? null;

            if (
                $seleccionados === null &&
                isset($datos['id_equipo'])
            ) {
                $seleccionados = [$datos['id_equipo']];
            }

            if (
                !is_array($seleccionados) ||
                count($seleccionados) === 0
            ) {
                throw new InvalidArgumentException(
                    'Selecciona una o varias maquinas.'
                );
            }

            $ids = [];

            foreach ($seleccionados as $valor) {

                if (!is_scalar($valor)) {
                    throw new InvalidArgumentException(
                        'La seleccion de equipos es invalida.'
                    );
                }

                $id = filter_var(
                    $valor,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                if ($id === false) {
                    throw new InvalidArgumentException(
                        'La seleccion de equipos es invalida.'
                    );
                }

                $ids[] = (int) $id;
            }

            // EVITAR EQUIPOS REPETIDOS
            $ids = array_values(array_unique($ids));
            sort($ids, SORT_NUMERIC);

            // 3. VALIDAR LAS FECHAS
            $inicio = $datos['fecha_inicio'] ?? '';
            $fin = $datos['fecha_fin_prevista'] ?? '';

            if (!is_string($inicio) || !is_string($fin)) {
                throw new InvalidArgumentException(
                    'Las fechas deben ser validas.'
                );
            }

            $dias = ValidadorFechasContrato::calcularDias(
                $inicio,
                $fin
            );

            // 4. INICIAR TRANSACCION
            $this->pdo->beginTransaction();

            // 5. COMPROBAR QUE EL USUARIO EXISTA
            $stmtUsuario = $this->pdo->prepare(
                'SELECT id_usuario
                 FROM usuario
                 WHERE id_usuario = ?
                 AND estado = 1'
            );

            $stmtUsuario->execute([(int) $idUsuario]);

            if (!$stmtUsuario->fetchColumn()) {
                throw new RuntimeException(
                    'El usuario no existe o esta desactivado.'
                );
            }

            // 6. VERIFICAR EL CLIENTE
            $stmtCliente = $this->pdo->prepare(
                'SELECT id_cliente
                 FROM cliente
                 WHERE id_cliente = ?'
            );

            $stmtCliente->execute([(int) $idCliente]);

            if (!$stmtCliente->fetchColumn()) {
                throw new RuntimeException(
                    'El cliente seleccionado no existe.'
                );
            }

            // 7. CONSULTAR Y BLOQUEAR LOS EQUIPOS
            $marcadores = implode(
                ', ',
                array_fill(0, count($ids), '?')
            );

            $stmtEquipos = $this->pdo->prepare(
                "SELECT id_equipo,
                        codigo,
                        precio_alquiler,
                        estado_operativo
                 FROM equipo
                 WHERE id_equipo IN ($marcadores)
                 ORDER BY id_equipo
                 FOR UPDATE"
            );

            $stmtEquipos->execute($ids);

            $equipos = $stmtEquipos->fetchAll(
                PDO::FETCH_ASSOC
            );

            if (count($equipos) !== count($ids)) {
                throw new RuntimeException(
                    'Una de las maquinas seleccionadas no existe.'
                );
            }

            // 8. COMPROBAR EL ESTADO DE CADA EQUIPO
            foreach ($equipos as $equipo) {

                if (
                    $equipo['estado_operativo'] !== 'Disponible'
                ) {
                    throw new RuntimeException(
                        'La maquina ' .
                        $equipo['codigo'] .
                        ' no esta disponible.'
                    );
                }

                if (
                    (float) $equipo['precio_alquiler'] <= 0
                ) {
                    throw new RuntimeException(
                        'La maquina ' .
                        $equipo['codigo'] .
                        ' tiene un precio incorrecto.'
                    );
                }
            }

            // 9. VALIDAR ALQUILERES QUE SE CRUZAN
            // Y DEVOLUCIONES VENCIDAS PENDIENTES
            $sqlConflictos = "
                SELECT e.codigo, ca.numero_contrato
                FROM detalle_contrato dc

                INNER JOIN contrato_alquiler ca
                    ON ca.id_contrato = dc.id_contrato

                INNER JOIN equipo e
                    ON e.id_equipo = dc.id_equipo

                LEFT JOIN devolucion d
                    ON d.id_detalle = dc.id_detalle

                WHERE dc.id_equipo IN ($marcadores)

                AND ca.estado IN ('Activo', 'Vencido')

                AND d.id_devolucion IS NULL

                AND (
                    (
                        ca.fecha_inicio <= ?
                        AND ca.fecha_fin_prevista >= ?
                    )
                    OR ca.fecha_fin_prevista < CURDATE()
                )

                LIMIT 1
            ";

            $stmtConflictos = $this->pdo->prepare(
                $sqlConflictos
            );

            $stmtConflictos->execute(
                array_merge($ids, [$fin, $inicio])
            );

            $conflicto = $stmtConflictos->fetch(
                PDO::FETCH_ASSOC
            );

            if ($conflicto) {
                throw new RuntimeException(
                    'La maquina ' .
                    $conflicto['codigo'] .
                    ' tiene un alquiler sin devolver: ' .
                    $conflicto['numero_contrato']
                );
            }

            // 10. CALCULAR EL IMPORTE TOTAL
            $importeTotal = 0.0;

            foreach ($equipos as $equipo) {

                $precio = (float) $equipo['precio_alquiler'];

                $subtotal = round(
                    $precio * $dias,
                    2
                );

                $importeTotal += $subtotal;
            }

            $importeTotal = round($importeTotal, 2);

            // 11. GENERAR NUMERO DE CONTRATO
            $numeroContrato = 'CTR-' .
                date('Ymd') . '-' .
                strtoupper(bin2hex(random_bytes(5)));

            // 12. GUARDAR EL CONTRATO
            $stmtContrato = $this->pdo->prepare(
                "INSERT INTO contrato_alquiler
                (
                    id_usuario,
                    id_cliente,
                    numero_contrato,
                    fecha_contrato,
                    fecha_inicio,
                    fecha_fin_prevista,
                    importe_total,
                    estado
                )
                VALUES (
                    ?, ?, ?, CURDATE(), ?, ?, ?, 'Activo'
                )"
            );

            $stmtContrato->execute([
                (int) $idUsuario,
                (int) $idCliente,
                $numeroContrato,
                $inicio,
                $fin,
                $importeTotal
            ]);

            // 13. OBTENER EL ID DEL CONTRATO
            $idContrato = (int) $this->pdo->lastInsertId();

            // 14. REGISTRAR LOS DETALLES DEL CONTRATO
            $detalleModel = new DetalleContrato(
                $this->pdo
            );

            $stmtOcupar = $this->pdo->prepare(
                "UPDATE equipo
                 SET estado_operativo = 'Ocupado'
                 WHERE id_equipo = ?"
            );

            foreach ($equipos as $equipo) {

                $precio = (float) $equipo['precio_alquiler'];

                $subtotal = round(
                    $precio * $dias,
                    2
                );

                $guardado = $detalleModel->insertar(
                    $idContrato,
                    (int) $equipo['id_equipo'],
                    $precio,
                    $dias,
                    $subtotal
                );

                if (!$guardado) {
                    throw new RuntimeException(
                        'Error al registrar una maquina del contrato.'
                    );
                }

                // MARCAR CADA EQUIPO COMO OCUPADO
                $stmtOcupar->execute([
                    (int) $equipo['id_equipo']
                ]);
            }

            // 15. CONFIRMAR LOS CAMBIOS
            $this->pdo->commit();

            return [
                'ok' => true,
                'mensaje' =>
                    'Contrato ' .
                    $numeroContrato .
                    ' registrado con ' .
                    count($equipos) .
                    ' maquina(s). Total: S/ ' .
                    number_format($importeTotal, 2)
            ];

        } catch (Throwable $e) {

            // DESHACER CAMBIOS SI OCURRE UN ERROR
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            // CONTROLAR ERRORES DE BASE DE DATOS
            if ($e instanceof PDOException) {

                error_log(
                    'Error en contratos: ' .
                    $e->getMessage()
                );

                return [
                    'ok' => false,
                    'mensaje' =>
                        'No se pudo registrar el contrato. Revisa la base de datos.'
                ];
            }

            return [
                'ok' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }
}
