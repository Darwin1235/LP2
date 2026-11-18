<?php
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/CategoriaEquipo.php';

class EquipoController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $equipoModel = new Equipo();
        $categoriaModel = new CategoriaEquipo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subaccion = $_POST['subaccion'] ?? '';
            try {
                if ($subaccion === 'guardar_categoria') {
                    $nombreCat = trim($_POST['nombre_cat'] ?? '');
                    if ($nombreCat === '') {
                        throw new Exception("El nombre de la categoría es obligatorio.");
                    }
                    $categoriaModel->guardar($nombreCat, trim($_POST['desc_cat'] ?? ''));
                    $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'texto' => 'Categoría registrada correctamente.'];
                } elseif ($subaccion === 'guardar_equipo') {
                    $id = $_POST['id_equipo'] ?? '';
                    $idCategoria = $_POST['id_categoria'] ?? '';
                    $codigo = trim($_POST['codigo'] ?? '');
                    $nombre = trim($_POST['nombre'] ?? '');
                    $precio = floatval($_POST['precio_alquiler'] ?? 0);

                    if ($idCategoria === '') {
                        throw new Exception("Primero debe registrar y seleccionar una categoría.");
                    }
                    if ($codigo === '' || $nombre === '') {
                        throw new Exception("El código y el nombre del equipo son obligatorios.");
                    }
                    if ($precio <= 0) {
                        throw new Exception("La tarifa diaria debe ser mayor a S/ 0.00.");
                    }

                    if ($id) {
                        $equipoModel->actualizar($id, $_POST);
                        $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'texto' => 'Equipo actualizado correctamente.'];
                    } else {
                        $equipoModel->guardar($_POST);
                        $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'texto' => 'Equipo registrado correctamente.'];
                    }
                }
            } catch (PDOException $e) {
                $msg = ($e->getCode() == 23000)
                    ? "Error: El código de equipo ya existe en el inventario."
                    : "Error en base de datos: " . $e->getMessage();
                $_SESSION['alerta_equipo'] = ['tipo' => 'danger', 'texto' => $msg];
            } catch (Exception $e) {
                $_SESSION['alerta_equipo'] = ['tipo' => 'danger', 'texto' => $e->getMessage()];
            }

            header("Location: index.php?action=equipos");
            exit();
        }

        if (isset($_GET['eliminar'])) {
            try {
                $equipoModel->eliminar($_GET['eliminar']);
                $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'texto' => 'Equipo eliminado correctamente.'];
            } catch (PDOException $e) {
                $_SESSION['alerta_equipo'] = ['tipo' => 'danger', 'texto' => 'No se puede eliminar este equipo porque tiene contratos registrados.'];
            }
            header("Location: index.php?action=equipos");
            exit();
        }

        if (isset($_GET['eliminar_cat'])) {
            try {
                $categoriaModel->eliminar($_GET['eliminar_cat']);
                $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'texto' => 'Categoría eliminada correctamente.'];
            } catch (PDOException $e) {
                $_SESSION['alerta_equipo'] = ['tipo' => 'danger', 'texto' => 'No se puede eliminar la categoría porque tiene equipos asociados.'];
            }
            header("Location: index.php?action=equipos");
            exit();
        }

        $alerta = $_SESSION['alerta_equipo'] ?? null;
        unset($_SESSION['alerta_equipo']);

        $equipoEditar = isset($_GET['editar']) ? $equipoModel->obtenerPorId($_GET['editar']) : null;
        $equipos = $equipoModel->listar();
        $categorias = $categoriaModel->listar();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/equipos/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}