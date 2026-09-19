<?php
require_once __DIR__ . '/sgl/guardia.php';
exigirRol('administrador', 'consultor');
$tituloPagina = 'Reportes';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <section><h2 class="text-lg">Reportes</h2><p class="texto-tenue">Módulo disponible para administradores y consultores. Los reportes se implementarán en el día 15.</p></section>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
