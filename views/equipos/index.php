<?php
$totalEquipos      = count($equipos);
$totalDisponibles  = count(array_filter($equipos, fn($e) => $e['estado_operativo'] === 'Disponible'));
$totalOcupados     = count(array_filter($equipos, fn($e) => $e['estado_operativo'] === 'Ocupado'));
$totalMantenimiento = count(array_filter($equipos, fn($e) => $e['estado_operativo'] === 'Mantenimiento'));
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-dark text-white">
                <?= $equipoEditar ? 'Editar Equipo' : 'Registrar Nuevo Equipo' ?>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=equipos">
                    <input type="hidden" name="subaccion" value="guardar_equipo">
                    <input type="hidden" name="id_equipo" value="<?= $equipoEditar['id_equipo'] ?? '' ?>">
                    <div class="mb-2">
                        <label>Categoría</label>
                        <select name="id_categoria" class="form-select" required>
                            <option value="">-- Seleccione Categoría --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_categoria'] ?>" <?= ($equipoEditar['id_categoria'] ?? '') == $cat['id_categoria'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Código</label>
                        <input type="text" name="codigo" class="form-control" required placeholder="EQ-001" value="<?= htmlspecialchars($equipoEditar['codigo'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($equipoEditar['nombre'] ?? '') ?>">
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label>Marca</label>
                            <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($equipoEditar['marca'] ?? '') ?>">
                        </div>
                        <div class="col">
                            <label>Modelo</label>
                            <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($equipoEditar['modelo'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label>Precio por Día (S/)</label>
                        <input type="number" step="0.01" min="0.01" name="precio_alquiler" class="form-control" required value="<?= $equipoEditar['precio_alquiler'] ?? '' ?>">
                    </div>
                    <div class="mb-2">
                        <label>Estado Operativo</label>
                        <select name="estado_operativo" class="form-select">
                            <?php foreach (['Disponible', 'Ocupado', 'Mantenimiento'] as $est): ?>
                                <option value="<?= $est ?>" <?= ($equipoEditar['estado_operativo'] ?? '') === $est ? 'selected' : '' ?>><?= $est ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($equipoEditar['descripcion'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Guardar Equipo</button>
                    <?php if ($equipoEditar): ?>
                        <a href="index.php?action=equipos" class="btn btn-secondary w-100 mt-1">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">Categorías de Equipo</div>
            <div class="card-body">
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
                <ul class="list-group list-group-flush small">
                    <?php if (empty($categorias)): ?>
                        <li class="list-group-item text-muted px-0">Cree una categoría antes de registrar equipos.</li>
                    <?php endif; ?>
                    <?php foreach ($categorias as $c): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><strong><?= htmlspecialchars($c['nombre']) ?></strong></span>
                            <?php if (($_SESSION['usuario']['rol'] ?? '') === 'Administrador'): ?>
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

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>Inventario de Maquinaria</span>
                <span class="badge bg-light text-dark" id="contadorMostrados">
                    Mostrando <?= $totalEquipos ?> de <?= $totalEquipos ?>
                </span>
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
                            data-estado="<?= htmlspecialchars($eq['estado_operativo']) ?>"
                            data-busqueda="<?= htmlspecialchars(strtolower($eq['codigo'] . ' ' . $eq['nombre'] . ' ' . $eq['marca'] . ' ' . $eq['modelo'] . ' ' . $eq['categoria'])) ?>">
                            <td><strong><?= htmlspecialchars($eq['codigo']) ?></strong></td>
                            <td><?= htmlspecialchars($eq['nombre']) ?><br><small class="text-muted"><?= htmlspecialchars($eq['marca'] . ' ' . $eq['modelo']) ?></small></td>
                            <td><?= htmlspecialchars($eq['categoria']) ?></td>
                            <td>S/ <?= number_format($eq['precio_alquiler'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $eq['estado_operativo'] === 'Disponible' ? 'success' : ($eq['estado_operativo'] === 'Ocupado' ? 'warning text-dark' : 'danger') ?>">
                                    <?= $eq['estado_operativo'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?action=equipos&editar=<?= $eq['id_equipo'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <?php if (($_SESSION['usuario']['rol'] ?? '') === 'Administrador'): ?>
                                    <a href="index.php?action=equipos&eliminar=<?= $eq['id_equipo'] ?>"
                                       class="btn btn-sm btn-outline-danger btn-confirmar-eliminar"
                                       data-mensaje="Se eliminará el equipo «<?= htmlspecialchars($eq['codigo'] . ' - ' . $eq['nombre']) ?>» del inventario.">Eliminar</a>
                                <?php endif; ?>
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
                const est = b.dataset.estado;
                if (est === 'Todos') b.classList.add('btn-outline-dark');
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
})();
</script>