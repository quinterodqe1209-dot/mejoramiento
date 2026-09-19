<?php
declare(strict_types=1);

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/sesion.php';

iniciarSesionSegura();
$rutaBase = basename(dirname($_SERVER['SCRIPT_NAME'] ?? '')) === 'sgl' ? '../' : '';
$mensaje = '';
$correo = '';

function intentosRecientes(PDO $pdo, string $correo): int
{
    $st = $pdo->prepare(
        'SELECT intentos FROM intentos_acceso
         WHERE correo = :correo AND ultimo_intento >= (NOW() - INTERVAL 15 MINUTE)'
    );
    $st->execute([':correo' => $correo]);
    return (int)($st->fetchColumn() ?: 0);
}

function registrarIntentoFallido(PDO $pdo, string $correo): int
{
    $st = $pdo->prepare(
        'INSERT INTO intentos_acceso (correo, intentos, ultimo_intento)
         VALUES (:correo, 1, NOW())
         ON DUPLICATE KEY UPDATE
            intentos = IF(ultimo_intento < (NOW() - INTERVAL 15 MINUTE), 1, intentos + 1),
            ultimo_intento = NOW()'
    );
    $st->execute([':correo' => $correo]);
    return intentosRecientes($pdo, $correo);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim((string)($_POST['correo'] ?? ''));
    $clave = (string)($_POST['clave'] ?? '');

    if (!validarCsrf($_POST['csrf'] ?? null)) {
        http_response_code(419);
        $mensaje = 'La solicitud no es válida. Recarga el formulario e inténtalo de nuevo.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($clave) < 8) {
        $mensaje = 'Correo o contraseña incorrectos.';
    } else {
        $st = $conexion->prepare(
            'SELECT id, nombre, clave_hash, rol, activo, bloqueado_hasta
             FROM usuarios WHERE correo = :correo LIMIT 1'
        );
        $st->execute([':correo' => $correo]);
        $usuario = $st->fetch();
        $bloqueada = $usuario
            && $usuario['bloqueado_hasta'] !== null
            && strtotime((string)$usuario['bloqueado_hasta']) > time();

        if ($bloqueada) {
            $mensaje = 'Cuenta bloqueada temporalmente. Inténtalo más tarde.';
        } elseif ($usuario && (int)$usuario['activo'] === 1
            && password_verify($clave, (string)$usuario['clave_hash'])) {
            abrirSesion($usuario);
            $conexion->prepare('DELETE FROM intentos_acceso WHERE correo = :correo')
                ->execute([':correo' => $correo]);
            header('Location: ' . $rutaBase . 'dashboard.php', true, 303);
            exit;
        } else {
            $intentos = registrarIntentoFallido($conexion, $correo);
            if ($usuario && $intentos >= 5) {
                $conexion->prepare(
                    'UPDATE usuarios SET bloqueado_hasta = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                     WHERE id = :id'
                )->execute([':id' => $usuario['id']]);
            }
            $mensaje = 'Correo o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | ZDTecnoc</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($rutaBase, ENT_QUOTES, 'UTF-8') ?>css/estilos.css">
</head>
<body>
    <main class="login-container">
        <section class="login-card" aria-labelledby="titulo-login">
            <header class="login-header">
                <span class="brand-name text-xl">ZDTecnoc</span>
                <h1 id="titulo-login" class="text-lg">Iniciar sesión</h1>
                <p class="text-sm">Panel de gestión de tienda tecnológica</p>
            </header>
            <?php if ($mensaje !== ''): ?>
                <p class="alerta alerta--error" role="alert">
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>
            <form action="login.php" method="post" autocomplete="on">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" class="form-input"
                           value="<?= htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') ?>"
                           required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="clave">Contraseña</label>
                    <input type="password" id="clave" name="clave" class="form-input"
                           required minlength="8" autocomplete="current-password">
                </div>
                <button type="submit" class="btn-primary">Ingresar</button>
            </form>
        </section>
    </main>
</body>
</html>
