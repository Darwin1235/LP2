<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?action=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alquiler de Maquinaria - Empresa M</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php?action=dashboard">Empresa M | Alquiler</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php?action=equipos">Equipos</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php?action=clientes">Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php?action=contratos">Contratos</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php?action=devoluciones">Devoluciones</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php?action=alertas">Alertas</a></li>
      </ul>
      <span class="navbar-text text-white me-3">
        <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?> (<?= $_SESSION['usuario']['rol'] ?>)
      </span>
      <a href="index.php?action=logout" class="btn btn-outline-danger btn-sm">Salir</a>
    </div>
  </div>
</nav>
<div class="container">