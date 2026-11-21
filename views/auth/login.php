<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Empresa M</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center vh-100">
<div class="card p-4 shadow" style="width: 390px;">
    <h4 class="text-center mb-3">Ingreso al Sistema</h4>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success py-2">
            Cuenta creada correctamente. Ya puedes iniciar sesión.
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=login">
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="correo" class="form-control" required placeholder="admin@empresam.com">
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="contrasena" class="form-control" required placeholder="123456">
        </div>
        <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
    </form>

    <div class="text-center mt-3">
        <small class="text-muted">¿No tienes una cuenta? <a href="index.php?action=register" class="text-decoration-none fw-semibold">Regístrate aquí</a></small>
    </div>
</div>
</body>
</html>