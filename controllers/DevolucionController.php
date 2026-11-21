<?php
require_once __DIR__ . '/../models/Devolucion.php';

class DevolucionController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $devModel = new Devolucion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idDetalle = $_POST['id_detalle'] ?? '';
            $idContrato = $_POST['id_contrato'] ?? '';
            $idEquipo = $_POST['id_equipo'] ?? '';
            $condicion = $_POST['condicion_equipo'] ?? 'Operativo / Buen Estado';
            $observacion = trim($_POST['observacion'] ?? '');

            if ($idDetalle === '' || $idContrato === '' || $idEquipo === '') {
                $_SESSION['alerta_devolucion'] = ['tipo' => 'danger', 'texto' => 'Datos incompletos para procesar la devolución.'];
            } else {
                try {
                    $ok = $devModel->registrar($idDetalle, $idContrato, $idEquipo, $condicion, $observacion);
                    $_SESSION['alerta_devolucion'] = $ok
                        ? ['tipo' => 'success', 'texto' => 'Devolución registrada correctamente y estado del equipo actualizado.']
                        : ['tipo' => 'danger', 'texto' => 'No se pudo registrar la devolución.'];
                } catch (Exception $e) {
                    $_SESSION['alerta_devolucion'] = ['tipo' => 'danger', 'texto' => 'Este contrato ya fue devuelto o ocurrió un error en la base de datos.'];
                }
            }

            header("Location: index.php?action=devoluciones");
            exit();
        }

        $alerta = $_SESSION['alerta_devolucion'] ?? null;
        unset($_SESSION['alerta_devolucion']);

        $pendientes = $devModel->listarPendientes();
        $historial = $devModel->listarHistorial();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/devoluciones/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}