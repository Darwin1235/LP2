<?php
require_once __DIR__ . '/../config/Conexion.php';

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
            $esVencido = ($ctr['fecha_fin_prevista'] < date('Y-m-d'));
            if ($esVencido) {
                $this->pdo->prepare("UPDATE contrato_alquiler SET estado = 'Vencido' WHERE id_contrato = ?")->execute([$ctr['id_contrato']]);
            }

            $chk = $this->pdo->prepare("SELECT id_notificacion FROM notificacion WHERE id_contrato = ? AND estado = 'Pendiente'");
            $chk->execute([$ctr['id_contrato']]);
            if (!$chk->fetch()) {
                $tipo = $esVencido ? 'Contrato Vencido' : 'Próximo a Vencer';
                $msg = "El contrato {$ctr['numero_contrato']} del cliente {$ctr['nombre']} {$ctr['apellido']} tiene fecha límite {$ctr['fecha_fin_prevista']}.";
                $ins = $this->pdo->prepare("INSERT INTO notificacion (id_contrato, tipo, mensaje, fecha_generacion, estado) VALUES (?, ?, ?, NOW(), 'Pendiente')");
                $ins->execute([$ctr['id_contrato'], $tipo, $msg]);
            }
        }
    }

    public function listar() {
        $sql = "SELECT n.*, ca.numero_contrato, c.nombre, c.apellido, c.telefono
                FROM notificacion n
                INNER JOIN contrato_alquiler ca ON n.id_contrato = ca.id_contrato
                INNER JOIN cliente c ON ca.id_cliente = c.id_cliente
                ORDER BY n.fecha_generacion DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function marcarLeida($id) {
        $stmt = $this->pdo->prepare("UPDATE notificacion SET estado = 'Leido', fecha_envio = NOW() WHERE id_notificacion = ?");
        return $stmt->execute([$id]);
    }
}