<?php
require_once __DIR__ . '/sgl/login.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZDTecnoc - Iniciar sesión</title>
    <link rel="stylesheet" href="css/tokens.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <main class="login-container">
        <div class="login-card">
            <header class="login-header">
                <span class="brand-name">ZDTecnoc</span>
                <h1>Iniciar sesión</h1>
            </header>

            <?php if ($error !== ''): ?>
                <div class="alerta alerta--error" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['m']) && $_GET['m'] === 'sesion_cerrada'): ?>
                <div class="alerta" style="border-left-color: var(--exito); color: var(--exito); background-color: #eafbe6;" role="alert">
                    Sesión cerrada correctamente.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['m']) && $_GET['m'] === 'requiere_ingreso'): ?>
                <div class="alerta alerta--error" role="alert">
                    Debes iniciar sesión para acceder.
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(tokenCsrf()) ?>">
                
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" class="form-input" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="clave">Contraseña</label>
                    <input type="password" id="clave" name="clave" class="form-input" required>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Ingresar</button>
            </form>
        </div>
    </main>
</body>
</html>
