<div class="row">
    <!-- Formulario de Equipos -->
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
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_categoria'] ?>" <?= ($equipoEditar['id_categoria'] ?? '') == $cat['id_categoria'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Código</label>
                        <input type="text" name="codigo" class="form-control" required placeholder="EQ-004" value="<?= htmlspecialchars($equipoEditar['codigo'] ?? '') ?>">
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
                        <input type="number" step="0.01" name="precio_alquiler" class="form-control" required value="<?= $equipoEditar['precio_alquiler'] ?? '' ?>">
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

        <!-- Gestión Rápida de Categorías -->
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
                    <?php foreach ($categorias as $c): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><strong><?= htmlspecialchars($c['nombre']) ?></strong></span>
                            <a href="index.php?action=equipos&eliminar_cat=<?= $c['id_categoria'] ?>" class="text-danger text-decoration-none" onclick="return confirm('¿Eliminar categoría?')">✕</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tabla de Equipos -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">Inventario de Maquinaria</div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
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
                        <?php foreach ($equipos as $eq): ?>
                        <tr>
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
                                <a href="index.php?action=equipos&eliminar=<?= $eq['id_equipo'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este equipo?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>