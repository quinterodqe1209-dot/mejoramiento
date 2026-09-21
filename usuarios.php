<?php
declare(strict_types=1);

require_once __DIR__ . '/sgl/guardia.php';
require_once __DIR__ . '/sgl/conexion.php';
require_once __DIR__ . '/sgl/csrf.php';
require_once __DIR__ . '/app/modelos/UsuarioModelo.php';

exigirRol('administrador');
$modelo = new UsuarioModelo($conexion);
$errores = [];
$busqueda = trim((string)($_GET['busqueda'] ?? ''));
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$datos = ['id' => 0, 'nombre' => '', 'correo' => '', 'rol' => 'consultor', 'activo' => 1];

$rolesPermitidos = ['administrador', 'vendedor', 'consultor'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrf($_POST['csrf'] ?? null)) {
        http_response_code(419);
        exit('Solicitud no válida.');
    }
    $accion = (string)($_POST['accion'] ?? 'guardar');
    $id = (int)($_POST['id'] ?? 0);

    if ($accion === 'desactivar') {
        // Evitar que el administrador se desactive a sí mismo
        if ($id === (int)($_SESSION['usuario']['id'] ?? 0)) {
            $_SESSION['aviso_error'] = 'No puedes desactivar tu propia cuenta.';
        } else {
            $modelo->desactivar($id);
            $_SESSION['aviso'] = 'Usuario desactivado correctamente.';
        }
        header('Location: usuarios.php', true, 303);
        exit;
    }

    $datos = [
        'id'     => $id,
        'nombre' => trim((string)($_POST['nombre'] ?? '')),
        'correo' => trim((string)($_POST['correo'] ?? '')),
        'rol'    => (string)($_POST['rol'] ?? 'consultor'),
        'activo' => (int)($_POST['activo'] ?? 1),
    ];
    $clave = trim((string)($_POST['clave'] ?? ''));

    if (mb_strlen($datos['nombre']) < 3) {
        $errores[] = 'El nombre debe tener al menos 3 caracteres.';
    }
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo no es válido.';
    }
    if (!in_array($datos['rol'], $rolesPermitidos, true)) {
        $errores[] = 'Rol no permitido.';
    }
    if ($id === 0 && mb_strlen($clave) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($clave !== '' && mb_strlen($clave) < 8) {
        $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
    }
    if ($modelo->correoExiste($datos['correo'], $id)) {
        $errores[] = 'El correo ya está registrado por otro usuario.';
    }

    if (!$errores) {
        if ($id > 0) {
            $modelo->actualizar($id, [
                ':nombre'  => $datos['nombre'],
                ':correo'  => $datos['correo'],
                ':rol'     => $datos['rol'],
                ':activo'  => $datos['activo'],
            ]);
            if ($clave !== '') {
                $modelo->cambiarClave($id, password_hash($clave, PASSWORD_DEFAULT));
            }
            $_SESSION['aviso'] = 'Usuario actualizado correctamente.';
        } else {
            $modelo->crear([
                ':nombre'     => $datos['nombre'],
                ':correo'     => $datos['correo'],
                ':clave_hash' => password_hash($clave, PASSWORD_DEFAULT),
                ':rol'        => $datos['rol'],
            ]);
            $_SESSION['aviso'] = 'Usuario creado correctamente.';
        }
        header('Location: usuarios.php', true, 303);
        exit;
    }
} elseif (isset($_GET['editar'])) {
    $usuario = $modelo->obtener((int)$_GET['editar']);
    if ($usuario) {
        $datos = $usuario;
    }
}

