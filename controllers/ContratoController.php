<?php
require_once __DIR__ . '/../models/ContratoAlquiler.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Equipo.php';

class ContratoController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $contratoModel = new ContratoAlquiler();
        $clienteModel = new Cliente();
        $equipoModel = new Equipo();

        $alerta = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_usuario' => $_SESSION['usuario']['id_usuario'] ?? 1,
                'id_cliente' => $_POST['id_cliente'],
                'id_equipo' => $_POST['id_equipo'],
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin_prevista' => $_POST['fecha_fin_prevista']
            ];
            $alerta = $contratoModel->registrar($datos);
        }

        $contratos = $contratoModel->listar();
        $clientes = $clienteModel->listar();
        $equiposDisponibles = $equipoModel->listarDisponibles();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/contratos/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}