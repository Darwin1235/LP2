<?php
require_once __DIR__ . '/../config/Conexion.php';

class Equipo {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
        $this->asegurarTablaMantenimiento();
    }

    private function asegurarTablaMantenimiento() {
        $sql = "CREATE TABLE IF NOT EXISTS mantenimiento_equipo (
            id_mantenimiento INT AUTO_INCREMENT PRIMARY KEY,
            id_equipo INT NOT NULL,
            fecha_ingreso DATE NOT NULL,
            fecha_salida DATE NULL,
            tipo_mantenimiento VARCHAR(80) NOT NULL,
            descripcion TEXT NOT NULL,
            costo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            estado_mantenimiento ENUM('En Taller', 'Finalizado') NOT NULL DEFAULT 'En Taller',
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_equipo) REFERENCES equipo(id_equipo) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->pdo->exec($sql);
    }

    public function listar() {
        $sql = "SELECT e.*, c.nombre AS categoria,
                       (SELECT COUNT(*) FROM mantenimiento_equipo m WHERE m.id_equipo = e.id_equipo) AS total_mantenimientos
                FROM equipo e 
                INNER JOIN categoria_equipo c ON e.id_categoria = c.id_categoria 
                ORDER BY e.id_equipo DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function listarDisponibles() {
        $sql = "SELECT e.*, c.nombre AS categoria 
                FROM equipo e 
                INNER JOIN categoria_equipo c ON e.id_categoria = c.id_categoria 
                WHERE e.estado_operativo = 'Disponible'";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM equipo WHERE id_equipo = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function existeCodigo($codigo, $idExcluir = null) {
        if ($idExcluir) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM equipo WHERE codigo = ? AND id_equipo != ?");
            $stmt->execute([$codigo, $idExcluir]);
        } else {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM equipo WHERE codigo = ?");
            $stmt->execute([$codigo]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }

    public function guardar($datos) {
        $sql = "INSERT INTO equipo (id_categoria, codigo, nombre, descripcion, marca, modelo, precio_alquiler, estado_operativo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['id_categoria'], $datos['codigo'], $datos['nombre'],
            $datos['descripcion'], $datos['marca'], $datos['modelo'],
            $datos['precio_alquiler'], $datos['estado_operativo']
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE equipo SET id_categoria=?, codigo=?, nombre=?, descripcion=?, marca=?, modelo=?, precio_alquiler=?, estado_operativo=? 
                WHERE id_equipo=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['id_categoria'], $datos['codigo'], $datos['nombre'],
            $datos['descripcion'], $datos['marca'], $datos['modelo'],
            $datos['precio_alquiler'], $datos['estado_operativo'], $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM equipo WHERE id_equipo = ?");
        return $stmt->execute([$id]);
    }

    /* ==========================================================
       HISTORIAL TÉCNICO DE MANTENIMIENTOS POR EQUIPO
       ========================================================== */
    public function listarMantenimientos() {
        $sql = "SELECT m.*, e.codigo, e.nombre AS equipo_nombre
                FROM mantenimiento_equipo m
                INNER JOIN equipo e ON m.id_equipo = e.id_equipo
                ORDER BY m.fecha_ingreso DESC, m.id_mantenimiento DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function registrarMantenimiento($datos) {
        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO mantenimiento_equipo
                    (id_equipo, fecha_ingreso, fecha_salida, tipo_mantenimiento, descripcion, costo, estado_mantenimiento)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $datos['id_equipo'],
                $datos['fecha_ingreso'],
                $datos['fecha_salida'] ?: null,
                $datos['tipo_mantenimiento'],
                $datos['descripcion'],
                $datos['costo'],
                $datos['estado_mantenimiento']
            ]);

            if ($datos['estado_mantenimiento'] === 'En Taller') {
                $stmtEq = $this->pdo->prepare("UPDATE equipo SET estado_operativo = 'Mantenimiento' WHERE id_equipo = ?");
                $stmtEq->execute([$datos['id_equipo']]);
            } else {
                $estadoRestaurado = $this->determinarEstadoTrasTaller($datos['id_equipo']);
                $stmtEq = $this->pdo->prepare("UPDATE equipo SET estado_operativo = ? WHERE id_equipo = ?");
                $stmtEq->execute([$estadoRestaurado, $datos['id_equipo']]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function finalizarMantenimiento($idMantenimiento) {
        $this->pdo->beginTransaction();
        try {
            $stmtGet = $this->pdo->prepare("SELECT id_equipo FROM mantenimiento_equipo WHERE id_mantenimiento = ?");
            $stmtGet->execute([$idMantenimiento]);
            $idEquipo = $stmtGet->fetchColumn();

            if ($idEquipo) {
                $stmtUpd = $this->pdo->prepare("UPDATE mantenimiento_equipo SET estado_mantenimiento = 'Finalizado', fecha_salida = CURDATE() WHERE id_mantenimiento = ?");
                $stmtUpd->execute([$idMantenimiento]);

                $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM mantenimiento_equipo WHERE id_equipo = ? AND estado_mantenimiento = 'En Taller'");
                $stmtCheck->execute([$idEquipo]);

                if ((int)$stmtCheck->fetchColumn() === 0) {
                    $estadoRestaurado = $this->determinarEstadoTrasTaller($idEquipo);
                    $stmtEq = $this->pdo->prepare("UPDATE equipo SET estado_operativo = ? WHERE id_equipo = ?");
                    $stmtEq->execute([$estadoRestaurado, $idEquipo]);
                }
            }
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function determinarEstadoTrasTaller($idEquipo) {
        $sql = "SELECT COUNT(*) 
                FROM detalle_contrato dc
                INNER JOIN contrato_alquiler ca ON dc.id_contrato = ca.id_contrato
                LEFT JOIN devolucion d ON dc.id_detalle = d.id_detalle
                WHERE dc.id_equipo = ? 
                  AND ca.estado IN ('Activo', 'Vencido') 
                  AND d.id_devolucion IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idEquipo]);
        return ((int)$stmt->fetchColumn() > 0) ? 'Ocupado' : 'Disponible';
    }

    public function eliminarMantenimiento($idMantenimiento) {
        $stmt = $this->pdo->prepare("DELETE FROM mantenimiento_equipo WHERE id_mantenimiento = ?");
        return $stmt->execute([$idMantenimiento]);
    }
}