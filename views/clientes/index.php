<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <?= $clienteEditar ? 'Editar Cliente' : 'Registrar Cliente' ?>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=clientes">
                    <input type="hidden" name="id_cliente" value="<?= $clienteEditar['id_cliente'] ?? '' ?>">
                    <div class="mb-2">
                        <label>DNI / RUC</label>
                        <input type="text" name="documento" class="form-control" required value="<?= htmlspecialchars($clienteEditar['documento'] ?? '') ?>">
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
                        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($clienteEditar['telefono'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label>Correo</label>
                        <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($clienteEditar['correo'] ?? '') ?>">
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
            <div class="card-header bg-dark text-white">Directorio de Clientes</div>
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
                        <?php foreach ($clientes as $cli): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($cli['documento']) ?></strong></td>
                            <td><?= htmlspecialchars($cli['nombre'] . ' ' . $cli['apellido']) ?><br><small class="text-muted"><?= htmlspecialchars($cli['direccion']) ?></small></td>
                            <td><?= htmlspecialchars($cli['telefono']) ?></td>
                            <td><?= htmlspecialchars($cli['correo']) ?></td>
                            <td>
                                <a href="index.php?action=clientes&editar=<?= $cli['id_cliente'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="index.php?action=clientes&eliminar=<?= $cli['id_cliente'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar cliente?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>