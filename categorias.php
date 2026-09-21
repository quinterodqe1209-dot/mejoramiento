<?php
declare(strict_types=1);

require_once __DIR__ . '/sgl/guardia.php';
require_once __DIR__ . '/sgl/conexion.php';
require_once __DIR__ . '/sgl/csrf.php';
require_once __DIR__ . '/app/modelos/CategoriaModelo.php';

exigirRol('administrador', 'vendedor');
$modelo = new CategoriaModelo($conexion);
$errores = [];
$busqueda = trim((string)($_GET['busqueda'] ?? ''));
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$datos = ['id' => 0, 'nombre' => '', 'descripcion' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrf($_POST['csrf'] ?? null)) {
        http_response_code(419);
        exit('Solicitud no válida.');
    }
    $accion = (string)($_POST['accion'] ?? 'guardar');
    $id = (int)($_POST['id'] ?? 0);

    if ($accion === 'eliminar') {
        try {
            $modelo->eliminar($id);
            $_SESSION['aviso'] = 'Categoría eliminada correctamente.';
        } catch (PDOException $e) {
            $_SESSION['aviso_error'] = 'No se puede eliminar: tiene productos asociados.';
        }
        header('Location: categorias.php', true, 303);
        exit;
    }

    $datos = [
        'id'          => $id,
        'nombre'      => trim((string)($_POST['nombre'] ?? '')),
        'descripcion' => trim((string)($_POST['descripcion'] ?? '')),
    ];

    if (mb_strlen($datos['nombre']) < 2) {
        $errores[] = 'El nombre debe tener al menos 2 caracteres.';
    }

    if (!$errores) {
        $parametros = [
            ':nombre'      => $datos['nombre'],
            ':descripcion' => $datos['descripcion'] ?: null,
        ];
        if ($id > 0) {
            $modelo->actualizar($id, $parametros);
            $_SESSION['aviso'] = 'Categoría actualizada correctamente.';
        } else {
            $modelo->crear($parametros);
            $_SESSION['aviso'] = 'Categoría creada correctamente.';
        }
        header('Location: categorias.php', true, 303);
        exit;
    }
} elseif (isset($_GET['editar'])) {
    $cat = $modelo->obtener((int)$_GET['editar']);
    if ($cat) {
        $datos = $cat;
    }
}

$porPagina = 10;
$total     = $modelo->total($busqueda);
$paginas   = max(1, (int)ceil($total / $porPagina));
$pagina    = min($pagina, $paginas);
$categorias = $modelo->listar($busqueda, $pagina, $porPagina);
$aviso      = $_SESSION['aviso'] ?? '';
$avisoError = $_SESSION['aviso_error'] ?? '';
unset($_SESSION['aviso'], $_SESSION['aviso_error']);
$tituloPagina = 'Categorías';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <section aria-labelledby="titulo-categorias">
        <h2 id="titulo-categorias" class="text-lg">Gestión de categorías</h2>
        <?php if ($aviso !== ''): ?><p class="alerta alerta--exito" role="status"><?= htmlspecialchars($aviso, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php if ($avisoError !== ''): ?><p class="alerta alerta--error" role="alert"><?= htmlspecialchars($avisoError, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php if ($errores): ?><div class="alerta alerta--error" role="alert"><ul><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" class="login-card" style="max-width:100%;" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= (int)$datos['id'] ?>">
            <input type="hidden" name="accion" value="guardar">
            <h3><?= $datos['id'] ? 'Editar categoría' : 'Registrar categoría' ?></h3>
            <div class="form-group"><label for="nombre">Nombre</label><input class="form-input" id="nombre" name="nombre" value="<?= htmlspecialchars((string)$datos['nombre'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="form-group"><label for="descripcion">Descripción</label><textarea class="form-input" id="descripcion" name="descripcion" rows="2"><?= htmlspecialchars((string)($datos['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></div>
            <button class="btn-primary" type="submit"><?= $datos['id'] ? 'Actualizar' : 'Guardar' ?></button>
            <?php if ($datos['id']): ?><a class="boton-accion" href="categorias.php">Cancelar</a><?php endif; ?>
        </form>
    </section>
    <section aria-labelledby="lista-categorias">
        <h3 id="lista-categorias">Listado</h3>
        <form method="get"><label for="busqueda">Buscar categoría</label><input class="form-input" id="busqueda" name="busqueda" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"><button class="btn-primary" type="submit">Buscar</button></form>
        <div class="table-container"><table class="products-table"><caption>Categorías registradas</caption><thead><tr><th scope="col">Nombre</th><th scope="col">Descripción</th><th scope="col">Acciones</th></tr></thead><tbody>
        <?php foreach ($categorias as $cat): ?><tr><td><?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)($cat['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td><td class="acciones-crud"><a class="boton-accion boton-editar" href="?editar=<?= (int)$cat['id'] ?>">Editar</a><form method="post" style="display:inline" onsubmit="return confirm('¿Eliminar esta categoría?');"><input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$cat['id'] ?>"><button class="boton-accion boton-eliminar" type="submit">Eliminar</button></form></td></tr><?php endforeach; ?>
        </tbody></table></div>
        <nav aria-label="Paginación"><p>Página <?= $pagina ?> de <?= $paginas ?></p><?php if ($pagina > 1): ?><a href="?pagina=<?= $pagina - 1 ?>&busqueda=<?= urlencode($busqueda) ?>">Anterior</a><?php endif; ?> <?php if ($pagina < $paginas): ?><a href="?pagina=<?= $pagina + 1 ?>&busqueda=<?= urlencode($busqueda) ?>">Siguiente</a><?php endif; ?></nav>
    </section>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
