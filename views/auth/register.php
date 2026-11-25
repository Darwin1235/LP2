<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Personal — Empresa M | Alquiler de Maquinaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090d16;
            --bg-panel: rgba(17, 24, 39, 0.88);
            --accent-amber: #f59e0b;
            --border-subtle: rgba(255, 255, 255, 0.1);
        }

        body {
            min-height: 100vh;
            margin: 0;
            background-color: var(--bg-dark);
            background-image:
                radial-gradient(circle at 15% 20%, rgba(245, 158, 11, 0.14) 0%, transparent 42%),
                radial-gradient(circle at 85% 80%, rgba(37, 99, 235, 0.18) 0%, transparent 45%),
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 36px 36px, 36px 36px;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 2rem 1rem;
        }

        .hazard-top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: repeating-linear-gradient(
                -45deg,
                #f59e0b,
                #f59e0b 14px,
                #111827 14px,
                #111827 28px
            );
            z-index: 100;
        }

        .bg-gear-watermark {
            position: fixed;
            right: -90px;
            bottom: -90px;
            width: 420px;
            height: 420px;
            opacity: 0.045;
            pointer-events: none;
            z-index: 0;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 2;
        }

        .auth-card {
            background: var(--bg-panel);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--border-subtle);
            border-top: 3px solid var(--accent-amber);
            border-radius: 16px;
            padding: 2rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fbbf24;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .brand-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #ffffff;
        }

        .brand-subtitle {
            color: #94a3b8;
            font-size: 0.88rem;
        }

        .form-label {
            color: #cbd5e1;
            font-size: 0.83rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .input-group-text {
            background-color: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-right: none;
            color: #94a3b8;
        }

        .form-control, .form-select {
            background-color: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.22);
            color: #f8fafc;
            padding: 0.6rem 0.85rem;
            font-size: 0.92rem;
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(15, 23, 42, 1);
            border-color: var(--accent-amber);
            color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.18);
        }

        .form-select option {
            background-color: #0f172a;
            color: #f8fafc;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--accent-amber);
            color: #fbbf24;
        }

        .btn-industrial {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: #0f172a;
            font-weight: 700;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-industrial:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #090d16;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.4);
        }

        .auth-footer-link {
            color: #fbbf24;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer-link:hover {
            color: #fde68a;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="hazard-top-bar"></div>

<svg class="bg-gear-watermark" viewBox="0 0 24 24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
    <path d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58a.49.49 0 0 0 .12-.61l-1.92-3.32a.49.49 0 0 0-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 0 0-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.31-.07.63-.07.94s.02.63.07.94l-2.03 1.58a.49.49 0 0 0-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6A3.6 3.6 0 1 1 12 8.4a3.6 3.6 0 0 1 0 7.2z"/>
</svg>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-3">
            <div class="brand-badge">
                <i class="bi bi-gear-wide-connected"></i>
                <span>Empresa M • Alquiler</span>
            </div>
            <h1 class="brand-title mb-1">Crear Cuenta</h1>
            <p class="brand-subtitle mb-0">Registro de personal operativo y administrativo</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center py-2 px-3 small border-0" style="background: rgba(220, 38, 38, 0.22); color: #fca5a5;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
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
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="correo" class="form-control" required value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" placeholder="usuario@empresam.com">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Rol del sistema</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <select name="rol" class="form-select" required>
                        <option value="Encargado" <?= (($_POST['rol'] ?? '') === 'Encargado') ? 'selected' : '' ?>>Encargado de Operaciones</option>
                        <option value="Administrador" <?= (($_POST['rol'] ?? '') === 'Administrador') ? 'selected' : '' ?>>Administrador General</option>
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-4">
                <div class="col-6">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="contrasena" class="form-control" required minlength="6" placeholder="Mín. 6 caracteres">
                </div>
                <div class="col-6">
                    <label class="form-label">Confirmar</label>
                    <input type="password" name="confirmar_contrasena" class="form-control" required minlength="6" placeholder="Repita clave">
                </div>
            </div>

            <button type="submit" class="btn btn-industrial w-100">
                <i class="bi bi-person-plus-fill me-1"></i> Registrarme en el Sistema
            </button>
        </form>

        <div class="text-center mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <small style="color: #94a3b8;">
                ¿Ya tienes cuenta?
                <a href="index.php?action=login" class="auth-footer-link ms-1">Inicia sesión</a>
            </small>
        </div>
    </div>
</div>

</body>
</html>