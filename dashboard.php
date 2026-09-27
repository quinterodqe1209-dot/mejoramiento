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
    <section class="seccion-graficos" aria-labelledby="titulo-graficos">
        <h2 id="titulo-graficos" class="text-lg">Indicadores gráficos</h2>
        <form id="filtro-graficos" class="filtro-graficos">
            <div class="form-group"><label for="desde">Desde</label><input class="form-input" id="desde" type="date" value="<?= date('Y-01-01') ?>" required></div>
            <div class="form-group"><label for="hasta">Hasta</label><input class="form-input" id="hasta" type="date" value="<?= date('Y-m-d') ?>" required></div>
            <button class="btn-primary" type="submit">Actualizar gráficos</button>
        </form>
        <p id="error-graficos" class="alerta alerta--error" role="alert"></p>
        <div class="graficos-grid">
            <article class="grafico-panel"><h3>Ventas por mes</h3><div class="grafico-lienzo"><canvas id="grafico-ventas"></canvas></div></article>
            <article class="grafico-panel"><h3>Ventas por categoría</h3><div class="grafico-lienzo"><canvas id="grafico-categorias"></canvas></div></article>
            <article class="grafico-panel grafico-panel--ancho"><h3>Clientes con mayor compra</h3><div class="grafico-lienzo"><canvas id="grafico-clientes"></canvas></div></article>
        </div>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="js/graficos.js"></script>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
