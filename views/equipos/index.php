<?php
$esAdmin            = (($_SESSION['usuario']['rol'] ?? '') === 'Administrador');
$totalEquipos       = count($equipos);
$totalDisponibles   = count(array_filter($equipos, fn($e) => $e['estado_operativo'] === 'Disponible'));
$totalOcupados      = count(array_filter($equipos, fn($e) => $e['estado_operativo'] === 'Ocupado'));
$totalMantenimiento = count(array_filter($equipos, fn($e) => $e['estado_operativo'] === 'Mantenimiento'));
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-dark text-white">
                Registrar Nuevo Equipo
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=equipos">
                    <input type="hidden" name="subaccion" value="guardar_equipo">
                    <div class="mb-2">
                        <label>Categoría</label>
                        <select name="id_categoria" class="form-select" required>
                            <option value="">-- Seleccione Categoría --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_categoria'] ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Código</label>
                        <input type="text" name="codigo" class="form-control" required placeholder="EQ-001">
                    </div>
                    <div class="mb-2">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label>Marca</label>
                            <input type="text" name="marca" class="form-control">
                        </div>
                        <div class="col">
                            <label>Modelo</label>
                            <input type="text" name="modelo" class="form-control">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label>Precio por Día (S/)</label>
                        <input type="number" step="0.01" min="0.01" name="precio_alquiler" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Estado Operativo</label>
                        <select name="estado_operativo" class="form-select">
                            <?php foreach (['Disponible', 'Ocupado', 'Mantenimiento'] as $est): ?>
                                <option value="<?= $est ?>"><?= $est ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Guardar Equipo</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <span>Categorías de Equipo</span>
                <?php if (!$esAdmin): ?>
                    <span class="badge bg-light text-dark">Solo lectura</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($esAdmin): ?>
                    <form method="POST" action="index.php?action=equipos" class="mb-3">
                        <input type="hidden" name="subaccion" value="guardar_categoria">
                        <div class="mb-2">
                            <input type="text" name="nombre_cat" class="form-control form-control-sm" placeholder="Nueva categoría" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="desc_cat" class="form-control form-control-sm" placeholder="Descripción breve">
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm w-100">Agregar Categoría</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted small mb-2">Solo un Administrador puede crear o eliminar categorías.</p>
                <?php endif; ?>

                <ul class="list-group list-group-flush small">
                    <?php if (empty($categorias)): ?>
                        <li class="list-group-item text-muted px-0">Cree una categoría antes de registrar equipos.</li>
                    <?php endif; ?>
                    <?php foreach ($categorias as $c): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><strong><?= htmlspecialchars($c['nombre']) ?></strong></span>
                            <?php if ($esAdmin): ?>
                                <a href="index.php?action=equipos&eliminar_cat=<?= $c['id_categoria'] ?>"
                                   class="text-danger text-decoration-none fw-bold btn-confirmar-eliminar"
                                   data-mensaje="¿Deseas eliminar la categoría «<?= htmlspecialchars($c['nombre']) ?>»?">✕</a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Inventario + Exportación + Historial Mantenimiento -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>Inventario de Maquinaria</span>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" id="btnExportarExcel" class="btn btn-sm btn-success" title="Descargar inventario filtrado en Excel/CSV">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </button>
                    <button type="button" id="btnExportarPDF" class="btn btn-sm btn-danger" title="Generar reporte PDF imprimible">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </button>
                    <span class="badge bg-light text-dark" id="contadorMostrados">
                        Mostrando <?= $totalEquipos ?> de <?= $totalEquipos ?>
                    </span>
                </div>
            </div>

            <div class="card-body border-bottom bg-light py-3">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted">🔍</span>
                            <input type="text" id="buscadorEquipos" class="form-control border-start-0"
                                   placeholder="Buscar por código, nombre, marca o categoría...">
                            <button type="button" id="limpiarBusqueda" class="btn btn-outline-secondary d-none" title="Limpiar filtro">✕</button>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="d-flex flex-wrap gap-1 justify-content-lg-end" id="filtrosEstado">
                            <button type="button" class="btn btn-sm btn-dark filtro-estado active" data-estado="Todos">
                                Todos <span class="badge bg-secondary ms-1"><?= $totalEquipos ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success filtro-estado" data-estado="Disponible">
                                Disponible <span class="badge bg-success ms-1"><?= $totalDisponibles ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark filtro-estado" data-estado="Ocupado">
                                Ocupado <span class="badge bg-warning text-dark ms-1"><?= $totalOcupados ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger filtro-estado" data-estado="Mantenimiento">
                                Mantenimiento <span class="badge bg-danger ms-1"><?= $totalMantenimiento ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover align-middle" id="tablaEquipos">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Equipo</th>
                            <th>Categoría</th>
                            <th>Tarifa/Día</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipos)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Aún no hay equipos registrados en el inventario.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($equipos as $eq): ?>
                        <tr class="fila-equipo"
                            data-codigo="<?= htmlspecialchars($eq['codigo']) ?>"
                            data-nombre="<?= htmlspecialchars($eq['nombre']) ?>"
                            data-marca="<?= htmlspecialchars(trim($eq['marca'] . ' ' . $eq['modelo'])) ?>"
                            data-categoria="<?= htmlspecialchars($eq['categoria']) ?>"
                            data-tarifa="<?= number_format($eq['precio_alquiler'], 2, '.', '') ?>"
                            data-estado="<?= htmlspecialchars($eq['estado_operativo']) ?>"
                            data-busqueda="<?= htmlspecialchars(strtolower($eq['codigo'] . ' ' . $eq['nombre'] . ' ' . $eq['marca'] . ' ' . $eq['modelo'] . ' ' . $eq['categoria'])) ?>">
                            <td><strong><?= htmlspecialchars($eq['codigo']) ?></strong></td>
                            <td>
                                <?= htmlspecialchars($eq['nombre']) ?>
                                <br><small class="text-muted"><?= htmlspecialchars($eq['marca'] . ' ' . $eq['modelo']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($eq['categoria']) ?></td>
                            <td>S/ <?= number_format($eq['precio_alquiler'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $eq['estado_operativo'] === 'Disponible' ? 'success' : ($eq['estado_operativo'] === 'Ocupado' ? 'warning text-dark' : 'danger') ?>">
                                    <?= $eq['estado_operativo'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary btn-abrir-editar"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarEquipo"
                                            data-id="<?= $eq['id_equipo'] ?>"
                                            data-id-categoria="<?= $eq['id_categoria'] ?>"
                                            data-codigo="<?= htmlspecialchars($eq['codigo']) ?>"
                                            data-nombre="<?= htmlspecialchars($eq['nombre']) ?>"
                                            data-marca="<?= htmlspecialchars($eq['marca'] ?? '') ?>"
                                            data-modelo="<?= htmlspecialchars($eq['modelo'] ?? '') ?>"
                                            data-precio="<?= number_format($eq['precio_alquiler'], 2, '.', '') ?>"
                                            data-estado="<?= htmlspecialchars($eq['estado_operativo']) ?>"
                                            data-descripcion="<?= htmlspecialchars($eq['descripcion'] ?? '') ?>">
                                        Editar
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary btn-abrir-mant"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalMantenimiento"
                                            data-id="<?= $eq['id_equipo'] ?>"
                                            data-etiqueta="<?= htmlspecialchars($eq['codigo'] . ' - ' . $eq['nombre']) ?>"
                                            title="Registrar ingreso o historial en taller">
                                        Taller (<?= (int)($eq['total_mantenimientos'] ?? 0) ?>)
                                    </button>
                                    <?php if ($esAdmin): ?>
                                        <a href="index.php?action=equipos&eliminar=<?= $eq['id_equipo'] ?>"
                                           class="btn btn-sm btn-outline-danger btn-confirmar-eliminar"
                                           data-mensaje="Se eliminará el equipo «<?= htmlspecialchars($eq['codigo'] . ' - ' . $eq['nombre']) ?>» del inventario.">Eliminar</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr id="filaSinResultados" class="d-none">
                            <td colspan="6" class="text-center text-muted py-4">
                                No se encontraron equipos que coincidan con el filtro aplicado.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Panel de Historial Técnico de Mantenimientos -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>Historial Técnico de Mantenimientos</span>
                <button type="button" class="btn btn-sm btn-warning text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#modalMantenimiento">
                    + Registrar Mantenimiento
                </button>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Equipo</th>
                            <th>Tipo</th>
                            <th>Ingreso / Salida</th>
                            <th>Descripción</th>
                            <th>Costo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mantenimientos)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    No hay registros de mantenimiento técnico todavía.
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($mantenimientos as $m): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($m['codigo']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($m['equipo_nombre']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($m['tipo_mantenimiento']) ?></td>
                                <td class="small">
                                    Ing: <?= htmlspecialchars($m['fecha_ingreso']) ?><br>
                                    Sal: <?= $m['fecha_salida'] ? htmlspecialchars($m['fecha_salida']) : '<span class="text-muted">Pendiente</span>' ?>
                                </td>
                                <td class="small"><?= htmlspecialchars($m['descripcion']) ?></td>
                                <td>S/ <?= number_format($m['costo'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $m['estado_mantenimiento'] === 'En Taller' ? 'danger' : 'success' ?>">
                                        <?= htmlspecialchars($m['estado_mantenimiento']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if ($m['estado_mantenimiento'] === 'En Taller'): ?>
                                            <a href="index.php?action=equipos&finalizar_mant=<?= $m['id_mantenimiento'] ?>"
                                               class="btn btn-sm btn-outline-success"
                                               title="Dar salida de taller y marcar equipo Disponible">
                                                Finalizar
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($esAdmin): ?>
                                            <a href="index.php?action=equipos&eliminar_mant=<?= $m['id_mantenimiento'] ?>"
                                               class="btn btn-sm btn-outline-danger btn-confirmar-eliminar"
                                               data-mensaje="¿Eliminar este registro del historial de mantenimiento?">
                                                ✕
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Editar Equipo -->
<div class="modal fade" id="modalEditarEquipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="index.php?action=equipos" class="modal-content">
            <input type="hidden" name="subaccion" value="guardar_equipo">
            <input type="hidden" name="id_equipo" id="editIdEquipo">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6">Editar Equipo de Maquinaria</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Categoría</label>
                    <select name="id_categoria" id="editIdCategoria" class="form-select" required>
                        <option value="">-- Seleccione Categoría --</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id_categoria'] ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-5">
                        <label class="form-label small fw-semibold">Código</label>
                        <input type="text" name="codigo" id="editCodigo" class="form-control" required>
                    </div>
                    <div class="col-7">
                        <label class="form-label small fw-semibold">Nombre del Equipo</label>
                        <input type="text" name="nombre" id="editNombre" class="form-control" required>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Marca</label>
                        <input type="text" name="marca" id="editMarca" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Modelo</label>
                        <input type="text" name="modelo" id="editModelo" class="form-control">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Precio por Día (S/)</label>
                        <input type="number" step="0.01" min="0.01" name="precio_alquiler" id="editPrecio" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Estado Operativo</label>
                        <select name="estado_operativo" id="editEstado" class="form-select">
                            <?php foreach (['Disponible', 'Ocupado', 'Mantenimiento'] as $est): ?>
                                <option value="<?= $est ?>"><?= $est ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Descripción</label>
                    <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm">Actualizar Equipo</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Registrar Mantenimiento Técnico -->
<div class="modal fade" id="modalMantenimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="index.php?action=equipos" class="modal-content">
            <input type="hidden" name="subaccion" value="guardar_mantenimiento">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6">Registrar Mantenimiento de Maquinaria</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Maquinaria / Equipo</label>
                    <select name="id_equipo_mant" id="selectEquipoMant" class="form-select" required>
                        <option value="">-- Seleccione Equipo --</option>
                        <?php foreach ($equipos as $eq): ?>
                            <option value="<?= $eq['id_equipo'] ?>">
                                <?= htmlspecialchars($eq['codigo'] . ' - ' . $eq['nombre'] . ' (' . $eq['estado_operativo'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Fecha de Ingreso</label>
                        <input type="date" name="fecha_ingreso" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Fecha de Salida (Opcional)</label>
                        <input type="date" name="fecha_salida" class="form-control">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tipo de Mantenimiento</label>
                        <select name="tipo_mantenimiento" class="form-select" required>
                            <option value="Preventivo">Preventivo</option>
                            <option value="Correctivo">Correctivo</option>
                            <option value="Cambio de Aceite / Filtros">Cambio de Aceite / Filtros</option>
                            <option value="Revisión Hidráulica / Motor">Revisión Hidráulica / Motor</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Costo Estimado (S/)</label>
                        <input type="number" step="0.01" min="0" name="costo_mant" class="form-control" value="0.00" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Estado del Mantenimiento</label>
                    <select name="estado_mantenimiento" class="form-select" required>
                        <option value="En Taller">En Taller (cambia equipo a Mantenimiento)</option>
                        <option value="Finalizado">Finalizado (deja equipo Disponible)</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Detalle Técnico / Observación</label>
                    <textarea name="descripcion_mant" class="form-control" rows="2" required placeholder="Ej. Cambio de filtros hidráulicos, engrase general y calibración..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm">Guardar Registro</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if (!empty($alerta)): ?>
Swal.fire({
    icon: <?= json_encode($alerta['tipo']) ?>,
    title: <?= json_encode($alerta['titulo'] ?? 'Aviso') ?>,
    text: <?= json_encode($alerta['texto']) ?>,
    confirmButtonColor: '#0f172a'
});
<?php endif; ?>

// Cargar datos del equipo en el modal al pulsar "Editar"
document.querySelectorAll('.btn-abrir-editar').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('editIdEquipo').value = this.dataset.id || '';
        document.getElementById('editIdCategoria').value = this.dataset.idCategoria || '';
        document.getElementById('editCodigo').value = this.dataset.codigo || '';
        document.getElementById('editNombre').value = this.dataset.nombre || '';
        document.getElementById('editMarca').value = this.dataset.marca || '';
        document.getElementById('editModelo').value = this.dataset.modelo || '';
        document.getElementById('editPrecio').value = this.dataset.precio || '';
        document.getElementById('editEstado').value = this.dataset.estado || 'Disponible';
        document.getElementById('editDescripcion').value = this.dataset.descripcion || '';
    });
});

// Preseleccionar equipo al abrir modal desde el botón "Taller" de una fila
document.querySelectorAll('.btn-abrir-mant').forEach(btn => {
    btn.addEventListener('click', function () {
        const select = document.getElementById('selectEquipoMant');
        if (select) {
            select.value = this.dataset.id || '';
        }
    });
});

document.querySelectorAll('.btn-confirmar-eliminar').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.getAttribute('href');
        const mensaje = this.getAttribute('data-mensaje') || 'Esta acción no se puede deshacer.';

        Swal.fire({
            title: '¿Estás seguro?',
            text: mensaje,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});

(function() {
    const inputBuscar = document.getElementById('buscadorEquipos');
    const btnLimpiar = document.getElementById('limpiarBusqueda');
    const botonesEstado = document.querySelectorAll('.filtro-estado');
    const filas = document.querySelectorAll('.fila-equipo');
    const filaVacia = document.getElementById('filaSinResultados');
    const contador = document.getElementById('contadorMostrados');
    const total = filas.length;
    let estadoActivo = 'Todos';

    function aplicarFiltros() {
        const texto = inputBuscar.value.trim().toLowerCase();
        btnLimpiar.classList.toggle('d-none', texto === '');
        let visibles = 0;

        filas.forEach(fila => {
            const coincideEstado = (estadoActivo === 'Todos' || fila.dataset.estado === estadoActivo);
            const coincideTexto = (texto === '' || fila.dataset.busqueda.includes(texto));

            if (coincideEstado && coincideTexto) {
                fila.classList.remove('d-none');
                visibles++;
            } else {
                fila.classList.add('d-none');
            }
        });

        if (filaVacia && total > 0) {
            filaVacia.classList.toggle('d-none', visibles > 0);
        }
        if (contador) {
            contador.textContent = `Mostrando ${visibles} de ${total}`;
        }
    }

    inputBuscar.addEventListener('input', aplicarFiltros);

    btnLimpiar.addEventListener('click', () => {
        inputBuscar.value = '';
        aplicarFiltros();
        inputBuscar.focus();
    });

    botonesEstado.forEach(btn => {
        btn.addEventListener('click', () => {
            botonesEstado.forEach(b => b.classList.remove('active', 'btn-dark', 'text-white'));
            botonesEstado.forEach(b => {
                if (b.dataset.estado === 'Todos') b.classList.add('btn-outline-dark');
            });

            btn.classList.remove('btn-outline-dark');
            btn.classList.add('active');
            if (btn.dataset.estado === 'Todos') {
                btn.classList.add('btn-dark', 'text-white');
            }

            estadoActivo = btn.dataset.estado;
            aplicarFiltros();
        });
    });

    // Obtener filas actualmente visibles para exportar
    function obtenerFilasVisibles() {
        return Array.from(filas).filter(f => !f.classList.contains('d-none'));
    }

    // Exportar a Excel (CSV con BOM UTF-8)
    document.getElementById('btnExportarExcel').addEventListener('click', () => {
        const visibles = obtenerFilasVisibles();
        if (visibles.length === 0) {
            Swal.fire({ icon: 'info', title: 'Sin datos', text: 'No hay equipos visibles para exportar.' });
            return;
        }

        const encabezados = ['Codigo', 'Equipo', 'Marca / Modelo', 'Categoria', 'Tarifa por Dia (S/)', 'Estado Operativo'];
        const lineas = [encabezados.join(';')];

        visibles.forEach(f => {
            const cols = [
                f.dataset.codigo,
                f.dataset.nombre,
                f.dataset.marca,
                f.dataset.categoria,
                f.dataset.tarifa,
                f.dataset.estado
            ].map(val => `"${String(val || '').replace(/"/g, '""')}"`);
            lineas.push(cols.join(';'));
        });

        const bom = '\uFEFF';
        const blob = new Blob([bom + lineas.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `Inventario_Equipos_EmpresaM_${new Date().toISOString().slice(0, 10)}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    });

    // Exportar a Reporte PDF Imprimible
    document.getElementById('btnExportarPDF').addEventListener('click', () => {
        const visibles = obtenerFilasVisibles();
        if (visibles.length === 0) {
            Swal.fire({ icon: 'info', title: 'Sin datos', text: 'No hay equipos visibles para generar el reporte.' });
            return;
        }

        const fechaHoy = new Date().toLocaleDateString('es-PE', { year: 'numeric', month: 'long', day: 'numeric' });
        let filasHtml = '';
        visibles.forEach((f, idx) => {
            filasHtml += `
                <tr>
                    <td>${idx + 1}</td>
                    <td><strong>${f.dataset.codigo}</strong></td>
                    <td>${f.dataset.nombre}</td>
                    <td>${f.dataset.marca || '-'}</td>
                    <td>${f.dataset.categoria}</td>
                    <td style="text-align:right;">S/ ${parseFloat(f.dataset.tarifa).toFixed(2)}</td>
                    <td>${f.dataset.estado}</td>
                </tr>`;
        });

        const ventana = window.open('', '_blank', 'width=920,height=680');
        ventana.document.write(`
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>Reporte de Inventario de Maquinaria - Empresa M</title>
                <style>
                    body { font-family: Arial, sans-serif; color: #1e293b; padding: 24px; }
                    .header { display: flex; justify-content: space-between; border-bottom: 2px solid #0b1120; padding-bottom: 12px; margin-bottom: 18px; }
                    .title { font-size: 18px; font-weight: bold; color: #0b1120; }
                    .sub { font-size: 12px; color: #64748b; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th { background: #0b1120; color: #fff; text-align: left; padding: 8px; }
                    td { border-bottom: 1px solid #cbd5e1; padding: 7px 8px; }
                    tr:nth-child(even) { background: #f8fafc; }
                    .footer { margin-top: 18px; font-size: 11px; color: #64748b; text-align: right; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div>
                        <div class="title">EMPRESA M | ALQUILER DE MAQUINARIA</div>
                        <div class="sub">Reporte Oficial de Inventario de Flota (Filtro: ${estadoActivo})</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="sub">Fecha de emisión: ${fechaHoy}</div>
                        <div class="sub">Total equipos listados: ${visibles.length}</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Equipo</th>
                            <th>Marca / Modelo</th>
                            <th>Categoría</th>
                            <th style="text-align:right;">Tarifa / Día</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>${filasHtml}</tbody>
                </table>
                <div class="footer">Generado desde el Sistema Web de Gestión de Alquiler — Empresa M</div>
                <script>window.onload = function() { window.print(); };<\/script>
            </body>
            </html>
        `);
        ventana.document.close();
    });
})();
</script>