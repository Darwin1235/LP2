<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?action=login");
    exit();
}
$currentAction = $_GET['action'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alquiler de Maquinaria - Empresa M</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            color: #1e293b;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .navbar-corp {
            background-color: #0f172a;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.15);
            padding-top: 0.7rem;
            padding-bottom: 0.7rem;
        }
        .navbar-corp .navbar-brand {
            color: #ffffff;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .brand-accent {
            color: #38bdf8;
        }
        .navbar-corp .nav-link {
            color: #cbd5e1;
            font-weight: 500;
            font-size: 0.93rem;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .navbar-corp .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
        }
        .navbar-corp .nav-link.active {
            color: #ffffff;
            background-color: #2563eb;
            font-weight: 600;
        }
        .user-pill {
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #e2e8f0;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        /* Mejora global para tablas y tarjetas de todos los módulos */
        .table {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        }
        .table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 2px solid #e2e8f0;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-corp mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php?action=dashboard">
        <i class="bi bi-gear-wide-connected brand-accent"></i>
        <span>Empresa M <span class="text-secondary fw-normal">|</span> <span class="brand-accent">Alquiler</span></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menuPrincipal">
      <ul class="navbar-nav me-auto ms-lg-3 gap-1">
        <li class="nav-item">
            <a class="nav-link <?= $currentAction === 'dashboard' ? 'active' : '' ?>" href="index.php?action=dashboard">
                <i class="bi bi-speedometer2 me-1"></i>Inicio
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentAction === 'equipos' ? 'active' : '' ?>" href="index.php?action=equipos">
                <i class="bi bi-truck-flatbed me-1"></i>Equipos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentAction === 'clientes' ? 'active' : '' ?>" href="index.php?action=clientes">
                <i class="bi bi-people me-1"></i>Clientes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentAction === 'contratos' ? 'active' : '' ?>" href="index.php?action=contratos">
                <i class="bi bi-file-earmark-text me-1"></i>Contratos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentAction === 'devoluciones' ? 'active' : '' ?>" href="index.php?action=devoluciones">
                <i class="bi bi-arrow-return-left me-1"></i>Devoluciones
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentAction === 'alertas' ? 'active' : '' ?>" href="index.php?action=alertas">
                <i class="bi bi-bell me-1"></i>Alertas
            </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-3 mt-2 mt-lg-0">
        <span class="user-pill">
            <i class="bi bi-person-circle me-1 text-info"></i>
            <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?> <small class="text-info">(<?= $_SESSION['usuario']['rol'] ?>)</small>
        </span>
        <a href="index.php?action=logout" class="btn btn-outline-light btn-sm px-3" style="border-radius: 8px;">
            <i class="bi bi-box-arrow-right me-1"></i>Salir
        </a>
      </div>
    </div>
  </div>
</nav>
<div class="container pb-5">