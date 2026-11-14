<?php
require_once __DIR__ . '/../models/Devolucion.php';

class DevolucionController {
    public function index() {
        $devModel = new Devolucion();
        $mensaje = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $devModel->registrar(
                $_POST['id_detalle'],
                $_POST['id_contrato'],
                $_POST['id_equipo'],
                $_POST['condicion_equipo'],
                trim($_POST['observacion'] ?? '')
            );
            $mensaje = $ok
                ? "Devolución registrada correctamente y estado del equipo actualizado."
                : "Error al registrar la devolución.";
        }

        $pendientes = $devModel->listarPendientes();
        $historial = $devModel->listarHistorial();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/devoluciones/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}