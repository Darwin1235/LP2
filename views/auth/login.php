<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Empresa M</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center vh-100">
<div class="card p-4 shadow" style="width: 380px;">
    <h4 class="text-center mb-3">Ingreso al Sistema</h4>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="index.php?action=login">
        <div class="mb-3">
            <label>Correo electrónico</label>
            <input type="email" name="correo" class="form-control" required placeholder="admin@empresam.com">
        </div>
        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="contrasena" class="form-control" required placeholder="123456">
        </div>
        <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
    </form>
</div>
</body>
</html>