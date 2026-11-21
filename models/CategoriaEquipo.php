<?php
require_once __DIR__ . '/../config/Conexion.php';

class CategoriaEquipo {
    private $pdo;
    public function __construct() { $this->pdo = Conexion::conectar(); }

    public function listar() {
        return $this->pdo->query("SELECT * FROM categoria_equipo ORDER BY id_categoria DESC")->fetchAll();
    }

    public function existeNombre($nombre) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categoria_equipo WHERE LOWER(nombre) = LOWER(?)");
        $stmt->execute([$nombre]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function contarEquiposAsociados($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM equipo WHERE id_categoria = ?");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }

    public function guardar($nombre, $descripcion) {
        $stmt = $this->pdo->prepare("INSERT INTO categoria_equipo (nombre, descripcion) VALUES (?, ?)");
        return $stmt->execute([$nombre, $descripcion]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM categoria_equipo WHERE id_categoria = ?");
        return $stmt->execute([$id]);
    }
}