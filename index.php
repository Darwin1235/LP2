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

    case 'register':
        require_once 'controllers/AuthController.php';
        (new AuthController())->register();
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
        <style>
            body {
                background-color: #f1f5f9;
            }
            .dashboard-header {
                border-bottom: 1px solid #e2e8f0;
                padding-bottom: 1rem;
                margin-bottom: 1.75rem;
            }
            .dashboard-title {
                color: #0f172a;
                font-weight: 700;
                letter-spacing: -0.5px;
            }
            .dashboard-subtitle {
                color: #64748b;
                font-size: 0.92rem;
            }
            .kpi-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-left-width: 5px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                height: 100%;
            }
            .kpi-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.09);
            }
            .kpi-disp    { border-left-color: #10b981; }
            .kpi-ocup    { border-left-color: #f59e0b; }
            .kpi-activos { border-left-color: #2563eb; }
            .kpi-alertas { border-left-color: #ef4444; }

            .kpi-title {
                font-size: 0.78rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.6px;
                color: #64748b;
                margin-bottom: 0.5rem;
            }
            .kpi-number {
                font-size: 2.25rem;
                font-weight: 800;
                color: #0f172a;
                line-height: 1.1;
            }
            .kpi-badge {
                display: inline-block;
                margin-top: 0.6rem;
                font-size: 0.75rem;
                font-weight: 600;
                padding: 3px 10px;
                border-radius: 20px;
            }
            .badge-disp    { background-color: #d1fae5; color: #065f46; }
            .badge-ocup    { background-color: #fef3c7; color: #92400e; }
            .badge-activos { background-color: #dbeafe; color: #1e40af; }
            .badge-alertas { background-color: #fee2e2; color: #991b1b; }

            .resumen-box {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-left: 5px solid #0f172a;
                border-radius: 12px;
                padding: 1.25rem 1.5rem;
                color: #334155;
                box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            }
        </style>

        <div class="dashboard-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h3 class="dashboard-title mb-1">Panel de Control — Empresa M</h3>
                <p class="dashboard-subtitle mb-0">Resumen general de flota, contratos vigentes y alertas del sistema</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card kpi-disp">
                    <div class="card-body p-4">
                        <div class="kpi-title">Equipos Disponibles</div>
                        <div class="kpi-number"><?= $disp ?></div>
                        <span class="kpi-badge badge-disp">Listos para alquiler</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card kpi-ocup">
                    <div class="card-body p-4">
                        <div class="kpi-title">Equipos Alquilados</div>
                        <div class="kpi-number"><?= $ocup ?></div>
                        <span class="kpi-badge badge-ocup">En operación</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card kpi-activos">
                    <div class="card-body p-4">
                        <div class="kpi-title">Contratos Activos</div>
                        <div class="kpi-number"><?= $activos ?></div>
                        <span class="kpi-badge badge-activos">Vigentes hoy</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card kpi-alertas">
                    <div class="card-body p-4">
                        <div class="kpi-title">Alertas Pendientes</div>
                        <div class="kpi-number"><?= $alertas ?></div>
                        <span class="kpi-badge badge-alertas">Requieren atención</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="resumen-box">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <strong style="color: #0f172a;">Resumen Operativo:</strong>
                    Actualmente hay <span class="badge bg-secondary px-2 py-1"><?= $mant ?></span> equipo(s) en taller de mantenimiento. Utilice el menú superior para registrar categorías, equipos, clientes, contratos o gestionar devoluciones y alertas.
                </div>
            </div>
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