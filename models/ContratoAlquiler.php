<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/DetalleContrato.php';

class ContratoAlquiler {
    private $pdo;
    public function __construct() { $this->pdo = Conexion::conectar(); }

    public function listar() {
        $sql = "SELECT ca.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido, c.documento,
                       e.codigo AS eq_codigo, e.nombre AS eq_nombre, dc.cantidad_dias, dc.id_detalle
                FROM contrato_alquiler ca
                INNER JOIN cliente c ON ca.id_cliente = c.id_cliente
                INNER JOIN detalle_contrato dc ON ca.id_contrato = dc.id_contrato
                INNER JOIN equipo e ON dc.id_equipo = e.id_equipo
                ORDER BY ca.id_contrato DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function registrar($datos) {
        try {
            $this->pdo->beginTransaction();

            $stmtEq = $this->pdo->prepare("SELECT precio_alquiler, estado_operativo FROM equipo WHERE id_equipo = ? FOR UPDATE");
            $stmtEq->execute([$datos['id_equipo']]);
            $equipo = $stmtEq->fetch();

            if (!$equipo || $equipo['estado_operativo'] !== 'Disponible') {
                throw new Exception("El equipo seleccionado ya no está disponible.");
            }

            $inicio = new DateTime($datos['fecha_inicio']);
            $fin = new DateTime($datos['fecha_fin_prevista']);
            if ($fin < $inicio) {
                throw new Exception("La fecha de fin prevista no puede ser anterior a la fecha de inicio.");
            }

            $dias = max(1, $inicio->diff($fin)->days);
            $precio = (float)$equipo['precio_alquiler'];
            $subtotal = $precio * $dias;
            $numeroContrato = 'CTR-' . date('Ymd') . '-' . rand(100, 999);

            $sqlCtr = "INSERT INTO contrato_alquiler (id_usuario, id_cliente, numero_contrato, fecha_contrato, fecha_inicio, fecha_fin_prevista, importe_total, estado)
                       VALUES (?, ?, ?, CURDATE(), ?, ?, ?, 'Activo')";
            $stmtCtr = $this->pdo->prepare($sqlCtr);
            $stmtCtr->execute([
                $datos['id_usuario'], $datos['id_cliente'], $numeroContrato,
                $datos['fecha_inicio'], $datos['fecha_fin_prevista'], $subtotal
            ]);
            $idContrato = $this->pdo->lastInsertId();

            $detalleModel = new DetalleContrato($this->pdo);
            $detalleModel->insertar($idContrato, $datos['id_equipo'], $precio, $dias, $subtotal);

            $this->pdo->prepare("UPDATE equipo SET estado_operativo = 'Ocupado' WHERE id_equipo = ?")->execute([$datos['id_equipo']]);

            $this->pdo->commit();
            return ['ok' => true, 'mensaje' => "Contrato $numeroContrato registrado por S/ " . number_format($subtotal, 2)];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['ok' => false, 'mensaje' => $e->getMessage()];
        }
    }
}