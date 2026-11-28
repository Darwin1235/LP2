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

    public function register() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=dashboard");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre     = trim($_POST['nombre'] ?? '');
            $apellido   = trim($_POST['apellido'] ?? '');
            $correo     = trim($_POST['correo'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');
            $confirmar  = trim($_POST['confirmar_contrasena'] ?? '');
            $rol        = 'Encargado'; // Todo nuevo registro se asigna exclusivamente como Encargado

            if ($nombre === '' || $apellido === '' || $correo === '' || $contrasena === '') {
                $error = "Todos los campos son obligatorios.";
                require __DIR__ . '/../views/auth/register.php';
                return;
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $error = "El formato del correo electrónico no es válido.";
                require __DIR__ . '/../views/auth/register.php';
                return;
            }

            if (strlen($contrasena) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres.";
                require __DIR__ . '/../views/auth/register.php';
                return;
            }

            if ($contrasena !== $confirmar) {
                $error = "Las contraseñas no coinciden.";
                require __DIR__ . '/../views/auth/register.php';
                return;
            }

            $modelo = new Usuario();

            if ($modelo->existeCorreo($correo)) {
                $error = "Ese correo ya está registrado en el sistema.";
                require __DIR__ . '/../views/auth/register.php';
                return;
            }

            if ($modelo->registrar($nombre, $apellido, $correo, $contrasena, $rol)) {
                header("Location: index.php?action=login&registered=1");
                exit();
            } else {
                $error = "No se pudo completar el registro. Intente nuevamente.";
            }
        }

        require __DIR__ . '/../views/auth/register.php';
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