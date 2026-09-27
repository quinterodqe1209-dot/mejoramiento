<?php
declare(strict_types=1);
require_once __DIR__ . '/sgl/guardia.php';
require_once __DIR__ . '/sgl/conexion.php';
exigirRol('administrador', 'consultor');
$tipo = (string)($_GET['tipo'] ?? 'ventas_categoria');
$permitidos = ['ventas_categoria', 'stock_critico', 'pedidos_cliente'];
if (!in_array($tipo, $permitidos, true)) $tipo = 'ventas_categoria';
$desde = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)($_GET['desde'] ?? '')) ? (string)$_GET['desde'] : date('Y-01-01');
$hasta = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)($_GET['hasta'] ?? '')) ? (string)$_GET['hasta'] : date('Y-m-d');
$titulos = ['ventas_categoria'=>'Reporte de ventas por categoría','stock_critico'=>'Reporte de inventario crítico','pedidos_cliente'=>'Reporte de pedidos por cliente'];
$filas = [];
$totalGeneral = 0.0;
$totalSecundario = 0;
if ($tipo === 'ventas_categoria') {
    $st = $conexion->prepare('SELECT categoria, SUM(unidades) AS unidades, SUM(total_vendido) AS total_vendido FROM v_ventas_categoria_mes WHERE periodo BETWEEN DATE_FORMAT(:desde, "%Y-%m") AND DATE_FORMAT(:hasta, "%Y-%m") GROUP BY categoria_id, categoria ORDER BY total_vendido DESC');
    $st->execute([':desde'=>$desde, ':hasta'=>$hasta]);
    $filas = $st->fetchAll();
    foreach ($filas as $fila) { $totalSecundario += (int)$fila['unidades']; $totalGeneral += (float)$fila['total_vendido']; }
} elseif ($tipo === 'stock_critico') {
    $filas = $conexion->query('SELECT nombre, stock FROM v_stock_critico ORDER BY stock ASC, nombre')->fetchAll();
    $totalSecundario = count($filas);
} else {
    $st = $conexion->prepare('SELECT cliente, COUNT(*) AS pedidos, SUM(total_comprado) AS total_comprado FROM v_clientes_mayor_compra_mes WHERE periodo BETWEEN DATE_FORMAT(:desde, "%Y-%m") AND DATE_FORMAT(:hasta, "%Y-%m") GROUP BY cliente_id, cliente ORDER BY total_comprado DESC');
    $st->execute([':desde'=>$desde, ':hasta'=>$hasta]);
    $filas = $st->fetchAll();
    foreach ($filas as $fila) { $totalSecundario += (int)$fila['pedidos']; $totalGeneral += (float)$fila['total_comprado']; }
}
if (($_GET['exportar'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="reporte-' . $tipo . '-' . date('Ymd-His') . '.csv"');
    $salida = fopen('php://output', 'w'); fwrite($salida, "\xEF\xBB\xBF");
    if ($tipo === 'ventas_categoria') fputcsv($salida, ['Categoría','Unidades','Total vendido'], ';');
    elseif ($tipo === 'stock_critico') fputcsv($salida, ['Producto','Stock actual'], ';');
    else fputcsv($salida, ['Cliente','Pedidos','Total comprado'], ';');
    foreach ($filas as $fila) {
        if ($tipo === 'ventas_categoria') fputcsv($salida, [$fila['categoria'], $fila['unidades'], $fila['total_vendido']], ';');
        elseif ($tipo === 'stock_critico') fputcsv($salida, [$fila['nombre'], $fila['stock']], ';');
        else fputcsv($salida, [$fila['cliente'], $fila['pedidos'], $fila['total_comprado']], ';');
    }
    fclose($salida); exit;
}
$tituloPagina = 'Reportes';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido"><article class="reporte" aria-labelledby="titulo-reporte">
<header class="reporte__encabezado"><img class="reporte__logo" src="assets/img/logo.png" alt="Logo de ZDTecnoc"><div class="reporte__marca"><h1 id="titulo-reporte">ZDTecnoc</h1><p>Sistema de gestión de tienda tecnológica · v1.0</p><p>Reporte de uso interno</p></div><div class="reporte__meta"><p>Generado: <?= date('d/m/Y H:i') ?></p><p>Usuario: <?= htmlspecialchars($_SESSION['usuario']['nombre'], ENT_QUOTES, 'UTF-8') ?></p><p>Rol: <?= htmlspecialchars($_SESSION['usuario']['rol'], ENT_QUOTES, 'UTF-8') ?></p></div></header>
<form class="reporte__filtros no-imprimir" method="get"><div class="form-group"><label for="tipo">Reporte</label><select class="form-input" id="tipo" name="tipo"><option value="ventas_categoria" <?= $tipo==='ventas_categoria'?'selected':'' ?>>Ventas por categoría</option><option value="stock_critico" <?= $tipo==='stock_critico'?'selected':'' ?>>Inventario crítico</option><option value="pedidos_cliente" <?= $tipo==='pedidos_cliente'?'selected':'' ?>>Pedidos por cliente</option></select></div><div class="form-group"><label for="desde">Desde</label><input class="form-input" id="desde" name="desde" type="date" value="<?= htmlspecialchars($desde, ENT_QUOTES, 'UTF-8') ?>"></div><div class="form-group"><label for="hasta">Hasta</label><input class="form-input" id="hasta" name="hasta" type="date" value="<?= htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8') ?>"></div><button class="btn-primary" type="submit">Filtrar</button></form>
<div class="reporte__acciones no-imprimir"><button class="boton-accion boton-editar" type="button" onclick="window.print()">Imprimir / Guardar PDF</button><a class="boton-accion boton-editar" href="?tipo=<?= urlencode($tipo) ?>&desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>&exportar=csv">Exportar CSV</a></div>
<h2><?= htmlspecialchars($titulos[$tipo], ENT_QUOTES, 'UTF-8') ?></h2><p>Filtros: <?= htmlspecialchars($desde, ENT_QUOTES, 'UTF-8') ?> al <?= htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8') ?></p>
<table class="reporte__tabla"><thead><tr><?php if($tipo==='ventas_categoria'): ?><th>Categoría</th><th>Unidades</th><th>Total vendido</th><?php elseif($tipo==='stock_critico'): ?><th>Producto</th><th>Stock actual</th><?php else: ?><th>Cliente</th><th>Pedidos</th><th>Total comprado</th><?php endif; ?></tr></thead><tbody><?php foreach($filas as $fila): ?><tr><?php if($tipo==='ventas_categoria'): ?><td><?= htmlspecialchars($fila['categoria'], ENT_QUOTES, 'UTF-8') ?></td><td><?= (int)$fila['unidades'] ?></td><td>$ <?= number_format((float)$fila['total_vendido'],0,',','.') ?></td><?php elseif($tipo==='stock_critico'): ?><td><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td><td><?= (int)$fila['stock'] ?></td><?php else: ?><td><?= htmlspecialchars($fila['cliente'], ENT_QUOTES, 'UTF-8') ?></td><td><?= (int)$fila['pedidos'] ?></td><td>$ <?= number_format((float)$fila['total_comprado'],0,',','.') ?></td><?php endif; ?></tr><?php endforeach; ?></tbody><tfoot><tr><td>Total general</td><td><?= $totalSecundario ?></td><?php if($tipo!=='stock_critico'): ?><td>$ <?= number_format($totalGeneral,0,',','.') ?></td><?php endif; ?></tr></tfoot></table><footer class="reporte__pie">Documento generado automáticamente por ZDTecnoc. Información de uso interno.</footer>
</article></main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
