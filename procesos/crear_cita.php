<?php
/**
 * Procesar creación de cita
 * Usuarios normales y administradores
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

// Verificar que el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no válido';
    header('Location: ../citaciones/citaciones.php');
    exit();
}

// Determinar si es admin o usuario normal
$esAdmin = $_SESSION['rol'] === 'admin';

// Recoger datos
$fecha_cita = trim($_POST['fecha_cita'] ?? '');
$motivo_cita = trim($_POST['motivo_cita'] ?? '');

// Si es admin, puede crear citas para otros usuarios
$idUser = $esAdmin && isset($_POST['idUser']) ? intval($_POST['idUser']) : $_SESSION['idUser'];

// Validar campos obligatorios
if (empty($fecha_cita)) {
    $_SESSION['error'] = 'La fecha de la cita es obligatoria';
    $redirect = $esAdmin ? '../admin/citas-administracion.php?accion=crear' : '../citaciones/citaciones.php?accion=crear';
    header('Location: ' . $redirect);
    exit();
}

// Validar formato de fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_cita)) {
    $_SESSION['error'] = 'Formato de fecha inválido';
    $redirect = $esAdmin ? '../admin/citas-administracion.php?accion=crear' : '../citaciones/citaciones.php?accion=crear';
    header('Location: ' . $redirect);
    exit();
}

// Validar que la fecha sea futura o de hoy
if (strtotime($fecha_cita) < strtotime(date('Y-m-d'))) {
    $_SESSION['error'] = 'No se pueden crear citas en fechas pasadas';
    $redirect = $esAdmin ? '../admin/citas-administracion.php?accion=crear' : '../citaciones/citaciones.php?accion=crear';
    header('Location: ' . $redirect);
    exit();
}

try {
    // Verificar que el usuario existe
    $stmt = $pdo->prepare("SELECT idUser FROM users_data WHERE idUser = ?");
    $stmt->execute([$idUser]);
    if (!$stmt->fetch()) {
        throw new Exception('Usuario no encontrado');
    }
    
    // Insertar la cita
    $sql = "INSERT INTO citas (idUser, fecha_cita, motivo_cita) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUser, $fecha_cita, $motivo_cita]);
    
    $_SESSION['success'] = 'Cita creada exitosamente';
    $redirect = $esAdmin ? '../admin/citas-administracion.php?idUser=' . $idUser : '../citaciones/citaciones.php';
    header('Location: ' . $redirect);
    exit();
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error al crear la cita: ' . $e->getMessage();
    $redirect = $esAdmin ? '../admin/citas-administracion.php?accion=crear' : '../citaciones/citaciones.php?accion=crear';
    header('Location: ' . $redirect);
    exit();
}
?>