$porPagina = 10;
$total     = $modelo->total($busqueda);
$paginas   = max(1, (int)ceil($total / $porPagina));
$pagina    = min($pagina, $paginas);
$usuarios   = $modelo->listar($busqueda, $pagina, $porPagina);
$aviso      = $_SESSION['aviso'] ?? '';
$avisoError = $_SESSION['aviso_error'] ?? '';
unset($_SESSION['aviso'], $_SESSION['aviso_error']);
$tituloPagina = 'Usuarios';
require __DIR__ . '/app/vistas/parciales/cabecera.php';
require __DIR__ . '/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
    <section aria-labelledby="titulo-usuarios">
        <h2 id="titulo-usuarios" class="text-lg">Gestión de usuarios</h2>
        <?php if ($aviso !== ''): ?><p class="alerta alerta--exito" role="status"><?= htmlspecialchars($aviso, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php if ($avisoError !== ''): ?><p class="alerta alerta--error" role="alert"><?= htmlspecialchars($avisoError, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php if ($errores): ?><div class="alerta alerta--error" role="alert"><ul><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" class="login-card" style="max-width:100%;" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= (int)$datos['id'] ?>">
            <input type="hidden" name="accion" value="guardar">
            <h3><?= $datos['id'] ? 'Editar usuario' : 'Registrar usuario' ?></h3>
            <div class="form-group"><label for="nombre">Nombre</label><input class="form-input" id="nombre" name="nombre" value="<?= htmlspecialchars((string)$datos['nombre'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="form-group"><label for="correo">Correo</label><input class="form-input" id="correo" name="correo" type="email" value="<?= htmlspecialchars((string)$datos['correo'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="form-group"><label for="clave">Contraseña <?= $datos['id'] ? '(dejar vacío para no cambiar)' : '(mínimo 8 caracteres)' ?></label><input class="form-input" id="clave" name="clave" type="password" autocomplete="new-password" <?= $datos['id'] ? '' : 'required' ?>></div>
            <div class="form-group"><label for="rol">Rol</label><select class="form-input" id="rol" name="rol" required><option value="administrador"<?= ($datos['rol'] ?? '') === 'administrador' ? ' selected' : '' ?>>Administrador</option><option value="vendedor"<?= ($datos['rol'] ?? '') === 'vendedor' ? ' selected' : '' ?>>Vendedor</option><option value="consultor"<?= ($datos['rol'] ?? '') === 'consultor' ? ' selected' : '' ?>>Consultor</option></select></div>
            <?php if ($datos['id']): ?>
            <div class="form-group"><label for="activo">Estado</label><select class="form-input" id="activo" name="activo"><option value="1"<?= (int)($datos['activo'] ?? 1) === 1 ? ' selected' : '' ?>>Activo</option><option value="0"<?= (int)($datos['activo'] ?? 1) === 0 ? ' selected' : '' ?>>Inactivo</option></select></div>
            <?php endif; ?>
            <button class="btn-primary" type="submit"><?= $datos['id'] ? 'Actualizar' : 'Guardar' ?></button>
            <?php if ($datos['id']): ?><a class="boton-accion" href="usuarios.php">Cancelar</a><?php endif; ?>
        </form>
    </section>
    <section aria-labelledby="lista-usuarios">
        <h3 id="lista-usuarios">Listado</h3>
        <form method="get"><label for="busqueda">Buscar usuario</label><input class="form-input" id="busqueda" name="busqueda" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"><button class="btn-primary" type="submit">Buscar</button></form>
        <div class="table-container"><table class="products-table"><caption>Usuarios del sistema</caption><thead><tr><th scope="col">Nombre</th><th scope="col">Correo</th><th scope="col">Rol</th><th scope="col">Estado</th><th scope="col">Acciones</th></tr></thead><tbody>
        <?php foreach ($usuarios as $u): ?><tr><td><?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($u['rol'], ENT_QUOTES, 'UTF-8') ?></td><td><?= (int)$u['activo'] ? 'Activo' : 'Inactivo' ?></td><td class="acciones-crud"><a class="boton-accion boton-editar" href="?editar=<?= (int)$u['id'] ?>">Editar</a><?php if ((int)$u['id'] !== (int)($_SESSION['usuario']['id'] ?? 0) && (int)$u['activo']): ?><form method="post" style="display:inline" onsubmit="return confirm('¿Desactivar este usuario?');"><input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="accion" value="desactivar"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="boton-accion boton-eliminar" type="submit">Desactivar</button></form><?php endif; ?></td></tr><?php endforeach; ?>
        </tbody></table></div>
        <nav aria-label="Paginación"><p>Página <?= $pagina ?> de <?= $paginas ?></p><?php if ($pagina > 1): ?><a href="?pagina=<?= $pagina - 1 ?>&busqueda=<?= urlencode($busqueda) ?>">Anterior</a><?php endif; ?> <?php if ($pagina < $paginas): ?><a href="?pagina=<?= $pagina + 1 ?>&busqueda=<?= urlencode($busqueda) ?>">Siguiente</a><?php endif; ?></nav>
    </section>
</main>
<?php require __DIR__ . '/app/vistas/parciales/pie.php'; ?>
