USE zdtecnoc_db;

CREATE OR REPLACE VIEW v_ventas_mes AS
SELECT
    DATE_FORMAT(p.fecha_pedido, '%Y-%m') AS periodo,
    COUNT(DISTINCT p.id) AS pedidos,
    SUM(d.cantidad) AS unidades,
    SUM(d.cantidad * d.precio_unitario) AS total_vendido
FROM pedidos p
INNER JOIN detalle_pedidos d ON d.pedido_id = p.id
GROUP BY DATE_FORMAT(p.fecha_pedido, '%Y-%m');

CREATE OR REPLACE VIEW v_ventas_categoria AS
SELECT
    c.id AS categoria_id,
    c.nombre AS categoria,
    COALESCE(SUM(d.cantidad), 0) AS unidades,
    COALESCE(SUM(d.cantidad * d.precio_unitario), 0) AS total_vendido
FROM categorias c
LEFT JOIN productos pr ON pr.categoria_id = c.id
LEFT JOIN detalle_pedidos d ON d.producto_id = pr.id
GROUP BY c.id, c.nombre;

CREATE OR REPLACE VIEW v_stock_critico AS
SELECT id, nombre, stock
FROM productos
WHERE activo = 1 AND stock < 5;

CREATE OR REPLACE VIEW v_clientes_mayor_compra AS
SELECT
    c.id AS cliente_id,
    c.nombre AS cliente,
    COUNT(DISTINCT p.id) AS pedidos,
    COALESCE(SUM(p.total), 0) AS total_comprado
FROM clientes c
LEFT JOIN pedidos p ON p.cliente_id = c.id
GROUP BY c.id, c.nombre;

CREATE OR REPLACE VIEW v_ventas_categoria_mes AS
SELECT
    DATE_FORMAT(p.fecha_pedido, '%Y-%m') AS periodo,
    c.id AS categoria_id,
    c.nombre AS categoria,
    SUM(d.cantidad) AS unidades,
    SUM(d.cantidad * d.precio_unitario) AS total_vendido
FROM pedidos p
INNER JOIN detalle_pedidos d ON d.pedido_id = p.id
INNER JOIN productos pr ON pr.id = d.producto_id
INNER JOIN categorias c ON c.id = pr.categoria_id
GROUP BY DATE_FORMAT(p.fecha_pedido, '%Y-%m'), c.id, c.nombre;

CREATE OR REPLACE VIEW v_clientes_mayor_compra_mes AS
SELECT
    DATE_FORMAT(p.fecha_pedido, '%Y-%m') AS periodo,
    c.id AS cliente_id,
    c.nombre AS cliente,
    SUM(p.total) AS total_comprado
FROM pedidos p
INNER JOIN clientes c ON c.id = p.cliente_id
GROUP BY DATE_FORMAT(p.fecha_pedido, '%Y-%m'), c.id, c.nombre;
