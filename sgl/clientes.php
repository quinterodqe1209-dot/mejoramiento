<?php
declare(strict_types=1);

require_once __DIR__ . '/sgl/guardia.php';
require_once __DIR__ . '/sgl/conexion.php';
require_once __DIR__ . '/sgl/csrf.php';
require_once __DIR__ . '/app/modelos/ClienteModelo.php';

exigirRol('administrador', 'vendedor');
$modelo = new ClienteModelo($conexion);
$errores = [];
$busqueda = trim((string)($_GET['busqueda'] ?? ''));
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$datos = ['id' => 0, 'nombre' => '', 'documento' => '', 'telefono' => '', 'correo' => '', 'direccion' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrf($_POST['csrf'] ?? null)) {
        http_response_code(419);
        exit('Solicitud no válida.');
    }
    $accion = (string)($_POST['accion'] ?? 'guardar');
    $id = (int)($_POST['id'] ?? 0);
    if ($accion === 'eliminar') {
        $modelo->eliminar($id);
        $_SESSION['aviso'] = 'Cliente eliminado correctamente.';
        header('Location: clientes.php', true, 303);
        exit;
    }
    $datos = [
        'id' => $id,
        'nombre' => trim((string)($_POST['nombre'] ?? '')),
        'documento' => trim((string)($_POST['documento'] ?? '')),
        'telefono' => trim((string)($_POST['telefono'] ?? '')),
        'correo' => trim((string)($_POST['correo'] ?? '')),
        'direccion' => trim((string)($_POST['direccion'] ?? '')),
    ];
    if (mb_strlen($datos['nombre']) < 3) $errores[] = 'El nombre debe tener al menos 3 caracteres.';
    if ($datos['documento'] === '') $errores[] = 'El documento es obligatorio.';
    if ($datos['correo'] !== '' && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) $errores[] = 'El correo no es válido.';
    if (!$errores) {
        try {
            $parametros = [
                ':nombre' => $datos['nombre'], ':documento' => $datos['documento'],
                ':telefono' => $datos['telefono'], ':correo' => $datos['correo'] ?: null,
                ':direccion' => $datos['direccion'],
            ];
            if ($id > 0) {
                $modelo->actualizar($id, $parametros);
                $_SESSION['aviso'] = 'Cliente actualizado correctamente.';
            } else {
                $modelo->crear($parametros);
                $_SESSION['aviso'] = 'Cliente creado correctamente.';
            }
            header('Location: clientes.php', true, 303);
            exit;
        } catch (PDOException $excepcion) {
            $errores[] = 'El documento ya está registrado.';
        }
    }
} elseif (isset($_GET['editar'])) {
    $cliente = $modelo->obtener((int)$_GET['editar']);
    if ($cliente) $datos = $cliente;
}

$porPagina = 10;
$total = $modelo->total($busqueda);
$paginas = max(1, (int)ceil($total / $porPagina));
$pagina = min($pagina, $paginas);
$clientes = $modelo->listar($busqueda, $pagina, $porPagina);
$aviso = $_SESSION['aviso'] ?? '';
unset($_SESSION['aviso']);
$tituloPagina = 'Clientes';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <h2 class="text-lg">Gestión de clientes</h2>
    <?php if ($aviso !== ''): ?><p class="alerta alerta--exito" role="status"><?= htmlspecialchars($aviso, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <?php if ($errores): ?><div class="alerta alerta--error" role="alert"><ul><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" class="login-card" style="max-width:100%;" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int)$datos['id'] ?>"><input type="hidden" name="accion" value="guardar">
        <h3><?= $datos['id'] ? 'Editar cliente' : 'Registrar cliente' ?></h3>
        <div class="form-group"><label for="nombre">Nombre</label><input class="form-input" id="nombre" name="nombre" value="<?= htmlspecialchars((string)$datos['nombre'], ENT_QUOTES, 'UTF-8') ?>" required></div>
        <div class="form-group"><label for="documento">Documento</label><input class="form-input" id="documento" name="documento" value="<?= htmlspecialchars((string)$datos['documento'], ENT_QUOTES, 'UTF-8') ?>" required></div>
        <div class="form-group"><label for="telefono">Teléfono</label><input class="form-input" id="telefono" name="telefono" value="<?= htmlspecialchars((string)$datos['telefono'], ENT_QUOTES, 'UTF-8') ?>"></div>
        <div class="form-group"><label for="correo">Correo</label><input class="form-input" id="correo" name="correo" type="email" value="<?= htmlspecialchars((string)$datos['correo'], ENT_QUOTES, 'UTF-8') ?>"></div>
        <div class="form-group"><label for="direccion">Dirección</label><input class="form-input" id="direccion" name="direccion" value="<?= htmlspecialchars((string)$datos['direccion'], ENT_QUOTES, 'UTF-8') ?>"></div>
        <button class="btn-primary" type="submit"><?= $datos['id'] ? 'Actualizar' : 'Guardar' ?></button>
    </form>
    <h3>Listado</h3>
    <form method="get"><label for="busqueda">Buscar cliente</label><input class="form-input" id="busqueda" name="busqueda" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"><button class="btn-primary" type="submit">Buscar</button></form>
    <div class="table-container"><table class="products-table"><caption>Clientes registrados</caption><thead><tr><th scope="col">Nombre</th><th scope="col">Documento</th><th scope="col">Correo</th><th scope="col">Teléfono</th><th scope="col">Acciones</th></tr></thead><tbody>
    <?php foreach ($clientes as $cliente): ?><tr><td><?= htmlspecialchars($cliente['nombre'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$cliente['documento'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$cliente['correo'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$cliente['telefono'], ENT_QUOTES, 'UTF-8') ?></td><td><a href="?editar=<?= (int)$cliente['id'] ?>">Editar</a><form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$cliente['id'] ?>"><button type="submit">Eliminar</button></form></td></tr><?php endforeach; ?></tbody></table></div>
    <nav aria-label="Paginación"><p>Página <?= $pagina ?> de <?= $paginas ?></p><?php if ($pagina > 1): ?><a href="?pagina=<?= $pagina - 1 ?>&busqueda=<?= urlencode($busqueda) ?>">Anterior</a><?php endif; ?> <?php if ($pagina < $paginas): ?><a href="?pagina=<?= $pagina + 1 ?>&busqueda=<?= urlencode($busqueda) ?>">Siguiente</a><?php endif; ?></nav>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
