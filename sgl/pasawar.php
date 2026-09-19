<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit('Esta utilidad solo se puede ejecutar desde la consola.');
}

$clave = $argv[1] ?? '';
if ($clave === '') {
    fwrite(STDOUT, "Escribe la contraseña que deseas convertir en hash: ");
    $clave = trim((string)fgets(STDIN));
}

if (strlen($clave) < 8) {
    fwrite(STDERR, "La contraseña debe tener mínimo 8 caracteres.\n");
    exit(1);
}

fwrite(STDOUT, password_hash($clave, PASSWORD_DEFAULT) . PHP_EOL);
