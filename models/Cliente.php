<?php
require_once __DIR__ . '/../config/Conexion.php';

class Cliente {
    private $pdo;
    public function __construct() { $this->pdo = Conexion::conectar(); }

    public function listar() {
        return $this->pdo->query("SELECT * FROM cliente ORDER BY id_cliente DESC")->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt =$this->pdo->prepare("SELECT * FROM cliente WHERE id_cliente = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function existeDocumento($documento,$idExcluir = null) {
        if ($idExcluir) {
            $stmt =$this->pdo->prepare("SELECT COUNT(*) FROM cliente WHERE documento = ? AND id_cliente != ?");
            $stmt->execute([$documento,$idExcluir]);
        } else {
            $stmt =$this->pdo->prepare("SELECT COUNT(*) FROM cliente WHERE documento = ?");
            $stmt->execute([$documento]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }

    public function contarContratosAsociados($id) {
        $stmt =$this->pdo->prepare("SELECT COUNT(*) FROM contrato_alquiler WHERE id_cliente = ?");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }

    public function guardar($datos) {$sql = "INSERT INTO cliente (documento, nombre, apellido, telefono, correo, direccion) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['documento'],$datos['nombre'], $datos['apellido'],$datos['telefono'], $datos['correo'],$datos['direccion']
        ]);
    }

    public function actualizar($id, $datos) {$sql = "UPDATE cliente SET documento=?, nombre=?, apellido=?, telefono=?, correo=?, direccion=? WHERE id_cliente=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$datos['documento'], $datos['nombre'],$datos['apellido'],
            $datos['telefono'],$datos['correo'], $datos['direccion'],$id
        ]);
    }

    public function eliminar($id) {
        $stmt =$this->pdo->prepare("DELETE FROM cliente WHERE id_cliente = ?");
        return $stmt->execute([$id]);
    }
}