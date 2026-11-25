<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso al Sistema — Empresa M | Alquiler de Maquinaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090d16;
            --bg-panel: rgba(17, 24, 39, 0.88);
            --accent-amber: #f59e0b;
            --accent-blue: #2563eb;
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
            padding: 1.5rem 1rem;
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
            animation: spinSlow 90s linear infinite;
        }

        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .auth-wrapper {
            width: 100%;
            max-width: 430px;
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
            padding: 2.25rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fbbf24;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .brand-title {
            font-size: 1.55rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.3px;
        }

        .brand-subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .form-label {
            color: #cbd5e1;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .input-group-text {
            background-color: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-right: none;
            color: #94a3b8;
        }

        .form-control {
            background-color: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.22);
            color: #f8fafc;
            padding: 0.65rem 0.85rem;
            font-size: 0.95rem;
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .form-control:focus {
            background-color: rgba(15, 23, 42, 1);
            border-color: var(--accent-amber);
            color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.18);
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--accent-amber);
            color: #fbbf24;
        }

        .btn-toggle-pass {
            background-color: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-left: none;
            color: #94a3b8;
        }

        .btn-toggle-pass:hover {
            color: #fbbf24;
            background-color: rgba(30, 41, 59, 0.9);
        }

        .btn-industrial {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: #0f172a;
            font-weight: 700;
            padding: 0.72rem 1rem;
            border-radius: 10px;
            font-size: 0.98rem;
            letter-spacing: 0.2px;
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
        <div class="text-center mb-4">
            <div class="brand-badge">
                <i class="bi bi-gear-wide-connected"></i>
                <span>Empresa M • Alquiler</span>
            </div>
            <h1 class="brand-title mb-1">Ingreso al Sistema</h1>
            <p class="brand-subtitle mb-0">Control de Flota y Maquinaria Pesada</p>
        </div>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success d-flex align-items-center py-2 px-3 small border-0" style="background: rgba(22, 163, 74, 0.2); color: #86efac;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>Cuenta creada correctamente. Ya puedes iniciar sesión.</div>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center py-2 px-3 small border-0" style="background: rgba(220, 38, 38, 0.22); color: #fca5a5;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login">
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="correo" class="form-control" required placeholder="admin@empresam.com" autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="contrasena" id="loginPassword" class="form-control" required placeholder="••••••••">
                    <button class="btn btn-toggle-pass" type="button" id="toggleLoginPass" title="Mostrar/Ocultar contraseña">
                        <i class="bi bi-eye" id="toggleLoginIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-industrial w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
            </button>
        </form>

        <div class="text-center mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <small style="color: #94a3b8;">
                ¿No tienes una cuenta?
                <a href="index.php?action=register" class="auth-footer-link ms-1">Regístrate aquí</a>
            </small>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggleLoginPass').addEventListener('click', function () {
        const input = document.getElementById('loginPassword');
        const icon = document.getElementById('toggleLoginIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>

</body>
</html>