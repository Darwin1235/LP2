<?php if ($alerta): ?>
    <div class="alert alert-<?= $alerta['ok'] ? 'success' : 'danger' ?>">
        <?= htmlspecialchars($alerta['mensaje']) ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Nuevo Contrato de Alquiler</div>
            <div class="card-body">
                <form method="POST" action="index.php?action=contratos">
                    <div class="mb-3">
                        <label>Cliente</label>
                        <select name="id_cliente" class="form-select" required>
                            <option value="">-- Seleccione Cliente --</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id_cliente'] ?>">
                                    <?= htmlspecialchars($c['documento'] . ' - ' . $c['nombre'] . ' ' . $c['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Maquinaria Disponible</label>
                        <select name="id_equipo" class="form-select" required>
                            <option value="">-- Seleccione Equipo --</option>
                            <?php foreach ($equiposDisponibles as $eq): ?>
                                <option value="<?= $eq['id_equipo'] ?>">
                                    <?= htmlspecialchars($eq['codigo'] . ' - ' . $eq['nombre'] . ' (S/ ' . $eq['precio_alquiler'] . '/día)') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Fecha Fin Prevista</label>
                        <input type="date" name="fecha_fin_prevista" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Generar Contrato</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Contratos Registrados</div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>N° Contrato</th>
                            <th>Cliente</th>
                            <th>Equipo</th>
                            <th>Periodo</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contratos as $ctr): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ctr['numero_contrato']) ?></strong></td>
                            <td><?= htmlspecialchars($ctr['cli_nombre'] . ' ' . $ctr['cli_apellido']) ?></td>
                            <td><?= htmlspecialchars($ctr['eq_codigo'] . ' - ' . $ctr['eq_nombre']) ?></td>
                            <td><?= $ctr['fecha_inicio'] ?> al <?= $ctr['fecha_fin_prevista'] ?><br><small class="text-muted">(<?= $ctr['cantidad_dias'] ?> días)</small></td>
                            <td><strong>S/ <?= number_format($ctr['importe_total'], 2) ?></strong></td>
                            <td>
                                <span class="badge bg-<?= $ctr['estado'] === 'Activo' ? 'primary' : ($ctr['estado'] === 'Devuelto' ? 'success' : 'danger') ?>">
                                    <?= $ctr['estado'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>