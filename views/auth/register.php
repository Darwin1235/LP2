<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Empresa M</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 py-4">
<div class="card p-4 shadow" style="width: 430px;">
    <h4 class="text-center mb-1">Crear Cuenta</h4>
    <p class="text-center text-muted small mb-3">Registro de personal en Empresa M</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=register">
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" placeholder="Ej. Juan">
            </div>
            <div class="col-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control" required value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" placeholder="Ej. Pérez">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="correo" class="form-control" required value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" placeholder="usuario@empresam.com">
        </div>

        <div class="mb-3">
            <label class="form-label">Rol del sistema</label>
            <select name="rol" class="form-select" required>
                <option value="Encargado" <?= (($_POST['rol'] ?? '') === 'Encargado') ? 'selected' : '' ?>>Encargado</option>
                <option value="Administrador" <?= (($_POST['rol'] ?? '') === 'Administrador') ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="contrasena" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="confirmar_contrasena" class="form-control" required minlength="6" placeholder="Repita su contraseña">
        </div>

        <button type="submit" class="btn btn-primary w-100">Registrarme</button>
    </form>

    <div class="text-center mt-3">
        <small class="text-muted">¿Ya tienes cuenta? <a href="index.php?action=login" class="text-decoration-none fw-semibold">Inicia sesión</a></small>
    </div>
</div>
</body>
</html>