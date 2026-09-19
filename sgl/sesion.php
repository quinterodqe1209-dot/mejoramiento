<?php
declare(strict_types=1);

const INACTIVIDAD_MAX = 1800;
const SESION_MAX = 28800;

function iniciarSesionSegura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']),
        'samesite' => 'Strict',
    ]);
    session_name('ZDTL_SESS');
    session_start();
}

function abrirSesion(array $usuario): void
{
    iniciarSesionSegura();
    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id' => (int)$usuario['id'],
        'nombre' => (string)$usuario['nombre'],
        'rol' => (string)$usuario['rol'],
    ];
    $_SESSION['inicio'] = time();
    $_SESSION['ultima_actividad'] = time();
    $_SESSION['huella'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
}

function cerrarSesion(): void
{
    iniciarSesionSegura();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $parametros['path'],
            'secure' => $parametros['secure'],
            'httponly' => $parametros['httponly'],
            'samesite' => $parametros['samesite'] ?? 'Strict',
        ]);
    }

    session_destroy();
}
