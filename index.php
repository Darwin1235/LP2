<?php
$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        require_once 'controllers/AuthController.php';
        (new AuthController())->login();
        break;
    case 'logout':
        require_once 'controllers/AuthController.php';
        (new AuthController())->logout();
        break;
    case 'dashboard':
        require_once 'views/layout/header.php';
        echo "<div class='alert alert-success'><h4>Bienvenido al Sistema de Alquiler de Maquinaria - Empresa M</h4><p>Seleccione un módulo en el menú superior para comenzar.</p></div>";
        require_once 'views/layout/footer.php';
        break;
    default:
        header("Location: index.php?action=login");
        break;
}