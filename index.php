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
        require_once 'config/Conexion.php';
        $db = Conexion::conectar();

        $disp    = $db->query("SELECT COUNT(*) FROM equipo WHERE estado_operativo = 'Disponible'")->fetchColumn();
        $ocup    = $db->query("SELECT COUNT(*) FROM equipo WHERE estado_operativo = 'Ocupado'")->fetchColumn();
        $mant    = $db->query("SELECT COUNT(*) FROM equipo WHERE estado_operativo = 'Mantenimiento'")->fetchColumn();
        $activos = $db->query("SELECT COUNT(*) FROM contrato_alquiler WHERE estado = 'Activo'")->fetchColumn();
        $alertas = $db->query("SELECT COUNT(*) FROM notificacion WHERE estado = 'Pendiente'")->fetchColumn();

        require_once 'views/layout/header.php';
        ?>
        <h3 class="mb-4">Panel de Control — Empresa M</h3>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-success shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Equipos Disponibles</h6>
                        <h2 class="mb-0"><?= $disp ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-dark bg-warning shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Equipos Alquilados</h6>
                        <h2 class="mb-0"><?= $ocup ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Contratos Activos</h6>
                        <h2 class="mb-0"><?= $activos ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Alertas Pendientes</h6>
                        <h2 class="mb-0"><?= $alertas ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-light border shadow-sm">
            <strong>Resumen Operativo:</strong> Actualmente hay <strong><?= $mant ?></strong> equipo(s) en taller de mantenimiento. Utilice el menú superior para registrar categorías, equipos, clientes, contratos o gestionar devoluciones y alertas.
        </div>
        <?php
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