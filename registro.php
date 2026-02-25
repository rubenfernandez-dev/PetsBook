<?php
/**
 * Formulario de registro de usuarios
 * Recopila datos personales y credenciales de acceso
 */
session_start();
require_once 'cookies.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - PetsBook</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <div class="registro-container">
            <h1>Crear Cuenta</h1>
            <p class="subtitulo">Completa el formulario para registrarte en PetsBook</p>
            
            <?php
            // Mostrar mensajes de error si existen
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>
            
            <form action="procesos/registrar_usuario.php" method="POST" class="form-registro">
                <!-- Datos personales -->
                <div class="form-section">
                    <h3>Datos Personales</h3>
                    
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required 
                               maxlength="100" placeholder="Ingresa tu nombre"
                               value="<?php echo isset($_SESSION['form_data']['nombre']) ? htmlspecialchars($_SESSION['form_data']['nombre']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="apellidos">Apellidos *</label>
                        <input type="text" id="apellidos" name="apellidos" required 
                               maxlength="150" placeholder="Ingresa tus apellidos"
                               value="<?php echo isset($_SESSION['form_data']['apellidos']) ? htmlspecialchars($_SESSION['form_data']['apellidos']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required 
                               maxlength="150" placeholder="ejemplo@correo.com"
                               value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono *</label>
                        <input type="tel" id="telefono" name="telefono" required 
                               maxlength="20" placeholder="Número de teléfono"
                               value="<?php echo isset($_SESSION['form_data']['telefono']) ? htmlspecialchars($_SESSION['form_data']['telefono']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                               value="<?php echo isset($_SESSION['form_data']['fecha_nacimiento']) ? htmlspecialchars($_SESSION['form_data']['fecha_nacimiento']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="sexo">Sexo</label>
                        <select id="sexo" name="sexo">
                            <option value="">Selecciona una opción</option>
                            <option value="Masculino" <?php echo (isset($_SESSION['form_data']['sexo']) && $_SESSION['form_data']['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Femenino" <?php echo (isset($_SESSION['form_data']['sexo']) && $_SESSION['form_data']['sexo'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                            <option value="Otro" <?php echo (isset($_SESSION['form_data']['sexo']) && $_SESSION['form_data']['sexo'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                            <option value="Prefiero no decir" <?php echo (isset($_SESSION['form_data']['sexo']) && $_SESSION['form_data']['sexo'] == 'Prefiero no decir') ? 'selected' : ''; ?>>Prefiero no decir</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <textarea id="direccion" name="direccion" rows="3" 
                                  placeholder="Calle, número, ciudad, código postal..."><?php echo isset($_SESSION['form_data']['direccion']) ? htmlspecialchars($_SESSION['form_data']['direccion']) : ''; ?></textarea>
                    </div>
                </div>
                
                <!-- Credenciales de acceso -->
                <div class="form-section">
                    <h3>Credenciales de Acceso</h3>
                    
                    <div class="form-group">
                        <label for="usuario">Usuario *</label>
                        <input type="text" id="usuario" name="usuario" required 
                               maxlength="100" placeholder="Elige un nombre de usuario"
                               value="<?php echo isset($_SESSION['form_data']['usuario']) ? htmlspecialchars($_SESSION['form_data']['usuario']) : ''; ?>">
                        <small>El nombre de usuario debe ser único</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña *</label>
                        <input type="password" id="password" name="password" required 
                               minlength="6" placeholder="Mínimo 6 caracteres">
                        <small>Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirmar Contraseña *</label>
                        <input type="password" id="password_confirm" name="password_confirm" required 
                               minlength="6" placeholder="Repite la contraseña">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                    <a href="login.php" class="btn btn-secondary">Ya tengo cuenta</a>
                </div>
                
                <p class="form-note">* Campos obligatorios</p>
            </form>
            
            <?php
            // Limpiar datos del formulario de sesión
            unset($_SESSION['form_data']);
            ?>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
