<?php
require_once __DIR__ . '/sgl/guardia.php';
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ZDTecnoc</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<div class="panel">
    <header class="panel__barra">
        <div>
            <button class="btn-menu" type="button" aria-label="Abrir menú">☰</button>
            <h1 class="text-lg">ZDTecnoc - Panel de Control</h1>
        </div>
        <span class="text-sm">
            Usuario: <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>
            (<?= htmlspecialchars($usuario['rol'], ENT_QUOTES, 'UTF-8') ?>)
        </span>
    </header>
    <aside class="panel__menu abierto" id="menu-lateral">
        <nav aria-label="Navegación principal">
            <ul style="list-style:none; padding:1rem; display:flex; flex-direction:column; gap:1rem;">
                <li><a href="dashboard.php" style="color:var(--superficie-alta);">Inicio</a></li>
                <li><a href="productos.php" style="color:var(--superficie-alta);">Productos</a></li>
                <?php if (puede('administrador')): ?>
                    <li><a href="usuarios.php" style="color:var(--superficie-alta);">Usuarios</a></li>
                <?php endif; ?>
                <li><a href="sgl/salir.php" style="color:var(--error);">Cerrar sesión</a></li>
            </ul>
        </nav>
    </aside>
    <main class="panel__contenido">
        <section>
            <h2 class="text-lg">Resumen general</h2>
            <div class="indicadores">
                <article class="login-card"><h3>Ventas del día</h3><p class="text-xl">$0.00</p></article>
                <article class="login-card"><h3>Total de productos</h3><p class="text-xl">12</p></article>
                <article class="login-card"><h3>Alertas</h3><p class="text-xl">0</p></article>
                <article class="login-card"><h3>Usuarios activos</h3><p class="text-xl">1</p></article>
            </div>
        </section>
    </main>
    <footer class="panel__pie"><p>&copy; 2026 ZDTecnoc. Todos los derechos reservados.</p></footer>
</div>
</body>
</html>
