<?php
require_once 'credenciales.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    $conexion = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false, // Máxima seguridad contra inyecciones SQL
    ]);

} catch (PDOException $e) {
    // En producción se oculta el error real, pero para desarrollo ayuda a depurar
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>