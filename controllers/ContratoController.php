<?php
require_once __DIR__ . '/../models/ContratoAlquiler.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Equipo.php';

class ContratoController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $contratoModel = new ContratoAlquiler();
        $clienteModel = new Cliente();
        $equipoModel = new Equipo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCliente = $_POST['id_cliente'] ?? '';
            $idEquipo = $_POST['id_equipo'] ?? '';
            $fechaInicio = $_POST['fecha_inicio'] ?? '';
            $fechaFin = $_POST['fecha_fin_prevista'] ?? '';

            if ($idCliente === '' || $idEquipo === '' || $fechaInicio === '' || $fechaFin === '') {
                $_SESSION['alerta_contrato'] = ['ok' => false, 'mensaje' => 'Todos los campos del contrato son obligatorios.'];
            } elseif ($fechaFin < $fechaInicio) {
                $_SESSION['alerta_contrato'] = ['ok' => false, 'mensaje' => 'La fecha de fin prevista no puede ser anterior a la fecha de inicio.'];
            } else {
                $datos = [
                    'id_usuario' => $_SESSION['usuario']['id_usuario'] ?? 1,
                    'id_cliente' => $idCliente,
                    'id_equipo' => $idEquipo,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin_prevista' => $fechaFin
                ];
                $_SESSION['alerta_contrato'] = $contratoModel->registrar($datos);
            }

            header("Location: index.php?action=contratos");
            exit();
        }

        $alerta = $_SESSION['alerta_contrato'] ?? null;
        unset($_SESSION['alerta_contrato']);

        $contratos = $contratoModel->listar();
        $clientes = $clienteModel->listar();
        $equiposDisponibles = $equipoModel->listarDisponibles();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/contratos/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}