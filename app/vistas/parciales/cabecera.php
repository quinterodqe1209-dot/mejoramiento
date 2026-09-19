<?php
require_once __DIR__ . '/../../config/config.php';
$usuario = $_SESSION['usuario'];
$tituloPagina = $tituloPagina ?? 'ZDTecnoc';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?> | ZDTecnoc</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/estilos.css">
</head>
<body>
<div class="panel">
    <header class="panel__barra">
        <div class="barra__marca">
            <button class="btn-menu" type="button" aria-label="Abrir menú"
                    aria-controls="menu-lateral" aria-expanded="false">☰</button>
            <h1 class="text-lg">ZDTecnoc - Panel de Control</h1>
        </div>
        <span class="text-sm">
            <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>
            · <?= htmlspecialchars($usuario['rol'], ENT_QUOTES, 'UTF-8') ?>
        </span>
    </header>
