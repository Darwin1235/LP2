<?php
require_once __DIR__ . '/../models/Notificacion.php';

class AlertaController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $notificacionModel = new Notificacion();
        $notificacionModel->sincronizarAlertasVencimiento();

        if (isset($_GET['leida'])) {
            $notificacionModel->marcarLeida($_GET['leida']);
            $_SESSION['alerta_notificacion'] = "Notificación marcada como atendida.";
            header("Location: index.php?action=alertas");
            exit();
        }

        $mensaje = $_SESSION['alerta_notificacion'] ?? null;
        unset($_SESSION['alerta_notificacion']);

        $notificaciones = $notificacionModel->listar();

        require __DIR__ . '/../views/layout/header.php';
        if ($mensaje) {
            echo '<div class="alert alert-success">' . htmlspecialchars($mensaje) . '</div>';
        }
        require __DIR__ . '/../views/alertas/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}