<?php
/**
 * Procesar inicio de sesión de usuario
 * Valida credenciales y crea sesión de usuario
 */

session_start();
require_once '../conexion.php';

if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Verificar que el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no válido';
    header('Location: ../login.php');
    exit();
}

// Recoger datos del formulario
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

// Guardar usuario para repoblar el formulario en caso de error
$_SESSION['login_usuario'] = $usuario;

// Validar campos obligatorios
if (empty($usuario) || empty($password)) {
    $_SESSION['error'] = 'Usuario y contraseña son obligatorios';
    header('Location: ../login.php');
    exit();
}

try {
    // Buscar usuario en la base de datos
    // JOIN entre users_login y users_data para obtener toda la información
    $sql = "SELECT 
                ul.idLogin,
                ul.idUser,
                ul.usuario,
                ul.password,
                ul.rol,
                ud.nombre,
                ud.apellidos,
                ud.email
            FROM users_login ul
            INNER JOIN users_data ud ON ul.idUser = ud.idUser
            WHERE ul.usuario = ?
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();
    
    // Verificar si el usuario existe
    if (!$user) {
        $_SESSION['error'] = 'Usuario o contraseña incorrectos';
        header('Location: ../login.php');
        exit();
    }
    
    // Verificar contraseña usando password_verify()
    if (!password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Usuario o contraseña incorrectos';
        header('Location: ../login.php');
        exit();
    }
    
    // Credenciales correctas - Iniciar sesión
    // Regenerar ID de sesión para prevenir session fixation
    session_regenerate_id(true);
    
    // Guardar datos del usuario en la sesión
    $_SESSION['idUser'] = $user['idUser'];
    $_SESSION['usuario'] = $user['usuario'];
    $_SESSION['rol'] = $user['rol'];
    $_SESSION['nombre'] = $user['nombre'];
    $_SESSION['apellidos'] = $user['apellidos'];
    $_SESSION['email'] = $user['email'];
    
    // Limpiar datos temporales
    unset($_SESSION['login_usuario']);
    unset($_SESSION['error']);
    
    // Redirigir según el rol del usuario
    if ($user['rol'] === 'admin') {
        // Administrador va al index (o podría ir a un panel de admin)
        header('Location: ../index.php');
    } else {
        // Usuario normal va al index
        header('Location: ../index.php');
    }
    exit();
    
} catch (PDOException $e) {
    // Error en la base de datos
    $_SESSION['error'] = 'Error en el sistema. Por favor, intenta más tarde';
    header('Location: ../login.php');
    exit();
}
?>
