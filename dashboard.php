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
    <section aria-labelledby="titulo-bienvenida" style="margin-bottom: 2rem; padding: 1.5rem; background-color: var(--superficie-alta); border: 1px solid var(--borde); border-radius: 8px;">
        <h1 id="titulo-bienvenida" style="color: var(--color-marca); margin-bottom: 0.5rem; font-size: 1.5rem;">
            ¡Bienvenido/a, <?= htmlspecialchars($_SESSION['usuario']['nombre'], ENT_QUOTES, 'UTF-8') ?>!
        </h1>
        <p class="texto-tenue" style="font-size: 1.1rem;">
            Has iniciado sesión como <strong style="color: var(--texto); text-transform: capitalize;"><?= htmlspecialchars($_SESSION['usuario']['rol'], ENT_QUOTES, 'UTF-8') ?></strong>.
        </p>
    </section>

    <?php $rol = $_SESSION['usuario']['rol']; ?>
    
    <?php if ($rol === 'administrador' || $rol === 'vendedor'): ?>
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
                
                <?php if ($rol === 'administrador'): ?>
                    <article class="tarjeta-indicador">
                        <h3>Usuarios activos</h3>
                        <p class="text-xl"><?= (int)$indicadores['usuarios_activos'] ?></p>
                    </article>
                <?php endif; ?>
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

    <?php elseif ($rol === 'consultor'): ?>
        <section aria-labelledby="titulo-resumen">
            <h2 id="titulo-resumen" class="text-lg">Catálogo disponible</h2>
            <p class="texto-tenue">Explora los productos y novedades que tenemos para ti.</p>
            <div class="indicadores">
                <article class="tarjeta-indicador" style="background-color: var(--color-marca-sua); border-color: var(--color-marca);">
                    <h3>Productos disponibles en tienda</h3>
                    <p class="text-xl" style="color: var(--color-marca);"><?= (int)$indicadores['productos'] ?> artículos</p>
                </article>
            </div>
            <div style="margin-top: 2rem;">
                <a href="productos.php" class="btn-primary" style="text-decoration: none; display: inline-block;">Ver listado de productos</a>
            </div>
        </section>
    <?php endif; ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="js/graficos.js"></script>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
