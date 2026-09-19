<?php
$paginaActual = basename($_SERVER['PHP_SELF']);
$opciones = [
    ['archivo' => 'dashboard.php', 'texto' => 'Inicio', 'roles' => ['administrador', 'vendedor', 'consultor']],
    ['archivo' => 'productos.php', 'texto' => 'Productos', 'roles' => ['administrador', 'vendedor', 'consultor']],
    ['archivo' => 'categorias.php', 'texto' => 'Categorías', 'roles' => ['administrador', 'vendedor']],
    ['archivo' => 'clientes.php', 'texto' => 'Clientes', 'roles' => ['administrador', 'vendedor']],
    ['archivo' => 'pedidos.php', 'texto' => 'Pedidos', 'roles' => ['administrador', 'vendedor']],
    ['archivo' => 'reportes.php', 'texto' => 'Reportes', 'roles' => ['administrador', 'consultor']],
    ['archivo' => 'usuarios.php', 'texto' => 'Usuarios', 'roles' => ['administrador']],
];
?>
<aside class="panel__menu" id="menu-lateral">
    <nav aria-label="Navegación principal">
        <ul class="menu-lista">
            <?php foreach ($opciones as $opcion): ?>
                <?php if (!puede(...$opcion['roles'])) continue; ?>
                <?php $activo = $paginaActual === $opcion['archivo']; ?>
                <li>
                    <a href="<?= BASE_URL . $opcion['archivo'] ?>"
                       class="menu-enlace<?= $activo ? ' menu-enlace--activo' : '' ?>"
                       <?= $activo ? 'aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($opcion['texto'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li><a class="menu-enlace menu-enlace--salir" href="<?= BASE_URL ?>sgl/salir.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</aside>
