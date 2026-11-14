<?php
require_once __DIR__ . '/../config/Conexion.php';

class DetalleContrato {
    private $pdo;
    public function __construct($pdo = null) {
        $this->pdo = $pdo ?: Conexion::conectar();
    }

    public function insertar($idContrato, $idEquipo, $precio, $dias, $subtotal) {
        $sql = "INSERT INTO detalle_contrato (id_contrato, id_equipo, precio_alquiler, cantidad_dias, subtotal)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$idContrato, $idEquipo, $precio, $dias, $subtotal]);
    }

    public function listarPorContrato($idContrato) {
        $sql = "SELECT dc.*, e.codigo, e.nombre AS equipo
                FROM detalle_contrato dc
                INNER JOIN equipo e ON dc.id_equipo = e.id_equipo
                WHERE dc.id_contrato = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idContrato]);
        return $stmt->fetchAll();
    }
}