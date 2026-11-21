<?php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/ValidadorFechasContrato.php';

class Notificacion {
    private $pdo;
    public function __construct() { $this->pdo = Conexion::conectar(); }

    public function sincronizarAlertasVencimiento() {
        $sql = "SELECT ca.id_contrato, ca.numero_contrato, ca.fecha_fin_prevista, c.nombre, c.apellido
                FROM contrato_alquiler ca
                INNER JOIN cliente c ON ca.id_cliente = c.id_cliente
                WHERE ca.estado IN ('Activo', 'Vencido')
                  AND ca.fecha_fin_prevista <= DATE_ADD(CURDATE(), INTERVAL 2 DAY)";
        $contratos = $this->pdo->query($sql)->fetchAll();

        foreach ($contratos as $ctr) {
            $eval = ValidadorFechasContrato::evaluarVencimiento($ctr['fecha_fin_prevista']);
            $esVencido = $eval['vencido'];

            if ($esVencido) {
                $this->pdo->prepare("UPDATE contrato_alquiler SET estado = 'Vencido' WHERE id_contrato = ? AND estado = 'Activo'")
                          ->execute([$ctr['id_contrato']]);
            }

            $tipo = $esVencido ? 'Contrato Vencido' : 'Próximo a Vencer';

            if ($esVencido) {
                $detalleTiempo = "lleva {$eval['dias_atraso']} día(s) de atraso (venció el {$ctr['fecha_fin_prevista']})";
            } elseif ($eval['dias_restantes'] === 0) {
                $detalleTiempo = "vence HOY ({$ctr['fecha_fin_prevista']})";
            } else {
                $detalleTiempo = "vence en {$eval['dias_restantes']} día(s) ({$ctr['fecha_fin_prevista']})";
            }

            $msg = "El contrato {$ctr['numero_contrato']} del cliente {$ctr['nombre']} {$ctr['apellido']} {$detalleTiempo}.";

            // Verificar si ya existe una notificación previa para este contrato y tipo (sin importar si está Leída o Pendiente)
            $chk = $this->pdo->prepare("SELECT id_notificacion, estado FROM notificacion WHERE id_contrato = ? AND tipo = ? LIMIT 1");
            $chk->execute([$ctr['id_contrato'], $tipo]);
            $existente = $chk->fetch();

            if ($existente) {
                // Actualizar el mensaje para mantener actualizado el conteo de días sin duplicar fila
                $upd = $this->pdo->prepare("UPDATE notificacion SET mensaje = ? WHERE id_notificacion = ?");
                $upd->execute([$msg, $existente['id_notificacion']]);
            } else {
                // Si antes tenía alerta 'Próximo a Vencer' y ahora venció, cerramos la preventiva para evitar ruido
                if ($esVencido) {
                    $this->pdo->prepare("UPDATE notificacion SET estado = 'Leido', fecha_envio = NOW() WHERE id_contrato = ? AND tipo = 'Próximo a Vencer' AND estado = 'Pendiente'")
                              ->execute([$ctr['id_contrato']]);
                }

                $ins = $this->pdo->prepare("INSERT INTO notificacion (id_contrato, tipo, mensaje, fecha_generacion, estado) VALUES (?, ?, ?, NOW(), 'Pendiente')");
                $ins->execute([$ctr['id_contrato'], $tipo, $msg]);
            }
        }
    }

    public function listar($filtro = 'todas') {
        $where = "";
        if ($filtro === 'pendientes') {
            $where = "WHERE n.estado = 'Pendiente'";
        } elseif ($filtro === 'vencidos') {
            $where = "WHERE n.tipo = 'Contrato Vencido'";
        }

        $sql = "SELECT n.*, ca.numero_contrato, ca.fecha_fin_prevista, ca.estado AS estado_contrato,
                       c.nombre, c.apellido, c.telefono
                FROM notificacion n
                INNER JOIN contrato_alquiler ca ON n.id_contrato = ca.id_contrato
                INNER JOIN cliente c ON ca.id_cliente = c.id_cliente
                $where
                ORDER BY FIELD(n.estado, 'Pendiente', 'Leido'), n.fecha_generacion DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerResumen() {
        $sql = "SELECT 
                    SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                    SUM(CASE WHEN tipo = 'Contrato Vencido' AND estado = 'Pendiente' THEN 1 ELSE 0 END) AS vencidos,
                    SUM(CASE WHEN tipo = 'Próximo a Vencer' AND estado = 'Pendiente' THEN 1 ELSE 0 END) AS por_vencer
                FROM notificacion";
        return $this->pdo->query($sql)->fetch() ?: ['pendientes' => 0, 'vencidos' => 0, 'por_vencer' => 0];
    }

    public function marcarLeida($id) {
        $stmt = $this->pdo->prepare("UPDATE notificacion SET estado = 'Leido', fecha_envio = NOW() WHERE id_notificacion = ?");
        return $stmt->execute([$id]);
    }

    public function marcarTodasLeidas() {
        $stmt = $this->pdo->prepare("UPDATE notificacion SET estado = 'Leido', fecha_envio = NOW() WHERE estado = 'Pendiente'");
        return $stmt->execute();
    }

    public function cerrarAlertasPorContrato($idContrato) {
        $stmt = $this->pdo->prepare("UPDATE notificacion SET estado = 'Leido', fecha_envio = NOW() WHERE id_contrato = ? AND estado = 'Pendiente'");
        return $stmt->execute([$idContrato]);
    }
}
