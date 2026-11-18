<div class="card shadow-sm">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span>Alertas y Notificaciones de Vencimiento</span>
        <span class="badge bg-warning text-dark">Monitoreo Automático</span>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tipo</th>
                    <th>Contrato</th>
                    <th>Detalle del Aviso</th>
                    <th>Teléfono Cliente</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($notificaciones)): ?>
                    <tr><td colspan="6" class="text-center text-muted">No hay alertas de vencimiento activas.</td></tr>
                <?php endif; ?>
                <?php foreach ($notificaciones as $n): ?>
                <tr>
                    <td>
                        <span class="badge bg-<?= $n['tipo'] === 'Contrato Vencido' ? 'danger' : 'warning text-dark' ?>">
                            <?= htmlspecialchars($n['tipo']) ?>
                        </span>
                    </td>
                    <td><strong><?= htmlspecialchars($n['numero_contrato']) ?></strong></td>
                    <td><?= htmlspecialchars($n['mensaje']) ?></td>
                    <td><?= htmlspecialchars($n['telefono']) ?></td>
                    <td>
                        <span class="badge bg-<?= $n['estado'] === 'Pendiente' ? 'secondary' : 'success' ?>">
                            <?= htmlspecialchars($n['estado']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($n['estado'] === 'Pendiente'): ?>
                            <a href="index.php?action=alertas&leida=<?= $n['id_notificacion'] ?>" class="btn btn-sm btn-outline-success">Marcar Atendida</a>
                        <?php else: ?>
                            <span class="text-muted small">Atendida</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>