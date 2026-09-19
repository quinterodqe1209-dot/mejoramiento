<?php
require_once __DIR__ . '/sgl/guardia.php';
exigirRol('administrador');
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | ZDTecnoc</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<main class="login-container">
    <section class="login-card" aria-labelledby="titulo-usuarios">
        <h1 id="titulo-usuarios">Gestión de usuarios</h1>
        <p>Área exclusiva para el rol administrador.</p>
        <p>Usuario actual: <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><a href="dashboard.php">Volver al dashboard</a> | <a href="sgl/salir.php">Cerrar sesión</a></p>
    </section>
</main>
</body>
</html>
