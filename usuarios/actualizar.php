<?php
/**
 * Actualizar datos del usuario
 * Formulario para editar datos personales y cambiar contraseña
 */
session_start();
require_once '../conexion.php';

if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Verificar que el usuario está logueado
if (!isset($_SESSION['idUser'])) {
    $_SESSION['error'] = 'Debes iniciar sesión';
    header('Location: ../login.php');
    exit();
}

$idUser = $_SESSION['idUser'];

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y limpiar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $sexo = trim($_POST['sexo'] ?? '');
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';
    
    $errores = [];
    
    // Validar campos obligatorios
    if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
    if (empty($apellidos)) $errores[] = 'Los apellidos son obligatorios';
    if (empty($email)) $errores[] = 'El email es obligatorio';
    if (empty($telefono)) $errores[] = 'El teléfono es obligatorio';
    if (empty($fecha_nacimiento)) $errores[] = 'La fecha de nacimiento es obligatoria';
    
    // Validar formato de email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El formato del email no es válido';
    }
    
    // Validar cambio de contraseña (si se proporcionó)
    if (!empty($password_nueva)) {
        if (empty($password_actual)) {
            $errores[] = 'Debes ingresar tu contraseña actual para cambiarla';
        }
        if (strlen($password_nueva) < 6) {
            $errores[] = 'La nueva contraseña debe tener al menos 6 caracteres';
        }
        if ($password_nueva !== $password_confirmar) {
            $errores[] = 'Las contraseñas nuevas no coinciden';
        }
    }
    
    if (empty($errores)) {
        try {
            $pdo->beginTransaction();
            
            // Verificar que el email no esté en uso por otro usuario
            $stmt = $pdo->prepare("SELECT idUser FROM users_data WHERE email = ? AND idUser != ?");
            $stmt->execute([$email, $idUser]);
            if ($stmt->fetch()) {
                throw new Exception('El email ya está en uso por otro usuario');
            }
            
            // Actualizar users_data
            $sql = "UPDATE users_data 
                    SET nombre = ?, apellidos = ?, email = ?, telefono = ?, 
                        fecha_nacimiento = ?, direccion = ?, sexo = ?
                    WHERE idUser = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nombre, $apellidos, $email, $telefono,
                $fecha_nacimiento, $direccion, $sexo, $idUser
            ]);
            
            // Si se proporcionó nueva contraseña, actualizar
            if (!empty($password_nueva)) {
                // Verificar contraseña actual
                $stmt = $pdo->prepare("SELECT password FROM users_login WHERE idUser = ?");
                $stmt->execute([$idUser]);
                $usuario = $stmt->fetch();
                
                if (!password_verify($password_actual, $usuario['password'])) {
                    throw new Exception('La contraseña actual es incorrecta');
                }
                
                // Actualizar contraseña
                $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                $sql = "UPDATE users_login SET password = ? WHERE idUser = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$password_hash, $idUser]);
            }
            
            // Actualizar datos en la sesión
            $_SESSION['nombre'] = $nombre;
            $_SESSION['apellidos'] = $apellidos;
            $_SESSION['email'] = $email;
            
            $pdo->commit();
            
            $_SESSION['success'] = 'Perfil actualizado exitosamente';
            header('Location: perfil.php');
            exit();
            
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['error'] = $e->getMessage();
        }
    } else {
        $_SESSION['error'] = implode('. ', $errores);
    }
}

// Obtener datos actuales del usuario
try {
    $sql = "SELECT 
                ud.nombre, ud.apellidos, ud.email, ud.telefono,
                ud.fecha_nacimiento, ud.direccion, ud.sexo,
                ul.usuario
            FROM users_data ud
            INNER JOIN users_login ul ON ud.idUser = ul.idUser
            WHERE ud.idUser = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUser]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        $_SESSION['error'] = 'No se encontraron datos del usuario';
        header('Location: perfil.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al cargar datos';
    header('Location: perfil.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Perfil - PetsBook</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="form-header">
            <h1>Actualizar Perfil</h1>
            <a href="perfil.php" class="btn btn-secondary">← Volver al Perfil</a>
        </div>
        
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        
        <form action="actualizar.php" method="POST" class="form-actualizar">
            <!-- Datos Personales -->
            <div class="form-section">
                <h3>👤 Datos Personales</h3>
                
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required maxlength="100"
                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="apellidos">Apellidos *</label>
                    <input type="text" id="apellidos" name="apellidos" required maxlength="150"
                           value="<?php echo htmlspecialchars($usuario['apellidos']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required maxlength="150"
                           value="<?php echo htmlspecialchars($usuario['email']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono *</label>
                    <input type="tel" id="telefono" name="telefono" required maxlength="20"
                           value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                           value="<?php echo $usuario['fecha_nacimiento']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" name="sexo">
                        <option value="">Selecciona una opción</option>
                        <option value="Masculino" <?php echo $usuario['sexo'] === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Femenino" <?php echo $usuario['sexo'] === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                        <option value="Otro" <?php echo $usuario['sexo'] === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                        <option value="Prefiero no decir" <?php echo $usuario['sexo'] === 'Prefiero no decir' ? 'selected' : ''; ?>>Prefiero no decir</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion" rows="3"><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                </div>
            </div>
            
            <!-- Credenciales -->
            <div class="form-section">
                <h3>🔑 Usuario (No editable)</h3>
                <div class="form-group">
                    <label>Usuario:</label>
                    <input type="text" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" disabled class="input-disabled">
                    <small>El nombre de usuario no se puede cambiar</small>
                </div>
            </div>
            
            <!-- Cambiar Contraseña -->
            <div class="form-section">
                <h3>🔒 Cambiar Contraseña (Opcional)</h3>
                <p class="form-note">Deja estos campos vacíos si no deseas cambiar tu contraseña</p>
                
                <div class="form-group">
                    <label for="password_actual">Contraseña Actual</label>
                    <input type="password" id="password_actual" name="password_actual" minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="password_nueva">Nueva Contraseña</label>
                    <input type="password" id="password_nueva" name="password_nueva" minlength="6">
                    <small>Mínimo 6 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmar">Confirmar Nueva Contraseña</label>
                    <input type="password" id="password_confirmar" name="password_confirmar" minlength="6">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
                <a href="perfil.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    
    <script src="../js/scripts.js"></script>
</body>
</html>
