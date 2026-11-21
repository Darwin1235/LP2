
<?php
require_once __DIR__ . '/../models/ContratoAlquiler.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Equipo.php';

class ContratoController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Registrar contratos requiere una cuenta que haya iniciado sesion.
        if (empty($_SESSION['usuario']['id_usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if (empty($_SESSION['csrf_contratos'])) {
            $_SESSION['csrf_contratos'] = bin2hex(random_bytes(32));
        }

        $contratoModel = new ContratoAlquiler();
        $clienteModel = new Cliente();
        $equipoModel = new Equipo();
        $alerta = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';

            if (!is_string($token) || !hash_equals($_SESSION['csrf_contratos'], $token)) {
                $alerta = [
                    'ok' => false,
                    'mensaje' => 'Formulario no valido. Actualiza la pagina e intenta otra vez.'
                ];
            } else {
                $datos = [
                    'id_usuario' => $_SESSION['usuario']['id_usuario'],
                    'id_cliente' => $_POST['id_cliente'] ?? null,
                    'id_equipos' => $_POST['id_equipos'] ?? [],
                    'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
                    'fecha_fin_prevista' => $_POST['fecha_fin_prevista'] ?? ''
                ];

                $alerta = $contratoModel->registrar($datos);
            }
        }

        $contratos = $contratoModel->listar();
        $clientes = $clienteModel->listar();
        $equiposDisponibles = $equipoModel->listarDisponibles();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/contratos/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}
