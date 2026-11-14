<?php
require_once __DIR__ . '/../config/Conexion.php';

class Equipo {
    private $pdo;
    public function __construct() { $this->pdo = Conexion::conectar(); }

    public function listar() {
        $sql = "SELECT e.*, c.nombre AS categoria 
                FROM equipo e 
                INNER JOIN categoria_equipo c ON e.id_categoria = c.id_categoria 
                ORDER BY e.id_equipo DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function listarDisponibles() {
        $sql = "SELECT e.*, c.nombre AS categoria 
                FROM equipo e 
                INNER JOIN categoria_equipo c ON e.id_categoria = c.id_categoria 
                WHERE e.estado_operativo = 'Disponible'";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM equipo WHERE id_equipo = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function guardar($datos) {
        $sql = "INSERT INTO equipo (id_categoria, codigo, nombre, descripcion, marca, modelo, precio_alquiler, estado_operativo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['id_categoria'], $datos['codigo'], $datos['nombre'],
            $datos['descripcion'], $datos['marca'], $datos['modelo'],
            $datos['precio_alquiler'], $datos['estado_operativo']
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE equipo SET id_categoria=?, codigo=?, nombre=?, descripcion=?, marca=?, modelo=?, precio_alquiler=?, estado_operativo=? 
                WHERE id_equipo=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['id_categoria'], $datos['codigo'], $datos['nombre'],
            $datos['descripcion'], $datos['marca'], $datos['modelo'],
            $datos['precio_alquiler'], $datos['estado_operativo'], $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM equipo WHERE id_equipo = ?");
        return $stmt->execute([$id]);
    }
}