<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Personal — Empresa M | Alquiler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --nav-dark: #0b1120;
            --brand-blue: #2563eb;
            --brand-blue-hover: #1d4ed8;
            --brand-cyan: #38bdf8;
            --surface-bg: #f1f5f9;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background-color: var(--surface-bg);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: stretch;
        }

        .split-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .brand-side {
            flex: 1.05;
            background: linear-gradient(145deg, #070b14 0%, #0b1120 55%, #132240 100%);
            color: #ffffff;
            padding: 3rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .brand-side::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(56, 189, 248, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56, 189, 248, 0.05) 1px, transparent 1px);
            background-size: 38px 38px;
            pointer-events: none;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.35rem;
            font-weight: 700;
            position: relative;
            z-index: 2;
        }

        .brand-logo i {
            color: var(--brand-cyan);
            font-size: 1.55rem;
        }

        .brand-logo .divider {
            color: rgba(255, 255, 255, 0.25);
            font-weight: 300;
        }

        .brand-logo .highlight {
            color: var(--brand-cyan);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 480px;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(37, 99, 235, 0.2);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #7dd3fc;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            margin-bottom: 1.25rem;
        }

        .hero-title {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.22;
            margin-bottom: 1rem;
        }

        .hero-desc {
            color: #94a3b8;
            font-size: 0.96rem;
            line-height: 1.6;
        }

        .machinery-bg-svg {
            position: absolute;
            right: -20px;
            bottom: 20px;
            width: 420px;
            opacity: 0.11;
            pointer-events: none;
            z-index: 1;
        }

        .brand-footer {
            position: relative;
            z-index: 2;
            color: #64748b;
            font-size: 0.82rem;
        }

        .form-side {
            flex: 1.05;
            background-color: var(--surface-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        .register-card {
            width: 100%;
            max-width: 470px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 4px solid var(--brand-blue);
            border-radius: 14px;
            padding: 2rem 2rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.07);
        }

        .register-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f172a;
        }

        .register-subtitle {
            color: #64748b;
            font-size: 0.88rem;
        }

        .form-label {
            font-size: 0.83rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-right: none;
            color: #64748b;
        }

        .form-control, .form-select {
            border: 1px solid #cbd5e1;
            padding: 0.6rem 0.85rem;
            font-size: 0.92rem;
            color: #0f172a;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--brand-blue);
            color: var(--brand-blue);
        }

        .btn-primary-brand {
            background-color: var(--brand-blue);
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 0.7rem 1rem;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: background-color 0.15s ease, transform 0.15s ease;
        }

        .btn-primary-brand:hover {
            background-color: var(--brand-blue-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .link-brand {
            color: var(--brand-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .link-brand:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .brand-side {
                display: none;
            }
            .form-side {
                background: linear-gradient(145deg, #0b1120 0%, #132240 100%);
            }
        }
    </style>
</head>
<body>

<div class="split-container">
    <div class="brand-side">
        <div class="brand-logo">
            <i class="bi bi-gear-wide-connected"></i>
            <span>Empresa M</span>
            <span class="divider">|</span>
            <span class="highlight">Alquiler</span>
        </div>

        <div class="hero-content">
            <div class="hero-pill">
                <i class="bi bi-person-badge"></i>
                <span>CONTROL DE ACCESO POR ROLES</span>
            </div>
            <h1 class="hero-title">Alta de personal operativo y administrativo</h1>
            <p class="hero-desc">
                Crea una cuenta asignando el perfil correspondiente (Encargado o Administrador) para gestionar equipos, clientes y contratos según los permisos de tu área.
            </p>
        </div>

        <svg class="machinery-bg-svg" viewBox="0 0 640 360" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="110" y="275" width="270" height="52" rx="26" stroke="#38bdf8" stroke-width="8"/>
            <circle cx="145" cy="301" r="14" stroke="#38bdf8" stroke-width="6"/>
            <circle cx="205" cy="301" r="14" stroke="#38bdf8" stroke-width="6"/>
            <circle cx="285" cy="301" r="14" stroke="#38bdf8" stroke-width="6"/>
            <circle cx="345" cy="301" r="14" stroke="#38bdf8" stroke-width="6"/>
            <path d="M135 275V205H225V135H315L345 205H365V275H135Z" stroke="#38bdf8" stroke-width="8" stroke-linejoin="round"/>
            <rect x="242" y="152" width="60" height="45" rx="6" stroke="#38bdf8" stroke-width="5"/>
            <path d="M345 190L465 75L555 155" stroke="#38bdf8" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M360 215L460 115" stroke="#38bdf8" stroke-width="6" stroke-linecap="round"/>
            <path d="M545 155L595 175L580 225L530 205Z" stroke="#38bdf8" stroke-width="8" stroke-linejoin="round"/>
        </svg>

        <div class="brand-footer">
            © 2026 Empresa M — Sistema de Alquiler y Control de Maquinaria
        </div>
    </div>

    <div class="form-side">
        <div class="register-card">
            <div class="mb-3">
                <h2 class="register-title mb-1">Crear Cuenta</h2>
                <p class="register-subtitle mb-0">Registro de personal en Empresa M</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center py-2 px-3 small">
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
                            <option value="Encargado" <?= (($_POST['rol'] ?? '') === 'Encargado') ? 'selected' : '' ?>>Encargado</option>
                            <option value="Administrador" <?= (($_POST['rol'] ?? '') === 'Administrador') ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="contrasena" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" name="confirmar_contrasena" class="form-control" required minlength="6" placeholder="Repita su clave">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-brand w-100">
                    <i class="bi bi-person-plus-fill me-1"></i> Registrarme
                </button>
            </form>

            <div class="text-center mt-3 pt-3 border-top">
                <small class="text-muted">
                    ¿Ya tienes cuenta?
                    <a href="index.php?action=login" class="link-brand ms-1">Inicia sesión</a>
                </small>
            </div>
        </div>
    </div>
</div>

</body>
</html>