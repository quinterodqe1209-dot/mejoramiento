<?php
declare(strict_types=1);

require_once __DIR__ . '/conexion.php';

$usuarios = [
    ['Administrador Sistema', 'admin@zd.com', 'Admin123*', 'administrador'],
    ['Vendedor Principal', 'vendedor@zd.com', 'Vendedor123*', 'vendedor'],
    ['Consultor Externo', 'consultor@zd.com', 'Consultor123*', 'consultor'],
];

$st = $conexion->prepare(
    'INSERT INTO usuarios (nombre, correo, clave_hash, rol, activo)
     VALUES (:nombre, :correo, :clave_hash, :rol, 1)
     ON DUPLICATE KEY UPDATE
        nombre = VALUES(nombre), clave_hash = VALUES(clave_hash),
        rol = VALUES(rol), activo = 1, bloqueado_hasta = NULL'
);

foreach ($usuarios as [$nombre, $correo, $clave, $rol]) {
    $st->execute([
        ':nombre' => $nombre,
        ':correo' => $correo,
        ':clave_hash' => password_hash($clave, PASSWORD_DEFAULT),
        ':rol' => $rol,
    ]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios de prueba | ZDTecnoc</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <main class="login-container">
        <section class="login-card" aria-labelledby="titulo-registro">
            <h1 id="titulo-registro">Usuarios de prueba creados</h1>
            <p>Las contraseñas se guardaron mediante <code>password_hash()</code>.</p>
            <ul>
                <li>Administrador: admin@zd.com / Admin123*</li>
                <li>Vendedor: vendedor@zd.com / Vendedor123*</li>
                <li>Consultor: consultor@zd.com / Consultor123*</li>
            </ul>
            <p><a href="login.php">Ir al inicio de sesión</a></p>
        </section>
    </main>
</body>
</html>
