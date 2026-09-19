<?php
declare(strict_types=1);

require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/../app/config/config.php';

iniciarSesionSegura();

$huellaActual = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
$sesionValida = !empty($_SESSION['usuario'])
    && hash_equals((string)($_SESSION['huella'] ?? ''), $huellaActual);
$ahora = time();
$expirada = empty($_SESSION['inicio'])
    || empty($_SESSION['ultima_actividad'])
    || $ahora - (int)$_SESSION['ultima_actividad'] > INACTIVIDAD_MAX
    || $ahora - (int)$_SESSION['inicio'] > SESION_MAX;

if (!$sesionValida || $expirada) {
    cerrarSesion();
    header('Location: ' . BASE_URL . 'login.php?m=requiere_ingreso', true, 303);
    exit;
}

$_SESSION['ultima_actividad'] = $ahora;

function exigirRol(string ...$roles): void
{
    if (!in_array($_SESSION['usuario']['rol'] ?? '', $roles, true)) {
        http_response_code(403);
        exit('403 - No tienes permiso para esta operación.');
    }
}

function puede(string ...$roles): bool
{
    return in_array($_SESSION['usuario']['rol'] ?? '', $roles, true);
}
