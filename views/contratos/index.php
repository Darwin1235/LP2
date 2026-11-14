
<?php
// Vista del modulo contratos; usa los datos proporcionados por ContratoController.
$esc = static function ($texto): string {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
};
?>
<h3 class="mb-4">Contratos de alquiler</h3>

<?php if ($alerta): ?>
    <div class="alert alert-<?= $alerta['ok'] ? 'success' : 'danger' ?>" role="alert">
        <?= $esc($alerta['mensaje']) ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Nuevo contrato</div>
            <div class="card-body">
                <form method="post" action="index.php?action=contratos">
                    <input type="hidden" name="csrf_token"
                           value="<?= $esc($_SESSION['csrf_contratos']) ?>">

                    <div class="mb-3">
                        <label for="id_cliente" class="form-label">Cliente</label>
                        <select id="id_cliente" name="id_cliente" class="form-select" required>
                            <option value="">Seleccione un cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $esc($cliente['id_cliente']) ?>">
                                    <?= $esc($cliente['documento'] . ' - ' .
                                        $cliente['nombre'] . ' ' . $cliente['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Maquinas disponibles</label>
                        <p class="text-muted small mb-2">Marca una o varias maquinas.</p>
                        <div class="border rounded p-2" style="max-height:240px;overflow-y:auto">
                            <?php if (empty($equiposDisponibles)): ?>
                                <p class="text-muted small mb-0">No hay maquinas disponibles.</p>
                            <?php else: ?>
                                <?php foreach ($equiposDisponibles as $equipo): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox"
                                               name="id_equipos[]"
                                               value="<?= $esc($equipo['id_equipo']) ?>"
                                               id="equipo_<?= $esc($equipo['id_equipo']) ?>">
                                        <label class="form-check-label"
                                               for="equipo_<?= $esc($equipo['id_equipo']) ?>">
                                            <?= $esc($equipo['codigo'] . ' - ' . $equipo['nombre']) ?>
                                            <br>
                                            <small class="text-muted">S/ <?= number_format((float) $equipo['precio_alquiler'], 2) ?> por dia</small>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio"
                               class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin_prevista" class="form-label">Fecha prevista de devolucion</label>
                        <input type="date" id="fecha_fin_prevista" name="fecha_fin_prevista"
                               class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"
                            <?= empty($equiposDisponibles) ? 'disabled' : '' ?>>
                        Registrar contrato
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Contratos registrados</div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Numero</th>
                            <th>Cliente</th>
                            <th>Maquinas</th>
                            <th>Periodo</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($contratos)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Sin contratos registrados</td></tr>
                    <?php else: ?>
                        <?php foreach ($contratos as $contrato): ?>
                            <tr>
                                <td><strong><?= $esc($contrato['numero_contrato']) ?></strong></td>
                                <td><?= $esc($contrato['cli_nombre'] . ' ' . $contrato['cli_apellido']) ?></td>
                                <td><?= $esc($contrato['equipos'] ?? '-') ?></td>
                                <td><?= $esc($contrato['fecha_inicio']) ?> al
                                    <?= $esc($contrato['fecha_fin_prevista']) ?></td>
                                <td>S/ <?= number_format((float) $contrato['importe_total'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $contrato['estado'] === 'Devuelto' ? 'success' : ($contrato['estado'] === 'Vencido' ? 'danger' : 'primary') ?>">
                                        <?= $esc($contrato['estado']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
