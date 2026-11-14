<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    public function index() {
        $clienteModel = new Cliente();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_cliente'] ?? '';
            if ($id) {
                $clienteModel->actualizar($id, $_POST);
            } else {
                $clienteModel->guardar($_POST);
            }
            header("Location: index.php?action=clientes");
            exit();
        }

        if (isset($_GET['eliminar'])) {
            $clienteModel->eliminar($_GET['eliminar']);
            header("Location: index.php?action=clientes");
            exit();
        }

        $clienteEditar = isset($_GET['editar']) ? $clienteModel->obtenerPorId($_GET['editar']) : null;
        $clientes = $clienteModel->listar();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/clientes/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}