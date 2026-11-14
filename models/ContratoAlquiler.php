
<?php

require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/DetalleContrato.php';
require_once __DIR__ . '/ValidadorFechasContrato.php';

class ContratoAlquiler
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    // LISTAR TODOS LOS CONTRATOS
    public function listar()
    {
        $sql = "SELECT
                    ca.*,
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

        return $this->pdo->query($sql)->fetchAll();
    }

    // REGISTRAR UN NUEVO CONTRATO
    public function registrar($datos)
    {
        try {
            $this->pdo->beginTransaction();

            // VALIDAR LAS FECHAS
            $dias = ValidadorFechasContrato::calcularDias(
                $datos['fecha_inicio'] ?? '',
                $datos['fecha_fin_prevista'] ?? ''
            );

            // CONSULTAR EL EQUIPO SELECCIONADO
            $sqlEquipo = "SELECT
                            precio_alquiler,
                            estado_operativo
                          FROM equipo
                          WHERE id_equipo = ?
                          FOR UPDATE";

            $stmtEq = $this->pdo->prepare($sqlEquipo);
            $stmtEq->execute([$datos['id_equipo']]);

            $equipo = $stmtEq->fetch();

            // VALIDAR DISPONIBILIDAD ACTUAL
            if (
                !$equipo ||
                $equipo['estado_operativo'] !== 'Disponible'
            ) {
                throw new Exception(
                    "El equipo seleccionado no esta disponible."
                );
            }

            // CALCULAR EL COSTO DEL ALQUILER
            $precio = (float) $equipo['precio_alquiler'];
            $subtotal = $precio * $dias;

            // GENERAR NUMERO DE CONTRATO
            $numeroContrato = 'CTR-' .
                date('Ymd') . '-' .
                rand(100, 999);

            // REGISTRAR EL CONTRATO
            $sqlContrato = "INSERT INTO contrato_alquiler
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
                VALUES (?, ?, ?, CURDATE(), ?, ?, ?, 'Activo')";

            $stmtCtr = $this->pdo->prepare($sqlContrato);

            $stmtCtr->execute([
                $datos['id_usuario'],
                $datos['id_cliente'],
                $numeroContrato,
                $datos['fecha_inicio'],
                $datos['fecha_fin_prevista'],
                $subtotal
            ]);

            // OBTENER EL ID DEL NUEVO CONTRATO
            $idContrato = $this->pdo->lastInsertId();

            // REGISTRAR EL DETALLE DEL CONTRATO
            $detalleModel = new DetalleContrato($this->pdo);

            $detalleModel->insertar(
                $idContrato,
                $datos['id_equipo'],
                $precio,
                $dias,
                $subtotal
            );

            // ACTUALIZAR EL ESTADO DEL EQUIPO
            $sqlActualizar = "UPDATE equipo
                              SET estado_operativo = 'Ocupado'
                              WHERE id_equipo = ?";

            $stmtActualizar = $this->pdo->prepare($sqlActualizar);

            $stmtActualizar->execute([
                $datos['id_equipo']
            ]);

            // CONFIRMAR LA TRANSACCION
            $this->pdo->commit();

            return [
                'ok' => true,
                'mensaje' => "Contrato $numeroContrato registrado correctamente. Total: S/ " .
                    number_format($subtotal, 2)
            ];

        } catch (Throwable $e) {

            // DESHACER CAMBIOS SI OCURRE UN ERROR
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'ok' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }
}
