<?php
require_once __DIR__ . '/sgl/guardia.php';
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | ZDTecnoc</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<main class="panel__contenido">
    <header class="login-header" style="text-align:left;">
        <span class="brand-name text-xl">ZDTecnoc</span>
        <h1 class="text-lg">Gestión de productos</h1>
        <p>Sesión: <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>
            (<?= htmlspecialchars($usuario['rol'], ENT_QUOTES, 'UTF-8') ?>)</p>
    </header>
    <nav aria-label="Navegación principal">
        <a href="dashboard.php">Inicio</a> |
        <a href="productos.php" aria-current="page">Productos</a> |
        <a href="sgl/salir.php">Cerrar sesión</a>
    </nav>
    <section class="login-card" style="max-width:100%; margin-top:1.5rem;">
        <h2 class="text-lg">Acceso protegido</h2>
        <p>Esta página solo se muestra cuando existe una sesión válida.</p>
        <p>El CRUD de productos se implementará sobre esta misma protección en el día 13.</p>
    </section>
</main>
</body>
</html>
