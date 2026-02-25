<?php
/**
 * Procesar edición de usuario desde panel admin
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
$idUser = intval($_POST['idUser'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$sexo = trim($_POST['sexo'] ?? '');
$password = $_POST['password'] ?? '';
$rol = trim($_POST['rol'] ?? 'user');

// Validar ID
if ($idUser <= 0) {
    $_SESSION['error'] = 'ID de usuario inválido';
    header('Location: ../admin/usuarios-administracion.php');
    exit();
}

// Validaciones
$errores = [];

if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
if (empty($apellidos)) $errores[] = 'Los apellidos son obligatorios';
if (empty($email)) $errores[] = 'El email es obligatorio';
if (empty($telefono)) $errores[] = 'El teléfono es obligatorio';
if (empty($fecha_nacimiento)) $errores[] = 'La fecha de nacimiento es obligatoria';

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
    header('Location: ../admin/usuarios-administracion.php?accion=editar&id=' . $idUser);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Verificar que el usuario existe
    $stmt = $pdo->prepare("SELECT idUser FROM users_data WHERE idUser = ?");
    $stmt->execute([$idUser]);
    if (!$stmt->fetch()) {
        throw new Exception('Usuario no encontrado');
    }
    
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
    $stmt->execute([$nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo, $idUser]);
    
    // Actualizar rol
    $sql = "UPDATE users_login SET rol = ? WHERE idUser = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rol, $idUser]);
    
    // Si se proporcionó contraseña, actualizar
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users_login SET password = ? WHERE idUser = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$password_hash, $idUser]);
    }
    
    $pdo->commit();
    
    $_SESSION['success'] = 'Usuario actualizado exitosamente';
    header('Location: ../admin/usuarios-administracion.php');
    exit();
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../admin/usuarios-administracion.php?accion=editar&id=' . $idUser);
    exit();
}
?>
