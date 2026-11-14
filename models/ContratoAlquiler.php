
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
                       e.codigo AS eq_codigo,
                       e.nombre AS eq_nombre,
                       dc.cantidad_dias,
                       dc.id_detalle
                FROM contrato_alquiler ca
                INNER JOIN cliente c
                    ON ca.id_cliente = c.id_cliente
                INNER JOIN detalle_contrato dc
                    ON ca.id_contrato = dc.id_contrato
                INNER JOIN equipo e
                    ON dc.id_equipo = e.id_equipo
                ORDER BY ca.id_contrato DESC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // REGISTRAR CONTRATO DE ALQUILER
    public function registrar(array $datos): array
    {
        try {

            // 1. VALIDAR IDENTIFICADORES
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

            $idEquipo = filter_var(
                $datos['id_equipo'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if (!$idUsuario || !$idCliente || !$idEquipo) {
                throw new InvalidArgumentException(
                    "Debes seleccionar un cliente, un equipo y un usuario valido."
                );
            }

            // 2. VALIDAR FECHAS
            $fechaInicio = $datos['fecha_inicio'] ?? '';
            $fechaFin = $datos['fecha_fin_prevista'] ?? '';

            $dias = ValidadorFechasContrato::calcularDias(
                $fechaInicio,
                $fechaFin
            );

            // 3. INICIAR TRANSACCION
            $this->pdo->beginTransaction();

            // 4. CONSULTAR Y BLOQUEAR EL EQUIPO
            $sqlEquipo = "SELECT precio_alquiler,
                                 estado_operativo
                          FROM equipo
                          WHERE id_equipo = ?
                          FOR UPDATE";

            $stmtEquipo = $this->pdo->prepare($sqlEquipo);
            $stmtEquipo->execute([$idEquipo]);

            $equipo = $stmtEquipo->fetch(PDO::FETCH_ASSOC);

            if (!$equipo) {
                throw new RuntimeException(
                    "El equipo seleccionado no existe."
                );
            }

            if ($equipo['estado_operativo'] === 'Mantenimiento') {
                throw new RuntimeException(
                    "El equipo se encuentra en mantenimiento."
                );
            }

            // 5. VERIFICAR ALQUILERES PENDIENTES
            // Detectar fechas cruzadas y devoluciones vencidas
            $sqlConflicto = "
                SELECT ca.numero_contrato
                FROM contrato_alquiler ca
                INNER JOIN detalle_contrato dc
                    ON dc.id_contrato = ca.id_contrato
                LEFT JOIN devolucion d
                    ON d.id_detalle = dc.id_detalle
                WHERE dc.id_equipo = ?
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

            $stmtConflicto = $this->pdo->prepare($sqlConflicto);

            $stmtConflicto->execute([
                $idEquipo,
                $fechaFin,
                $fechaInicio
            ]);

            $conflicto = $stmtConflicto->fetch(PDO::FETCH_ASSOC);

            if ($conflicto) {
                throw new RuntimeException(
                    "El equipo no esta disponible. Tiene un alquiler pendiente: " .
                    $conflicto['numero_contrato']
                );
            }

            // 6. COMPROBAR ESTADO ACTUAL
            if ($equipo['estado_operativo'] !== 'Disponible') {
                throw new RuntimeException(
                    "El equipo figura como ocupado y debe revisarse antes de alquilarlo."
                );
            }

            // 7. CALCULAR IMPORTE
            $precio = (float) $equipo['precio_alquiler'];

            if ($precio <= 0) {
                throw new RuntimeException(
                    "El equipo no tiene un precio de alquiler valido."
                );
            }

            $subtotal = round($precio * $dias, 2);

            // 8. GENERAR NUMERO DE CONTRATO
            $numeroContrato = 'CTR-' .
                date('Ymd') . '-' .
                strtoupper(bin2hex(random_bytes(4)));

            // 9. INSERTAR CONTRATO
            $sqlContrato = "
                INSERT INTO contrato_alquiler
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
                VALUES (?, ?, ?, CURDATE(), ?, ?, ?, 'Activo')
            ";

            $stmtContrato = $this->pdo->prepare($sqlContrato);

            $stmtContrato->execute([
                $idUsuario,
                $idCliente,
                $numeroContrato,
                $fechaInicio,
                $fechaFin,
                $subtotal
            ]);

            $idContrato = (int) $this->pdo->lastInsertId();

            // 10. REGISTRAR DETALLE DEL CONTRATO
            $detalle = new DetalleContrato($this->pdo);

            if (!$detalle->insertar(
                $idContrato,
                $idEquipo,
                $precio,
                $dias,
                $subtotal
            )) {
                throw new RuntimeException(
                    "No se pudo registrar el detalle del contrato."
                );
            }

            // 11. ACTUALIZAR ESTADO DEL EQUIPO
            $sqlActualizar = "
                UPDATE equipo
                SET estado_operativo = 'Ocupado'
                WHERE id_equipo = ?
            ";

            $stmtActualizar = $this->pdo->prepare($sqlActualizar);
            $stmtActualizar->execute([$idEquipo]);

            // 12. CONFIRMAR REGISTRO
            $this->pdo->commit();

            return [
                'ok' => true,
                'mensaje' => 'Contrato ' . $numeroContrato .
                    ' registrado. Total: S/ ' .
                    number_format($subtotal, 2)
            ];

        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if ($e instanceof PDOException) {
                error_log(
                    'Error de registro de contrato: ' .
                    $e->getMessage()
                );

                return [
                    'ok' => false,
                    'mensaje' => 'No se pudo guardar el contrato. Revisa la conexion y los datos.'
                ];
            }

            return [
                'ok' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }
}
