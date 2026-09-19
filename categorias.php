<?php
require_once __DIR__ . '/sgl/guardia.php';
exigirRol('administrador', 'vendedor');
$tituloPagina = 'Categorías';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <section><h2 class="text-lg">Categorías</h2><p class="texto-tenue">Módulo disponible para administradores y vendedores. El CRUD se implementará en el día 13.</p></section>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
