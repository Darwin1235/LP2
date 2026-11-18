<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=dashboard");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = trim($_POST['correo'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');

            if ($correo === '' || $contrasena === '') {
                $error = "Debe ingresar su correo y contraseña.";
                require __DIR__ . '/../views/auth/login.php';
                return;
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $error = "Formato de correo inválido.";
                require __DIR__ . '/../views/auth/login.php';
                return;
            }

            $modelo = new Usuario();
            $usuario = $modelo->autenticar($correo, $contrasena);

            if ($usuario) {
                session_regenerate_id(true);
                $_SESSION['usuario'] = $usuario;
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}