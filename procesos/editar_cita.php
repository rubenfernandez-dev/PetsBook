<?php
/**
 * Procesar edición de cita
 * Solo citas futuras
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

// Determinar si es admin
$esAdmin = $_SESSION['rol'] === 'admin';

// Recoger datos
$idCita = intval($_POST['idCita'] ?? 0);
$fecha_cita = trim($_POST['fecha_cita'] ?? '');
$motivo_cita = trim($_POST['motivo_cita'] ?? '');

// Validar ID
if ($idCita <= 0) {
    $_SESSION['error'] = 'ID de cita inválido';
    $redirect = $esAdmin ? '../admin/citas-administracion.php' : '../citaciones/citaciones.php';
    header('Location: ' . $redirect);
    exit();
}

// Validar campos obligatorios
if (empty($fecha_cita)) {
    $_SESSION['error'] = 'La fecha de la cita es obligatoria';
    $redirect = $esAdmin ? '../admin/citas-administracion.php?accion=editar&id=' . $idCita : '../citaciones/citaciones.php?accion=editar&id=' . $idCita;
    header('Location: ' . $redirect);
    exit();
}

// Validar formato de fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_cita)) {
    $_SESSION['error'] = 'Formato de fecha inválido';
    $redirect = $esAdmin ? '../admin/citas-administracion.php?accion=editar&id=' . $idCita : '../citaciones/citaciones.php?accion=editar&id=' . $idCita;
    header('Location: ' . $redirect);
    exit();
}

// Validar que la fecha sea futura o de hoy
if (strtotime($fecha_cita) < strtotime(date('Y-m-d'))) {
    $_SESSION['error'] = 'No se pueden programar citas en fechas pasadas';
    $redirect = $esAdmin ? '../admin/citas-administracion.php?accion=editar&id=' . $idCita : '../citaciones/citaciones.php?accion=editar&id=' . $idCita;
    header('Location: ' . $redirect);
    exit();
}

try {
    // Verificar que la cita existe y pertenece al usuario (si no es admin)
    if ($esAdmin) {
        $stmt = $pdo->prepare("SELECT idUser, fecha_cita FROM citas WHERE idCita = ?");
        $stmt->execute([$idCita]);
    } else {
        $stmt = $pdo->prepare("SELECT idUser, fecha_cita FROM citas WHERE idCita = ? AND idUser = ?");
        $stmt->execute([$idCita, $_SESSION['idUser']]);
    }
    
    $cita = $stmt->fetch();
    if (!$cita) {
        throw new Exception('Cita no encontrada o no tienes permiso para editarla');
    }
    
    // Validar que la cita original sea futura
    if (strtotime($cita['fecha_cita']) < strtotime(date('Y-m-d'))) {
        throw new Exception('No se pueden editar citas pasadas');
    }
    
    // Actualizar la cita
    $sql = "UPDATE citas SET fecha_cita = ?, motivo_cita = ? WHERE idCita = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fecha_cita, $motivo_cita, $idCita]);
    
    $_SESSION['success'] = 'Cita actualizada exitosamente';
    $redirect = $esAdmin ? '../admin/citas-administracion.php?idUser=' . $cita['idUser'] : '../citaciones/citaciones.php';
    header('Location: ' . $redirect);
    exit();
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error al actualizar la cita: ' . $e->getMessage();
    $redirect = $esAdmin ? '../admin/citas-administracion.php' : '../citaciones/citaciones.php';
    header('Location: ' . $redirect);
    exit();
}
?>
