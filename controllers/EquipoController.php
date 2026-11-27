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
        $rolUsuario = $_SESSION['usuario']['rol'] ?? 'Encargado';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subaccion = $_POST['subaccion'] ?? '';
            try {
                if ($subaccion === 'guardar_categoria') {
                    // Homologación de seguridad: solo Administrador crea categorías
                    if ($rolUsuario !== 'Administrador') {
                        throw new Exception("Solo un Administrador puede registrar nuevas categorías.");
                    }
                    $nombreCat = trim($_POST['nombre_cat'] ?? '');
                    if ($nombreCat === '') {
                        throw new Exception("El nombre de la categoría es obligatorio.");
                    }
                    if ($categoriaModel->existeNombre($nombreCat)) {
                        throw new Exception("La categoría «{$nombreCat}» ya existe en el sistema.");
                    }
                    $categoriaModel->guardar($nombreCat, trim($_POST['desc_cat'] ?? ''));
                    $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'titulo' => '¡Registrado!', 'texto' => 'Categoría agregada correctamente.'];

                } elseif ($subaccion === 'guardar_equipo') {
                    $id = $_POST['id_equipo'] ?? '';
                    $idCategoria = $_POST['id_categoria'] ?? '';
                    $codigo = strtoupper(trim($_POST['codigo'] ?? ''));
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
                    if ($equipoModel->existeCodigo($codigo, $id ?: null)) {
                        throw new Exception("El código «{$codigo}» ya pertenece a otro equipo en el inventario.");
                    }

                    $_POST['codigo'] = $codigo;
                    $_POST['nombre'] = $nombre;

                    if ($id) {
                        $equipoModel->actualizar($id, $_POST);
                        $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'titulo' => '¡Actualizado!', 'texto' => 'Los datos del equipo fueron actualizados.'];
                    } else {
                        $equipoModel->guardar($_POST);
                        $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'titulo' => '¡Registrado!', 'texto' => 'Equipo agregado al inventario correctamente.'];
                    }

                } elseif ($subaccion === 'guardar_mantenimiento') {
                    $idEquipo = $_POST['id_equipo_mant'] ?? '';
                    $fechaIngreso = $_POST['fecha_ingreso'] ?? '';
                    $fechaSalida = $_POST['fecha_salida'] ?? '';
                    $tipoMant = trim($_POST['tipo_mantenimiento'] ?? '');
                    $descMant = trim($_POST['descripcion_mant'] ?? '');
                    $costoMant = floatval($_POST['costo_mant'] ?? 0);
                    $estadoMant = $_POST['estado_mantenimiento'] ?? 'En Taller';

                    if ($idEquipo === '' || $fechaIngreso === '' || $tipoMant === '' || $descMant === '') {
                        throw new Exception("Complete todos los campos obligatorios del mantenimiento.");
                    }
                    if ($costoMant < 0) {
                        throw new Exception("El costo de mantenimiento no puede ser negativo.");
                    }
                    if ($fechaSalida !== '' && $fechaSalida < $fechaIngreso) {
                        throw new Exception("La fecha de salida no puede ser anterior a la fecha de ingreso.");
                    }

                    $equipoModel->registrarMantenimiento([
                        'id_equipo' => $idEquipo,
                        'fecha_ingreso' => $fechaIngreso,
                        'fecha_salida' => $fechaSalida,
                        'tipo_mantenimiento' => $tipoMant,
                        'descripcion' => $descMant,
                        'costo' => $costoMant,
                        'estado_mantenimiento' => $estadoMant
                    ]);

                    $_SESSION['alerta_equipo'] = [
                        'tipo' => 'success',
                        'titulo' => 'Mantenimiento registrado',
                        'texto' => 'El historial técnico y el estado del equipo fueron actualizados.'
                    ];
                }
            } catch (PDOException $e) {
                $msg = ($e->getCode() == 23000)
                    ? "El código de equipo ingresado ya existe en el inventario."
                    : "Error en base de datos: " . $e->getMessage();
                $_SESSION['alerta_equipo'] = ['tipo' => 'error', 'titulo' => 'No se pudo guardar', 'texto' => $msg];
            } catch (Exception $e) {
                $_SESSION['alerta_equipo'] = ['tipo' => 'warning', 'titulo' => 'Atención', 'texto' => $e->getMessage()];
            }

            header("Location: index.php?action=equipos");
            exit();
        }

        if (isset($_GET['finalizar_mant'])) {
            $equipoModel->finalizarMantenimiento($_GET['finalizar_mant']);
            $_SESSION['alerta_equipo'] = [
                'tipo' => 'success',
                'titulo' => 'Salida de taller',
                'texto' => 'Mantenimiento finalizado y equipo marcado como Disponible.'
            ];
            header("Location: index.php?action=equipos");
            exit();
        }

        if (isset($_GET['eliminar_mant'])) {
            if ($rolUsuario !== 'Administrador') {
                $_SESSION['alerta_equipo'] = ['tipo' => 'error', 'titulo' => 'Acceso denegado', 'texto' => 'Solo un Administrador puede eliminar registros de mantenimiento.'];
                header("Location: index.php?action=equipos");
                exit();
            }
            $equipoModel->eliminarMantenimiento($_GET['eliminar_mant']);
            $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'titulo' => 'Eliminado', 'texto' => 'Registro de mantenimiento eliminado.'];
            header("Location: index.php?action=equipos");
            exit();
        }

        if (isset($_GET['eliminar'])) {
            if ($rolUsuario !== 'Administrador') {
                $_SESSION['alerta_equipo'] = ['tipo' => 'error', 'titulo' => 'Acceso denegado', 'texto' => 'Solo un Administrador puede eliminar equipos.'];
                header("Location: index.php?action=equipos");
                exit();
            }

            try {
                $equipoModel->eliminar($_GET['eliminar']);
                $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'titulo' => 'Eliminado', 'texto' => 'Equipo eliminado del inventario.'];
            } catch (PDOException $e) {
                $_SESSION['alerta_equipo'] = ['tipo' => 'error', 'titulo' => 'No se puede eliminar', 'texto' => 'Este equipo tiene historial de contratos de alquiler asociados.'];
            }
            header("Location: index.php?action=equipos");
            exit();
        }

        if (isset($_GET['eliminar_cat'])) {
            if ($rolUsuario !== 'Administrador') {
                $_SESSION['alerta_equipo'] = ['tipo' => 'error', 'titulo' => 'Acceso denegado', 'texto' => 'Solo un Administrador puede eliminar categorías.'];
                header("Location: index.php?action=equipos");
                exit();
            }

            $idCat = $_GET['eliminar_cat'];
            $asociados = $categoriaModel->contarEquiposAsociados($idCat);

            if ($asociados > 0) {
                $_SESSION['alerta_equipo'] = [
                    'tipo'   => 'warning',
                    'titulo' => 'Categoría en uso',
                    'texto'  => "No se puede eliminar porque tiene {$asociados} equipo(s) asociado(s)."
                ];
            } else {
                try {
                    $categoriaModel->eliminar($idCat);
                    $_SESSION['alerta_equipo'] = ['tipo' => 'success', 'titulo' => 'Eliminada', 'texto' => 'Categoría eliminada correctamente.'];
                } catch (PDOException $e) {
                    $_SESSION['alerta_equipo'] = ['tipo' => 'error', 'titulo' => 'Error', 'texto' => 'No se pudo eliminar la categoría.'];
                }
            }
            header("Location: index.php?action=equipos");
            exit();
        }

        $alerta = $_SESSION['alerta_equipo'] ?? null;
        unset($_SESSION['alerta_equipo']);

        $equipoEditar = isset($_GET['editar']) ? $equipoModel->obtenerPorId($_GET['editar']) : null;
        $equipos = $equipoModel->listar();
        $categorias = $categoriaModel->listar();
        $mantenimientos = $equipoModel->listarMantenimientos();

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/equipos/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}