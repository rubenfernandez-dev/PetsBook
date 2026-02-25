<?php
/**
 * Formulario de inicio de sesión
 * Permite a los usuarios acceder al sistema
 */
session_start();
require_once 'cookies.php';

// Si ya hay sesión iniciada, redirigir al index
if (isset($_SESSION['idUser'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - PetsBook</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="login-container">
            <h1>Iniciar Sesión</h1>
            <p class="subtitulo">Bienvenido de nuevo a PetsBook</p>
            
            <?php
            // Mostrar mensaje de éxito si viene del registro
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            
            // Mostrar mensajes de error si existen
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>
            
            <form action="procesos/login_usuario.php" method="POST" class="form-login">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" required 
                           placeholder="Ingresa tu usuario"
                           value="<?php echo isset($_SESSION['login_usuario']) ? htmlspecialchars($_SESSION['login_usuario']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Ingresa tu contraseña">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </div>
                
                <div class="form-links">
                    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                </div>
            </form>
            
            <?php
            // Limpiar usuario guardado temporalmente
            unset($_SESSION['login_usuario']);
            ?>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
