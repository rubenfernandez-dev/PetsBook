<?php
/**
 * Procesar borrado de usuario
 * Solo para administradores
 * Valida que el usuario no se borre a sí mismo
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

// Obtener ID del usuario
$idUser = intval($_GET['id'] ?? 0);

// Validar ID
if ($idUser <= 0) {
    $_SESSION['error'] = 'ID de usuario inválido';
    header('Location: ../admin/usuarios-administracion.php');
    exit();
}

// Verificar que no sea el propio usuario
if ($idUser == $_SESSION['idUser']) {
    $_SESSION['error'] = 'No puedes eliminarte a ti mismo';
    header('Location: ../admin/usuarios-administracion.php');
    exit();
}

try {
    // Verificar que el usuario existe
    $stmt = $pdo->prepare("SELECT CONCAT(nombre, ' ', apellidos) as nombre_completo FROM users_data WHERE idUser = ?");
    $stmt->execute([$idUser]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        $_SESSION['error'] = 'Usuario no encontrado';
        header('Location: ../admin/usuarios-administracion.php');
        exit();
    }
    
    // OPCIONAL: Verificar si tiene citas o noticias (comentar si no se desea esta validación)
    /*
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM citas WHERE idUser = ?");
    $stmt->execute([$idUser]);
    $citas_count = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM noticias WHERE idUser = ?");
    $stmt->execute([$idUser]);
    $noticias_count = $stmt->fetchColumn();
    
    if ($citas_count > 0 || $noticias_count > 0) {
        $_SESSION['error'] = 'No se puede eliminar el usuario porque tiene ' . $citas_count . ' citas y ' . $noticias_count . ' noticias asociadas';
        header('Location: ../admin/usuarios-administracion.php');
        exit();
    }
    */
    
    // Eliminar usuario (CASCADE eliminará users_login, citas y noticias)
    $sql = "DELETE FROM users_data WHERE idUser = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUser]);
    
    $_SESSION['success'] = 'Usuario "' . htmlspecialchars($usuario['nombre_completo']) . '" eliminado exitosamente';
    header('Location: ../admin/usuarios-administracion.php');
    exit();
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al eliminar el usuario: ' . $e->getMessage();
    header('Location: ../admin/usuarios-administracion.php');
    exit();
}
?>
