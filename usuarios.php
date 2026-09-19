<?php
require_once __DIR__ . '/sgl/guardia.php';
exigirRol('administrador');
$tituloPagina = 'Usuarios';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <section aria-labelledby="titulo-usuarios">
        <h2 id="titulo-usuarios" class="text-lg">Gestión de usuarios</h2>
        <p>Área exclusiva para el rol administrador.</p>
    </section>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
