<?php
declare(strict_types=1);

require_once __DIR__ . '/sgl/guardia.php';
require_once __DIR__ . '/sgl/conexion.php';
require_once __DIR__ . '/sgl/csrf.php';
require_once __DIR__ . '/app/modelos/ProductoModelo.php';

exigirRol('administrador', 'vendedor');
$modelo = new ProductoModelo($conexion);
$errores = [];
$busqueda = trim((string)($_GET['busqueda'] ?? ''));
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$datosFormulario = [
    'id' => 0,
    'nombre' => '',
    'categoria_id' => '',
    'precio' => '',
    'stock' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrf($_POST['csrf'] ?? null)) {
        http_response_code(419);
        exit('Solicitud no válida.');
    }

    $accion = (string)($_POST['accion'] ?? 'guardar');
    $id = (int)($_POST['id'] ?? 0);

    if ($accion === 'eliminar') {
        $modelo->desactivar($id);
        $_SESSION['aviso'] = 'Producto desactivado correctamente.';
        header('Location: productos.php', true, 303);
        exit;
    }

    $datosFormulario = [
        'id' => $id,
        'nombre' => trim((string)($_POST['nombre'] ?? '')),
        'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
        'precio' => (string)($_POST['precio'] ?? ''),
        'stock' => (string)($_POST['stock'] ?? ''),
    ];
    $precio = filter_var($datosFormulario['precio'], FILTER_VALIDATE_FLOAT);
    $stock = filter_var($datosFormulario['stock'], FILTER_VALIDATE_INT);

    if (mb_strlen($datosFormulario['nombre']) < 3) {
        $errores[] = 'El nombre debe tener al menos 3 caracteres.';
    }
    if ($precio === false || $precio <= 0) {
        $errores[] = 'El precio debe ser mayor que cero.';
    }
    if ($stock === false || $stock < 0) {
        $errores[] = 'El stock debe ser un entero no negativo.';
    }
    if ($datosFormulario['categoria_id'] < 1) {
        $errores[] = 'Seleccione una categoría.';
    }

    if (!$errores) {
        $datos = [
            ':nombre' => $datosFormulario['nombre'],
            ':categoria' => $datosFormulario['categoria_id'],
            ':precio' => $precio,
            ':stock' => $stock,
        ];
        if ($id > 0) {
            $modelo->actualizar($id, $datos);
            $_SESSION['aviso'] = 'Producto actualizado correctamente.';
        } else {
            $modelo->crear($datos);
            $_SESSION['aviso'] = 'Producto creado correctamente.';
        }
        header('Location: productos.php', true, 303);
        exit;
    }
} elseif (isset($_GET['editar'])) {
    $producto = $modelo->obtener((int)$_GET['editar']);
    if ($producto) {
        $datosFormulario = [
            'id' => (int)$producto['id'],
            'nombre' => $producto['nombre'],
            'categoria_id' => (int)$producto['categoria_id'],
            'precio' => $producto['precio'],
            'stock' => $producto['stock'],
        ];
    }
}

$porPagina = 10;
$total = $modelo->total($busqueda);
$paginas = max(1, (int)ceil($total / $porPagina));
$pagina = min($pagina, $paginas);
$productos = $modelo->listar($busqueda, $pagina, $porPagina);
$categorias = $modelo->categorias();
$aviso = $_SESSION['aviso'] ?? '';
unset($_SESSION['aviso']);
$tituloPagina = 'Productos';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <section aria-labelledby="titulo-productos">
        <h2 id="titulo-productos" class="text-lg">Gestión de productos</h2>
        <?php if ($aviso !== ''): ?><p class="alerta alerta--exito" role="status"><?= htmlspecialchars($aviso, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php if ($errores): ?><div class="alerta alerta--error" role="alert"><ul><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" class="login-card" style="max-width:100%;" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= (int)$datosFormulario['id'] ?>">
            <input type="hidden" name="accion" value="guardar">
            <h3><?= $datosFormulario['id'] ? 'Editar producto' : 'Registrar producto' ?></h3>
            <div class="form-group"><label for="nombre">Nombre</label><input class="form-input" id="nombre" name="nombre" value="<?= htmlspecialchars((string)$datosFormulario['nombre'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="form-group"><label for="categoria_id">Categoría</label><select class="form-input" id="categoria_id" name="categoria_id" required><option value="">Seleccione</option><?php foreach ($categorias as $categoria): ?><option value="<?= (int)$categoria['id'] ?>" <?= (int)$datosFormulario['categoria_id'] === (int)$categoria['id'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label for="precio">Precio</label><input class="form-input" id="precio" name="precio" type="number" min="0.01" step="0.01" value="<?= htmlspecialchars((string)$datosFormulario['precio'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="form-group"><label for="stock">Stock</label><input class="form-input" id="stock" name="stock" type="number" min="0" step="1" value="<?= htmlspecialchars((string)$datosFormulario['stock'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <button class="btn-primary" type="submit"><?= $datosFormulario['id'] ? 'Actualizar' : 'Guardar' ?></button>
        </form>
    </section>
    <section aria-labelledby="lista-productos">
        <h3 id="lista-productos">Listado</h3>
        <form method="get"><label for="busqueda">Buscar producto</label><input class="form-input" id="busqueda" name="busqueda" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"><button class="btn-primary" type="submit">Buscar</button></form>
        <div class="table-container"><table class="products-table"><caption>Productos activos</caption><thead><tr><th scope="col">Nombre</th><th scope="col">Categoría</th><th scope="col">Precio</th><th scope="col">Stock</th><th scope="col">Acciones</th></tr></thead><tbody>
        <?php foreach ($productos as $producto): ?><tr><td><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($producto['categoria'], ENT_QUOTES, 'UTF-8') ?></td><td>$ <?= number_format((float)$producto['precio'], 0, ',', '.') ?></td><td><?= (int)$producto['stock'] ?></td><td class="acciones-crud"><a class="boton-accion boton-editar" href="?editar=<?= (int)$producto['id'] ?>">Editar</a><form method="post" onsubmit="return confirm('¿Deseas desactivar este producto?');"><input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$producto['id'] ?>"><button class="boton-accion boton-eliminar" type="submit">Eliminar</button></form></td></tr><?php endforeach; ?></tbody></table></div>
        <nav aria-label="Paginación"><p>Página <?= $pagina ?> de <?= $paginas ?></p><?php if ($pagina > 1): ?><a href="?pagina=<?= $pagina - 1 ?>&busqueda=<?= urlencode($busqueda) ?>">Anterior</a><?php endif; ?> <?php if ($pagina < $paginas): ?><a href="?pagina=<?= $pagina + 1 ?>&busqueda=<?= urlencode($busqueda) ?>">Siguiente</a><?php endif; ?></nav>
    </section>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
