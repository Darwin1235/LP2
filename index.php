<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    case 'equipos':
        require_once 'controllers/EquipoController.php';
        (new EquipoController())->index();
        break;
    case 'clientes':
        require_once 'controllers/ClienteController.php';
        (new ClienteController())->index();
        break;
    case 'contratos':
        require_once 'controllers/ContratoController.php';
        (new ContratoController())->index();
        break;
    case 'devoluciones':
        require_once 'controllers/DevolucionController.php';
        (new DevolucionController())->index();
        break;
    case 'alertas':
        require_once 'controllers/AlertaController.php';
        (new AlertaController())->index();
        break;
    default:
        header("Location: index.php?action=login");
        break;
}