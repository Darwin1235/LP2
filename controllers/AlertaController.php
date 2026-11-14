<?php
require_once __DIR__ . '/../models/Notificacion.php';

class AlertaController {
    public function index() {
        $notificacionModel = new Notificacion();
        $notificacionModel->sincronizarAlertasVencimiento();

        if (isset($_GET['leida'])) {
            $notificacionModel->marcarLeida($_GET['leida']);
            header("Location: index.php?action=alertas");
            exit();
        }

        $notificaciones = $notificacionModel->listar();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/alertas/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}