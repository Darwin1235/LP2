<?php
require_once __DIR__ . '/../config/Conexion.php';

class Devolucion {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function listarPendientes() {
        $sql = "SELECT dc.id_detalle, ca.id_contrato, ca.numero_contrato, ca.fecha_fin_prevista,
                       c.nombre AS cli_nombre, c.apellido AS cli_apellido,
                       e.id_equipo, e.codigo, e.nombre AS eq_nombre
                FROM contrato_alquiler ca
                INNER JOIN cliente c ON ca.id_cliente = c.id_cliente
                INNER JOIN detalle_contrato dc ON ca.id_contrato = dc.id_contrato
                INNER JOIN equipo e ON dc.id_equipo = e.id_equipo
                WHERE ca.estado IN ('Activo', 'Vencido')
                ORDER BY ca.fecha_fin_prevista ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function listarHistorial() {
        $sql = "SELECT d.*, ca.numero_contrato, e.codigo, e.nombre AS eq_nombre, 
                       c.nombre AS cli_nombre, c.apellido AS cli_apellido
                FROM devolucion d
                INNER JOIN detalle_contrato dc ON d.id_detalle = dc.id_detalle
                INNER JOIN contrato_alquiler ca ON dc.id_contrato = ca.id_contrato
                INNER JOIN equipo e ON dc.id_equipo = e.id_equipo
                INNER JOIN cliente c ON ca.id_cliente = c.id_cliente
                ORDER BY d.id_devolucion DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function registrar($idDetalle, $idContrato, $idEquipo, $condicion, $observacion) {
        try {
            $this->pdo->beginTransaction();

            // 1. Registrar la devolución
            $sqlDev = "INSERT INTO devolucion (id_detalle, fecha_devolucion, observacion, condicion_equipo) 
                       VALUES (?, CURDATE(), ?, ?)";
            $stmt = $this->pdo->prepare($sqlDev);
            $stmt->execute([$idDetalle, $observacion, $condicion]);

            // 2. Actualizar el contrato a 'Devuelto'
            $this->pdo->prepare("UPDATE contrato_alquiler SET estado = 'Devuelto' WHERE id_contrato = ?")
                      ->execute([$idContrato]);

            // 3. Actualizar el estado del equipo según su condición física
            $nuevoEstadoEquipo = ($condicion === 'Requiere Mantenimiento') ? 'Mantenimiento' : 'Disponible';
            $this->pdo->prepare("UPDATE equipo SET estado_operativo = ? WHERE id_equipo = ?")
                      ->execute([$nuevoEstadoEquipo, $idEquipo]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}