<?php
/**
 * Procesar creación de usuario desde panel admin
 * Solo para administradores
 */

session_start();
require_once '../conexion.php';

if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Verificar que el usuario está logueado y es admin
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['error'] = 'Acceso denegado';
    header('Location: ../admin/usuarios-administracion.php');
    exit();
}

// Verificar que el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no válido';
    header('Location: ../admin/usuarios-administracion.php');
    exit();
}

// Recoger y limpiar datos
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$sexo = trim($_POST['sexo'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';
$rol = trim($_POST['rol'] ?? 'user');

// Validaciones
$errores = [];

if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
if (empty($apellidos)) $errores[] = 'Los apellidos son obligatorios';
if (empty($email)) $errores[] = 'El email es obligatorio';
if (empty($telefono)) $errores[] = 'El teléfono es obligatorio';
if (empty($fecha_nacimiento)) $errores[] = 'La fecha de nacimiento es obligatoria';
if (empty($usuario)) $errores[] = 'El usuario es obligatorio';
if (empty($password)) $errores[] = 'La contraseña es obligatoria';

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El formato del email no es válido';
}

if (!empty($password) && strlen($password) < 6) {
    $errores[] = 'La contraseña debe tener al menos 6 caracteres';
}

if (!in_array($rol, ['user', 'admin'])) {
    $errores[] = 'El rol debe ser "user" o "admin"';
}

if (!empty($errores)) {
    $_SESSION['error'] = implode('. ', $errores);
    header('Location: ../admin/usuarios-administracion.php?accion=crear');
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Verificar que el email no exista
    $stmt = $pdo->prepare("SELECT idUser FROM users_data WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('El email ya está registrado');
    }
    
    // Verificar que el usuario no exista
    $stmt = $pdo->prepare("SELECT idLogin FROM users_login WHERE usuario = ?");
    $stmt->execute([$usuario]);
    if ($stmt->fetch()) {
        throw new Exception('El nombre de usuario ya está en uso');
    }
    
    // Insertar en users_data
    $sql = "INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo]);
    
    $idUser = $pdo->lastInsertId();
    
    // Encriptar contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar en users_login
    $sql = "INSERT INTO users_login (idUser, usuario, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUser, $usuario, $password_hash, $rol]);
    
    $pdo->commit();
    
    $_SESSION['success'] = 'Usuario creado exitosamente';
    header('Location: ../admin/usuarios-administracion.php');
    exit();
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../admin/usuarios-administracion.php?accion=crear');
    exit();
}
?>
