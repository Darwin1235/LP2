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

    public function existeCorreo($correo) {
        $sql = "SELECT id_usuario FROM usuario WHERE correo = :correo LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        return (bool) $stmt->fetch();
    }

    public function registrar($nombre, $apellido, $correo, $contrasena, $rol) {
        $rolesPermitidos = ['Administrador', 'Encargado'];
        if (!in_array($rol, $rolesPermitidos, true)) {
            $rol = 'Encargado';
        }

        $hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (nombre, apellido, correo, contrasena, rol, estado)
                VALUES (:nombre, :apellido, :correo, :contrasena, :rol, 1)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre'     => $nombre,
            ':apellido'   => $apellido,
            ':correo'     => $correo,
            ':contrasena' => $hash,
            ':rol'        => $rol
        ]);
    }
}