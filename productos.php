<?php
require_once __DIR__ . '/sgl/guardia.php';
$tituloPagina = 'Productos';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <section aria-labelledby="titulo-productos">
        <h2 id="titulo-productos" class="text-lg">Gestión de productos</h2>
        <p class="texto-tenue">Módulo protegido. El CRUD se implementará en el día 13.</p>
        <div class="login-card" style="max-width:100%; margin-top:1.5rem;">
            <h3>Acceso autorizado</h3>
            <p>Esta vista usa el menú centralizado y la sesión del usuario autenticado.</p>
        </div>
    </section>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
