<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $clienteModel = new Cliente();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_cliente'] ?? '';
            $documento = trim($_POST['documento'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $correo = trim($_POST['correo'] ?? '');

            try {
                if (!preg_match('/^[0-9]{8,11}$/', $documento)) {
                    throw new Exception("El DNI (8 dígitos) o RUC (11 dígitos) debe contener solo números.");
                }
                if ($nombre === '' || $apellido === '') {
                    throw new Exception("El nombre y apellido/representante son obligatorios.");
                }
                if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("El formato del correo electrónico es inválido.");
                }

                if ($id) {
                    $clienteModel->actualizar($id, $_POST);
                    $_SESSION['alerta_cliente'] = ['tipo' => 'success', 'texto' => 'Cliente actualizado correctamente.'];
                } else {
                    $clienteModel->guardar($_POST);
                    $_SESSION['alerta_cliente'] = ['tipo' => 'success', 'texto' => 'Cliente registrado correctamente.'];
                }
            } catch (PDOException $e) {
                $msg = ($e->getCode() == 23000)
                    ? "Error: Ya existe un cliente registrado con ese DNI o RUC."
                    : "Error en base de datos: " . $e->getMessage();
                $_SESSION['alerta_cliente'] = ['tipo' => 'danger', 'texto' => $msg];
            } catch (Exception $e) {
                $_SESSION['alerta_cliente'] = ['tipo' => 'danger', 'texto' => $e->getMessage()];
            }

            header("Location: index.php?action=clientes");
            exit();
        }

        if (isset($_GET['eliminar'])) {
            try {
                $clienteModel->eliminar($_GET['eliminar']);
                $_SESSION['alerta_cliente'] = ['tipo' => 'success', 'texto' => 'Cliente eliminado correctamente.'];
            } catch (PDOException $e) {
                $_SESSION['alerta_cliente'] = ['tipo' => 'danger', 'texto' => 'No se puede eliminar el cliente porque tiene contratos asociados.'];
            }
            header("Location: index.php?action=clientes");
            exit();
        }

        $alerta = $_SESSION['alerta_cliente'] ?? null;
        unset($_SESSION['alerta_cliente']);

        $clienteEditar = isset($_GET['editar']) ? $clienteModel->obtenerPorId($_GET['editar']) : null;
        $clientes = $clienteModel->listar();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/clientes/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}