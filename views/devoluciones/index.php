<?php if (!empty($alerta)): ?>
    <div class="alert alert-<?= $alerta['tipo'] ?> alert-dismissible fade show">
        <?= htmlspecialchars($alerta['texto']) ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">Contratos Pendientes de Devolución</div>
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Contrato</th>
                    <th>Cliente</th>
                    <th>Maquinaria</th>
                    <th>Fecha Límite</th>
                    <th>Registrar Recepción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendientes)): ?>
                    <tr><td colspan="5" class="text-center text-muted">No hay equipos pendientes de devolución.</td></tr>
                <?php endif; ?>
                <?php foreach ($pendientes as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['numero_contrato']) ?></strong></td>
                    <td><?= htmlspecialchars($p['cli_nombre'] . ' ' . $p['cli_apellido']) ?></td>
                    <td><?= htmlspecialchars($p['codigo'] . ' - ' . $p['eq_nombre']) ?></td>
                    <td><?= $p['fecha_fin_prevista'] ?></td>
                    <td>
                        <form method="POST" action="index.php?action=devoluciones" class="d-flex gap-2">
                            <input type="hidden" name="id_detalle" value="<?= $p['id_detalle'] ?>">
                            <input type="hidden" name="id_contrato" value="<?= $p['id_contrato'] ?>">
                            <input type="hidden" name="id_equipo" value="<?= $p['id_equipo'] ?>">
                            <select name="condicion_equipo" class="form-select form-select-sm" required>
                                <option value="Operativo / Buen Estado">Buen Estado (Disponible)</option>
                                <option value="Requiere Mantenimiento">Con Daños (Mantenimiento)</option>
                            </select>
                            <input type="text" name="observacion" class="form-control form-control-sm" placeholder="Observación">
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Confirmar recepción del equipo?')">Recibir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">Historial de Devoluciones Realizadas</div>
    <div class="card-body table-responsive">
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>Contrato</th>
                    <th>Equipo</th>
                    <th>Cliente</th>
                    <th>Fecha Devolución</th>
                    <th>Condición</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historial)): ?>
                    <tr><td colspan="6" class="text-center text-muted">Aún no se han registrado devoluciones.</td></tr>
                <?php endif; ?>
                <?php foreach ($historial as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($h['numero_contrato']) ?></td>
                    <td><?= htmlspecialchars($h['codigo'] . ' - ' . $h['eq_nombre']) ?></td>
                    <td><?= htmlspecialchars($h['cli_nombre'] . ' ' . $h['cli_apellido']) ?></td>
                    <td><?= $h['fecha_devolucion'] ?></td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($h['condicion_equipo']) ?></span></td>
                    <td><?= htmlspecialchars($h['observacion']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>