<?php
declare(strict_types=1);

require_once __DIR__ . '/sesion.php';
cerrarSesion();
header('Location: ../login.php?m=sesion_cerrada', true, 303);
exit;
