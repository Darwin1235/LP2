<?php
require_once __DIR__ . '/../config/Conexion.php';

class Usuario {
    private $pdo;
    public function __construct() {
        $this->pdo = Conexion::conectar();
    }
    public function autenticar($correo, $contrasena) {
        $sql = "SELECT * FROM usuario WHERE correo = :correo AND estado = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $user = $stmt->fetch();
        if ($user && ($contrasena === $user['contrasena'] || password_verify($contrasena, $user['contrasena']))) {
            return $user;
        }
        return false;
    }
}