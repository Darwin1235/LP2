<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso al Sistema — Empresa M | Alquiler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --nav-dark: #0b1120;
            --nav-dark-2: #111827;
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
            flex: 1.15;
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

        .brand-side::after {
            content: "";
            position: absolute;
            width: 480px;
            height: 480px;
            right: -120px;
            bottom: -140px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.35) 0%, transparent 70%);
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
            max-width: 500px;
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
            letter-spacing: 0.4px;
        }

        .hero-title {
            font-size: 2.15rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .hero-desc {
            color: #94a3b8;
            font-size: 0.98rem;
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }

        .feature-list {
            display: grid;
            gap: 0.75rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #cbd5e1;
            font-size: 0.9rem;
        }

        .feature-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(37, 99, 235, 0.22);
            border: 1px solid rgba(56, 189, 248, 0.25);
            color: var(--brand-cyan);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .machinery-bg-svg {
            position: absolute;
            right: -20px;
            bottom: 20px;
            width: 440px;
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
            flex: 0.95;
            background-color: var(--surface-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 4px solid var(--brand-blue);
            border-radius: 14px;
            padding: 2.25rem 2rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.07);
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
        }

        .login-subtitle {
            color: #64748b;
            font-size: 0.9rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-right: none;
            color: #64748b;
        }

        .form-control {
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.85rem;
            font-size: 0.95rem;
            color: #0f172a;
        }

        .form-control:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--brand-blue);
            color: var(--brand-blue);
        }

        .btn-toggle-pass {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-left: none;
            color: #64748b;
        }

        .btn-toggle-pass:hover {
            color: var(--brand-blue);
            background-color: #f8fafc;
        }

        .btn-primary-brand {
            background-color: var(--brand-blue);
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 0.72rem 1rem;
            border-radius: 8px;
            font-size: 0.96rem;
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
                <i class="bi bi-shield-check"></i>
                <span>PLATAFORMA OPERATIVA DE FLOTA</span>
            </div>
            <h1 class="hero-title">Gestión integral de alquiler de maquinaria pesada</h1>
            <p class="hero-desc">
                Administra disponibilidad de equipos, directorio de clientes, contratos vigentes y alertas de devolución en tiempo real desde un solo panel.
            </p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-truck-flatbed"></i></div>
                    <span>Control de inventario y estados operativos en vivo</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-file-earmark-text"></i></div>
                    <span>Gestión ágil de contratos, tarifas y devoluciones</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-bell"></i></div>
                    <span>Monitoreo preventivo de vencimientos y alertas</span>
                </div>
            </div>
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
        <div class="login-card">
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-2 d-lg-none">
                    <i class="bi bi-gear-wide-connected text-primary fs-4"></i>
                    <span class="fw-bold text-dark">Empresa M | <span class="text-primary">Alquiler</span></span>
                </div>
                <h2 class="login-title mb-1">Ingreso al Sistema</h2>
                <p class="login-subtitle mb-0">Ingresa tus credenciales para acceder al panel</p>
            </div>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success d-flex align-items-center py-2 px-3 small">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>Cuenta creada correctamente. Ya puedes iniciar sesión.</div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center py-2 px-3 small">
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
                        <input type="password" name="contrasena" id="loginPassword" class="form-control" required placeholder="123456">
                        <button class="btn btn-toggle-pass" type="button" id="toggleLoginPass" title="Mostrar u ocultar contraseña">
                            <i class="bi bi-eye" id="toggleLoginIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-brand w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <small class="text-muted">
                    ¿No tienes una cuenta?
                    <a href="index.php?action=register" class="link-brand ms-1">Regístrate aquí</a>
                </small>
            </div>
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