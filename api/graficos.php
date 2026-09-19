<?php
declare(strict_types=1);

require_once __DIR__ . '/../sgl/guardia.php';
require_once __DIR__ . '/../sgl/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$desde = (string)($_GET['desde'] ?? date('Y-01-01'));
$hasta = (string)($_GET['hasta'] ?? date('Y-m-d'));

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)
    || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)
    || $desde > $hasta) {
    http_response_code(422);
    echo json_encode(['error' => 'Rango de fechas no válido.']);
    exit;
}

$ventasSt = $conexion->prepare(
    'SELECT periodo, pedidos, unidades, total_vendido
     FROM v_ventas_mes
     WHERE periodo BETWEEN DATE_FORMAT(:desde, "%Y-%m") AND DATE_FORMAT(:hasta, "%Y-%m")
     ORDER BY periodo'
);
$ventasSt->execute([':desde' => $desde, ':hasta' => $hasta]);
$ventas = $ventasSt->fetchAll();

$categoriasSt = $conexion->prepare(
    'SELECT categoria, SUM(unidades) AS unidades, SUM(total_vendido) AS total_vendido
     FROM v_ventas_categoria_mes
     WHERE periodo BETWEEN DATE_FORMAT(:desde, "%Y-%m") AND DATE_FORMAT(:hasta, "%Y-%m")
     GROUP BY categoria_id, categoria ORDER BY total_vendido DESC'
);
$categoriasSt->execute([':desde' => $desde, ':hasta' => $hasta]);
$categorias = $categoriasSt->fetchAll();

$clientesSt = $conexion->prepare(
    'SELECT cliente, SUM(total_comprado) AS total_comprado
     FROM v_clientes_mayor_compra_mes
     WHERE periodo BETWEEN DATE_FORMAT(:desde, "%Y-%m") AND DATE_FORMAT(:hasta, "%Y-%m")
     GROUP BY cliente_id, cliente ORDER BY total_comprado DESC LIMIT 10'
);
$clientesSt->execute([':desde' => $desde, ':hasta' => $hasta]);
$clientes = $clientesSt->fetchAll();

$stockCritico = (int)$conexion->query('SELECT COUNT(*) FROM v_stock_critico')->fetchColumn();

echo json_encode([
    'ventasMes' => [
        'etiquetas' => array_column($ventas, 'periodo'),
        'pedidos' => array_map('intval', array_column($ventas, 'pedidos')),
        'valores' => array_map('floatval', array_column($ventas, 'total_vendido')),
    ],
    'categorias' => [
        'etiquetas' => array_column($categorias, 'categoria'),
        'valores' => array_map('floatval', array_column($categorias, 'total_vendido')),
    ],
    'clientes' => [
        'etiquetas' => array_column($clientes, 'cliente'),
        'valores' => array_map('floatval', array_column($clientes, 'total_comprado')),
    ],
    'stockCritico' => $stockCritico,
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
