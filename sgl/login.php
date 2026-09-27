<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/csrf.php';

iniciarSesionSegura();

if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php', true, 303);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave = $_POST['clave'] ?? '';
    $csrf = $_POST['csrf'] ?? '';

    if (!validarCsrf($csrf)) {
        http_response_code(419);
        exit('Token CSRF inválido.');
    }

    if ($correo === '' || $clave === '') {
        $error = 'Por favor, completa todos los campos.';
    } else {
        // Consultar intentos de acceso
        $st = $conexion->prepare('SELECT intentos, ultimo_intento FROM intentos_acceso WHERE correo = ?');
        $st->execute([$correo]);
        $intento = $st->fetch();

        if ($intento && (int)$intento['intentos'] >= 5) {
            $tiempo_pasado = time() - strtotime($intento['ultimo_intento']);
            if ($tiempo_pasado < 900) {
                $error = 'Cuenta bloqueada temporalmente. Intenta en ' . ceil((900 - $tiempo_pasado) / 60) . ' minutos.';
            } else {
                $conexion->prepare('UPDATE intentos_acceso SET intentos = 0 WHERE correo = ?')->execute([$correo]);
                $intento['intentos'] = 0;
            }
        }

        if ($error === '') {
            $st = $conexion->prepare('SELECT id, nombre, clave_hash, rol, activo FROM usuarios WHERE correo = ?');
            $st->execute([$correo]);
            $usuario = $st->fetch();

            if ($usuario && (int)$usuario['activo'] === 1 && password_verify($clave, $usuario['clave_hash'])) {
                $conexion->prepare('DELETE FROM intentos_acceso WHERE correo = ?')->execute([$correo]);
                abrirSesion($usuario);
                header('Location: dashboard.php', true, 303);
                exit;
            } else {
                $error = 'Credenciales incorrectas.';
                $conexion->prepare('
                    INSERT INTO intentos_acceso (correo, intentos, ultimo_intento) 
                    VALUES (?, 1, NOW()) 
                    ON DUPLICATE KEY UPDATE intentos = intentos + 1, ultimo_intento = NOW()
                ')->execute([$correo]);
            }
        }
    }
}
