<?php
$totalClientes = count($clientes);
$totalDni      = count(array_filter($clientes, fn($c) => strlen($c['documento']) === 8));
$totalRuc      = count(array_filter($clientes, fn($c) => strlen($c['documento']) === 11));
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-dark text-white">
                <?= $clienteEditar ? 'Editar Cliente' : 'Registrar Cliente' ?>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=clientes">
                    <input type="hidden" name="id_cliente" value="<?= $clienteEditar['id_cliente'] ?? '' ?>">
                    <div class="mb-2">
                        <label>DNI / RUC</label>
                        <input type="text" name="documento" class="form-control" pattern="([0-9]{8}|[0-9]{11})" maxlength="11" placeholder="8 dígitos (DNI) u 11 dígitos (RUC)" required value="<?= htmlspecialchars($clienteEditar['documento'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label>Nombre / Razón Social</label>
                        <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($clienteEditar['nombre'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label>Apellido / Representante</label>
                        <input type="text" name="apellido" class="form-control" required value="<?= htmlspecialchars($clienteEditar['apellido'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control" placeholder="Ej. 987654321" value="<?= htmlspecialchars($clienteEditar['telefono'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label>Correo</label>
                        <input type="email" name="correo" class="form-control" placeholder="cliente@correo.com" value="<?= htmlspecialchars($clienteEditar['correo'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($clienteEditar['direccion'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Guardar Cliente</button>
                    <?php if ($clienteEditar): ?>
                        <a href="index.php?action=clientes" class="btn btn-secondary w-100 mt-1">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>Directorio de Clientes</span>
                <span class="badge bg-light text-dark" id="contadorClientes">
                    Mostrando <?= $totalClientes ?> de <?= $totalClientes ?>
                </span>
            </div>

            <div class="card-body border-bottom bg-light py-3">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted">🔍</span>
                            <input type="text" id="buscadorClientes" class="form-control border-start-0"
                                   placeholder="Buscar por DNI/RUC, nombre, teléfono o correo...">
                            <button type="button" id="limpiarBusquedaCli" class="btn btn-outline-secondary d-none" title="Limpiar filtro">✕</button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="d-flex flex-wrap gap-1 justify-content-lg-end" id="filtrosTipoDoc">
                            <button type="button" class="btn btn-sm btn-dark filtro-doc active" data-tipo="Todos">
                                Todos <span class="badge bg-secondary ms-1"><?= $totalClientes ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary filtro-doc" data-tipo="DNI">
                                DNI (Natural) <span class="badge bg-primary ms-1"><?= $totalDni ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info text-dark filtro-doc" data-tipo="RUC">
                                RUC (Empresa) <span class="badge bg-info text-dark ms-1"><?= $totalRuc ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Documento</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientes)): ?>
                            <tr><td colspan="5" class="text-center text-muted">Aún no hay clientes registrados.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($clientes as $cli):
                            $tipoDoc = strlen($cli['documento']) === 11 ? 'RUC' : 'DNI';
                        ?>
                        <tr class="fila-cliente"
                            data-tipo="<?= $tipoDoc ?>"
                            data-busqueda="<?= htmlspecialchars(strtolower($cli['documento'] . ' ' . $cli['nombre'] . ' ' . $cli['apellido'] . ' ' . $cli['telefono'] . ' ' . $cli['correo'])) ?>">
                            <td>
                                <strong><?= htmlspecialchars($cli['documento']) ?></strong><br>
                                <span class="badge bg-<?= $tipoDoc === 'RUC' ? 'info text-dark' : 'secondary' ?>"><?= $tipoDoc ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($cli['nombre'] . ' ' . $cli['apellido']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($cli['direccion']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($cli['telefono']) ?></td>
                            <td><?= htmlspecialchars($cli['correo']) ?></td>
                            <td>
                                <a href="index.php?action=clientes&editar=<?= $cli['id_cliente'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <?php if (($_SESSION['usuario']['rol'] ?? '') === 'Administrador'): ?>
                                    <a href="index.php?action=clientes&eliminar=<?= $cli['id_cliente'] ?>"
                                       class="btn btn-sm btn-outline-danger btn-confirmar-eliminar"
                                       data-mensaje="Se eliminará al cliente «<?= htmlspecialchars($cli['nombre'] . ' ' . $cli['apellido']) ?>» (<?= htmlspecialchars($cli['documento']) ?>).">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr id="filaSinResultadosCli" class="d-none">
                            <td colspan="5" class="text-center text-muted py-4">
                                No se encontraron clientes que coincidan con la búsqueda.
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
    const inputBuscar = document.getElementById('buscadorClientes');
    const btnLimpiar = document.getElementById('limpiarBusquedaCli');
    const botonesTipo = document.querySelectorAll('.filtro-doc');
    const filas = document.querySelectorAll('.fila-cliente');
    const filaVacia = document.getElementById('filaSinResultadosCli');
    const contador = document.getElementById('contadorClientes');
    const total = filas.length;
    let tipoActivo = 'Todos';

    function aplicarFiltros() {
        const texto = inputBuscar.value.trim().toLowerCase();
        btnLimpiar.classList.toggle('d-none', texto === '');
        let visibles = 0;

        filas.forEach(fila => {
            const coincideTipo = (tipoActivo === 'Todos' || fila.dataset.tipo === tipoActivo);
            const coincideTexto = (texto === '' || fila.dataset.busqueda.includes(texto));

            if (coincideTipo && coincideTexto) {
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

    botonesTipo.forEach(btn => {
        btn.addEventListener('click', () => {
            botonesTipo.forEach(b => b.classList.remove('active', 'btn-dark', 'text-white'));
            botonesTipo.forEach(b => {
                if (b.dataset.tipo === 'Todos') b.classList.add('btn-outline-dark');
            });

            btn.classList.remove('btn-outline-dark');
            btn.classList.add('active');
            if (btn.dataset.tipo === 'Todos') {
                btn.classList.add('btn-dark', 'text-white');
            }

            tipoActivo = btn.dataset.tipo;
            aplicarFiltros();
        });
    });
})();
</script>