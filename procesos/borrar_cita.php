<?php
/**
 * Procesar borrado de cita
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

// Determinar si es admin
$esAdmin = $_SESSION['rol'] === 'admin';

// Obtener ID de la cita
$idCita = intval($_GET['id'] ?? 0);

// Validar ID
if ($idCita <= 0) {
    $_SESSION['error'] = 'ID de cita inválido';
    $redirect = $esAdmin ? '../admin/citas-administracion.php' : '../citaciones/citaciones.php';
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
        throw new Exception('Cita no encontrada o no tienes permiso para eliminarla');
    }
    
    // Validar que la cita sea futura
    if (strtotime($cita['fecha_cita']) < strtotime(date('Y-m-d'))) {
        throw new Exception('No se pueden eliminar citas pasadas');
    }
    
    // Eliminar la cita
    $sql = "DELETE FROM citas WHERE idCita = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idCita]);
    
    $_SESSION['success'] = 'Cita eliminada exitosamente';
    $redirect = $esAdmin ? '../admin/citas-administracion.php?idUser=' . $cita['idUser'] : '../citaciones/citaciones.php';
    header('Location: ' . $redirect);
    exit();
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error al eliminar la cita: ' . $e->getMessage();
    $redirect = $esAdmin ? '../admin/citas-administracion.php' : '../citaciones/citaciones.php';
    header('Location: ' . $redirect);
    exit();
}
?>
