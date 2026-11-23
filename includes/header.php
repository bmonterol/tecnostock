<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Guardián de seguridad
if (!isset($_SESSION['id_usuario'])) {
    header("Location: /tecnostock/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TecnoStock - Inventario</title>
    <!-- CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/tecnostock/index.php">📦 TecnoStock</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/tecnostock/modules/categorias/index.php">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/tecnostock/modules/productos/index.php">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/tecnostock/modules/movimientos/index.php">Movimientos</a>
                </li>
            </ul>
            <span class="navbar-text me-3">
                👤 <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </span>
            <a href="/tecnostock/logout.php" class="btn btn-outline-danger btn-sm">Cerrar Sesión</a>
        </div>
    </div>
</nav>

<!-- Contenedor principal donde irá el contenido de cada página -->
<div class="container">