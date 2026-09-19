<?php
require_once __DIR__ . '/sgl/guardia.php';
require_once __DIR__ . '/sgl/conexion.php';

$indicadores = $conexion->query(
    'SELECT
        (SELECT COUNT(*) FROM productos) AS productos,
        (SELECT COUNT(*) FROM pedidos WHERE DATE(fecha_pedido) = CURDATE()) AS pedidos_dia,
        (SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE DATE(fecha_pedido) = CURDATE()) AS ventas_dia,
        (SELECT COUNT(*) FROM productos WHERE stock < 5) AS stock_critico,
        (SELECT COUNT(*) FROM usuarios WHERE activo = 1) AS usuarios_activos'
)->fetch();

$tituloPagina = 'Dashboard';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <section aria-labelledby="titulo-resumen">
        <h2 id="titulo-resumen" class="text-lg">Resumen general</h2>
        <p class="texto-tenue">Indicadores actualizados desde la base de datos.</p>
        <div class="indicadores">
            <article class="tarjeta-indicador">
                <h3>Productos registrados</h3>
                <p class="text-xl"><?= (int)$indicadores['productos'] ?></p>
            </article>
            <article class="tarjeta-indicador">
                <h3>Pedidos de hoy</h3>
                <p class="text-xl"><?= (int)$indicadores['pedidos_dia'] ?></p>
            </article>
            <article class="tarjeta-indicador">
                <h3>Ventas de hoy</h3>
                <p class="text-xl">$ <?= number_format((float)$indicadores['ventas_dia'], 0, ',', '.') ?></p>
            </article>
            <article class="tarjeta-indicador">
                <h3>Stock crítico</h3>
                <p class="text-xl"><?= (int)$indicadores['stock_critico'] ?></p>
            </article>
            <article class="tarjeta-indicador">
                <h3>Usuarios activos</h3>
                <p class="text-xl"><?= (int)$indicadores['usuarios_activos'] ?></p>
            </article>
        </div>
    </section>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
