<?php
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/CategoriaEquipo.php';

class EquipoController {
    public function index() {
        $equipoModel = new Equipo();
        $categoriaModel = new CategoriaEquipo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subaccion = $_POST['subaccion'] ?? '';
            if ($subaccion === 'guardar_categoria') {
                $categoriaModel->guardar(trim($_POST['nombre_cat']), trim($_POST['desc_cat']));
            } elseif ($subaccion === 'guardar_equipo') {
                $id = $_POST['id_equipo'] ?? '';
                if ($id) {
                    $equipoModel->actualizar($id, $_POST);
                } else {
                    $equipoModel->guardar($_POST);
                }
            }
            header("Location: index.php?action=equipos");
            exit();
        }

        if (isset($_GET['eliminar'])) {
            $equipoModel->eliminar($_GET['eliminar']);
            header("Location: index.php?action=equipos");
            exit();
        }

        if (isset($_GET['eliminar_cat'])) {
            $categoriaModel->eliminar($_GET['eliminar_cat']);
            header("Location: index.php?action=equipos");
            exit();
        }

        $equipoEditar = isset($_GET['editar']) ? $equipoModel->obtenerPorId($_GET['editar']) : null;
        $equipos = $equipoModel->listar();
        $categorias = $categoriaModel->listar();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/equipos/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}