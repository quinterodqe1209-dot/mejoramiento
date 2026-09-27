<?php
declare(strict_types=1);

require_once __DIR__ . '/sgl/guardia.php';
require_once __DIR__ . '/sgl/conexion.php';
require_once __DIR__ . '/sgl/csrf.php';

exigirRol('administrador', 'vendedor');
$errores = [];
$aviso = $_SESSION['aviso'] ?? '';
unset($_SESSION['aviso']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrf($_POST['csrf'] ?? null)) {
        http_response_code(419);
        exit('Solicitud no válida.');
    }

    $clienteId = (int)($_POST['cliente_id'] ?? 0);
    $productoId = (int)($_POST['producto_id'] ?? 0);
    $cantidad = filter_var($_POST['cantidad'] ?? null, FILTER_VALIDATE_INT);

    if ($clienteId < 1) $errores[] = 'Seleccione un cliente.';
    if ($productoId < 1) $errores[] = 'Seleccione un producto.';
    if ($cantidad === false || $cantidad < 1) $errores[] = 'La cantidad debe ser un entero mayor que cero.';

    if (!$errores) {
        try {
            $conexion->beginTransaction();
            $productoSt = $conexion->prepare(
                'SELECT id, precio, stock FROM productos
                 WHERE id = :id AND activo = 1 FOR UPDATE'
            );
            $productoSt->execute([':id' => $productoId]);
            $producto = $productoSt->fetch();
            if (!$producto || (int)$producto['stock'] < $cantidad) {
                throw new RuntimeException('El producto no existe o no tiene stock suficiente.');
            }
            $total = (float)$producto['precio'] * $cantidad;
            $pedidoSt = $conexion->prepare(
                'INSERT INTO pedidos (cliente_id, usuario_id, total)
                 VALUES (:cliente, :usuario, :total)'
            );
            $pedidoSt->execute([
                ':cliente' => $clienteId,
                ':usuario' => $_SESSION['usuario']['id'],
                ':total' => $total,
            ]);
            $pedidoId = (int)$conexion->lastInsertId();
            $detalleSt = $conexion->prepare(
                'INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario)
                 VALUES (:pedido, :producto, :cantidad, :precio)'
            );
            $detalleSt->execute([
                ':pedido' => $pedidoId,
                ':producto' => $productoId,
                ':cantidad' => $cantidad,
                ':precio' => $producto['precio'],
            ]);
            $stockSt = $conexion->prepare(
                'UPDATE productos SET stock = stock - :cantidad WHERE id = :id'
            );
            $stockSt->execute([':cantidad' => $cantidad, ':id' => $productoId]);
            $conexion->commit();
            $_SESSION['aviso'] = 'Pedido registrado y stock actualizado correctamente.';
            header('Location: pedidos.php', true, 303);
            exit;
        } catch (Throwable $excepcion) {
            if ($conexion->inTransaction()) $conexion->rollBack();
            $errores[] = $excepcion->getMessage();
        }
    }
}

$clientes = $conexion->query('SELECT id, nombre FROM clientes ORDER BY nombre')->fetchAll();
$productos = $conexion->query('SELECT id, nombre, precio, stock FROM productos WHERE activo = 1 AND stock > 0 ORDER BY nombre')->fetchAll();
$pedidos = $conexion->query(
    'SELECT p.id, p.fecha_pedido, p.total, c.nombre AS cliente, u.nombre AS usuario
     FROM pedidos p INNER JOIN clientes c ON c.id = p.cliente_id
     INNER JOIN usuarios u ON u.id = p.usuario_id ORDER BY p.id DESC LIMIT 10'
)->fetchAll();
$tituloPagina = 'Pedidos';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <h2 class="text-lg">Registro de pedidos</h2>
    <?php if ($aviso !== ''): ?><p class="alerta alerta--exito" role="status"><?= htmlspecialchars($aviso, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <?php if ($errores): ?><div class="alerta alerta--error" role="alert"><ul><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="login-card" style="max-width:100%;" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-group"><label for="cliente_id">Cliente</label><select class="form-input" id="cliente_id" name="cliente_id" required><option value="">Seleccione</option><?php foreach ($clientes as $cliente): ?><option value="<?= (int)$cliente['id'] ?>"><?= htmlspecialchars($cliente['nombre'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label for="producto_id">Producto</label><select class="form-input" id="producto_id" name="producto_id" required><option value="">Seleccione</option><?php foreach ($productos as $producto): ?><option value="<?= (int)$producto['id'] ?>"><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?> - stock <?= (int)$producto['stock'] ?> - $ <?= number_format((float)$producto['precio'], 0, ',', '.') ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label for="cantidad">Cantidad</label><input class="form-input" id="cantidad" name="cantidad" type="number" min="1" step="1" required></div>
        <button class="btn-primary" type="submit">Registrar pedido</button>
    </form>
    <h3>Últimos pedidos</h3>
    <div class="table-container"><table class="products-table"><caption>Pedidos registrados</caption><thead><tr><th scope="col">ID</th><th scope="col">Cliente</th><th scope="col">Usuario</th><th scope="col">Fecha</th><th scope="col">Total</th></tr></thead><tbody><?php foreach ($pedidos as $pedido): ?><tr><td><?= (int)$pedido['id'] ?></td><td><?= htmlspecialchars($pedido['cliente'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($pedido['usuario'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($pedido['fecha_pedido'], ENT_QUOTES, 'UTF-8') ?></td><td>$ <?= number_format((float)$pedido['total'], 0, ',', '.') ?></td></tr><?php endforeach; ?></tbody></table></div>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
