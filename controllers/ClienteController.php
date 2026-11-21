<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $clienteModel = new Cliente();
        $rolUsuario = $_SESSION['usuario']['rol'] ?? 'Encargado';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id        = $_POST['id_cliente'] ?? '';
            $documento = trim($_POST['documento'] ?? '');
            $nombre    = trim($_POST['nombre'] ?? '');
            $apellido  = trim($_POST['apellido'] ?? '');
            $telefono  = trim($_POST['telefono'] ?? '');
            $correo    = trim($_POST['correo'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');

            try {
                if (!preg_match('/^([0-9]{8}|[0-9]{11})$/', $documento)) {
                    throw new Exception("El documento debe ser un DNI de 8 dígitos o un RUC de 11 dígitos.");
                }
                if ($nombre === '' || $apellido === '') {
                    throw new Exception("El nombre/razón social y apellido/representante son obligatorios.");
                }
                if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("El formato del correo electrónico es inválido.");
                }
                if ($clienteModel->existeDocumento($documento, $id ?: null)) {
                    throw new Exception("El DNI/RUC «{$documento}» ya se encuentra registrado en otro cliente.");
                }

                $datosLimpios = [
                    'documento' => $documento,
                    'nombre'    => $nombre,
                    'apellido'  => $apellido,
                    'telefono'  => $telefono,
                    'correo'    => $correo,
                    'direccion' => $direccion
                ];

                if ($id) {
                    $clienteModel->actualizar($id, $datosLimpios);
                    $_SESSION['alerta_cliente'] = ['tipo' => 'success', 'titulo' => '¡Actualizado!', 'texto' => 'Los datos del cliente fueron actualizados.'];
                } else {
                    $clienteModel->guardar($datosLimpios);
                    $_SESSION['alerta_cliente'] = ['tipo' => 'success', 'titulo' => '¡Registrado!', 'texto' => 'Cliente registrado correctamente en el directorio.'];
                }
            } catch (PDOException $e) {
                $msg = ($e->getCode() == 23000)
                    ? "Ya existe un cliente registrado con ese DNI o RUC."
                    : "Error en base de datos: " . $e->getMessage();
                $_SESSION['alerta_cliente'] = ['tipo' => 'error', 'titulo' => 'No se pudo guardar', 'texto' => $msg];
            } catch (Exception $e) {
                $_SESSION['alerta_cliente'] = ['tipo' => 'warning', 'titulo' => 'Atención', 'texto' => $e->getMessage()];
            }

            header("Location: index.php?action=clientes");
            exit();
        }

        if (isset($_GET['eliminar'])) {
            if ($rolUsuario !== 'Administrador') {
                $_SESSION['alerta_cliente'] = ['tipo' => 'error', 'titulo' => 'Acceso denegado', 'texto' => 'Solo un Administrador puede eliminar clientes.'];
                header("Location: index.php?action=clientes");
                exit();
            }

            $idCliente = $_GET['eliminar'];
            $contratos = $clienteModel->contarContratosAsociados($idCliente);

            if ($contratos > 0) {
                $_SESSION['alerta_cliente'] = [
                    'tipo'   => 'warning',
                    'titulo' => 'Cliente con historial',
                    'texto'  => "No se puede eliminar porque tiene {$contratos} contrato(s) de alquiler registrado(s)."
                ];
            } else {
                try {
                    $clienteModel->eliminar($idCliente);
                    $_SESSION['alerta_cliente'] = ['tipo' => 'success', 'titulo' => 'Eliminado', 'texto' => 'Cliente eliminado correctamente.'];
                } catch (PDOException $e) {
                    $_SESSION['alerta_cliente'] = ['tipo' => 'error', 'titulo' => 'Error', 'texto' => 'No se pudo eliminar el cliente porque tiene contratos asociados.'];
                }
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