<!-- Tarjetas de Resumen de Control de Vencimientos -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-start border-secondary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Alertas Pendientes</div>
                <div class="fs-3 fw-bold text-dark"><?= (int)($resumen['pendientes'] ?? 0) ?></div>
                <span class="badge bg-light text-secondary border">Requieren seguimiento</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-danger border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Contratos Vencidos</div>
                <div class="fs-3 fw-bold text-danger"><?= (int)($resumen['vencidos'] ?? 0) ?></div>
                <span class="badge bg-danger-subtle text-danger">Fuera de fecha límite</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-warning border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Próximos a Vencer (≤ 2 días)</div>
                <div class="fs-3 fw-bold text-warning-emphasis"><?= (int)($resumen['por_vencer'] ?? 0) ?></div>
                <span class="badge bg-warning-subtle text-warning-emphasis">Aviso preventivo</span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span>Alertas y Control de Vencimientos</span>
            <span class="badge bg-warning text-dark">Monitoreo Automático</span>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <!-- Filtros rápidos -->
            <div class="btn-group btn-group-sm" role="group">
                <a href="index.php?action=alertas&filtro=todas" class="btn btn-<?= $filtro === 'todas' ? 'light' : 'outline-light' ?>">Todas</a>
                <a href="index.php?action=alertas&filtro=pendientes" class="btn btn-<?= $filtro === 'pendientes' ? 'light' : 'outline-light' ?>">Pendientes</a>
                <a href="index.php?action=alertas&filtro=vencidos" class="btn btn-<?= $filtro === 'vencidos' ? 'light' : 'outline-light' ?>">Vencidos</a>
            </div>

            <?php if (($resumen['pendientes'] ?? 0) > 0): ?>
                <a href="index.php?action=alertas&marcar_todas=1" class="btn btn-sm btn-success" onclick="return confirm('¿Marcar todas las alertas pendientes como atendidas?');">
                    Marcar todas atendidas
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tipo</th>
                    <th>Contrato</th>
                    <th>Detalle del Aviso</th>
                    <th>Fecha Límite</th>
                    <th>Contacto Cliente</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($notificaciones)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay alertas registradas para este filtro.</td></tr>
                <?php endif; ?>

                <?php foreach ($notificaciones as $n): 
                    $eval = ValidadorFechasContrato::evaluarVencimiento($n['fecha_fin_prevista']);
                    $telLimpio = preg_replace('/[^0-9]/', '', $n['telefono'] ?? '');
                    $textoWsp = urlencode("Hola {$n['nombre']}, te recordamos que el contrato de alquiler {$n['numero_contrato']} tiene fecha límite {$n['fecha_fin_prevista']}. Por favor coordina la devolución o renovación del equipo.");
                ?>
                <tr class="<?= ($n['estado'] === 'Pendiente' && $n['tipo'] === 'Contrato Vencido') ? 'table-danger' : '' ?>">
                    <td>
                        <span class="badge bg-<?= $n['tipo'] === 'Contrato Vencido' ? 'danger' : 'warning text-dark' ?>">
                            <?= htmlspecialchars($n['tipo']) ?>
                        </span>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($n['numero_contrato']) ?></strong>
                        <div class="small text-muted"><?= htmlspecialchars($n['nombre'] . ' ' . $n['apellido']) ?></div>
                    </td>
                    <td>
                        <div><?= htmlspecialchars($n['mensaje']) ?></div>
                        <?php if ($n['estado_contrato'] === 'Finalizado' || $n['estado_contrato'] === 'Devuelto'): ?>
                            <span class="badge bg-info text-dark mt-1">Equipo ya devuelto</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="fw-semibold"><?= htmlspecialchars($n['fecha_fin_prevista']) ?></span>
                        <?php if ($eval['vencido']): ?>
                            <div class="small text-danger fw-bold">Atraso: <?= $eval['dias_atraso'] ?> día(s)</div>
                        <?php elseif ($eval['dias_restantes'] === 0): ?>
                            <div class="small text-warning-emphasis fw-bold">Vence hoy</div>
                        <?php else: ?>
                            <div class="small text-muted">Faltan <?= $eval['dias_restantes'] ?> día(s)</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><?= htmlspecialchars($n['telefono']) ?></div>
                        <?php if (!empty($telLimpio)): ?>
                            <a href="https://wa.me<?= $telLimpio ?>?text=<?= $textoWsp ?>" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2 mt-1" title="Enviar recordatorio por WhatsApp">
                                WhatsApp
                            </a>
                        <?php endif; ?>
                    </td>
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
