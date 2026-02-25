<?php
/**
 * Procesar borrado de noticia
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
    header('Location: ../admin/noticias-administracion.php');
    exit();
}

// Obtener ID de la noticia
$idNoticia = intval($_GET['id'] ?? 0);

// Validar que existe el ID
if ($idNoticia <= 0) {
    $_SESSION['error'] = 'ID de noticia inválido';
    header('Location: ../admin/noticias-administracion.php');
    exit();
}

try {
    // Verificar que la noticia existe
    $stmt = $pdo->prepare("SELECT titulo FROM noticias WHERE idNoticia = ?");
    $stmt->execute([$idNoticia]);
    $noticia = $stmt->fetch();
    
    if (!$noticia) {
        $_SESSION['error'] = 'La noticia no existe';
        header('Location: ../admin/noticias-administracion.php');
        exit();
    }
    
    // Eliminar la noticia
    $sql = "DELETE FROM noticias WHERE idNoticia = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idNoticia]);
    
    $_SESSION['success'] = 'Noticia "' . htmlspecialchars($noticia['titulo']) . '" eliminada exitosamente';
    header('Location: ../admin/noticias-administracion.php');
    exit();
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al eliminar la noticia: ' . $e->getMessage();
    header('Location: ../admin/noticias-administracion.php');
    exit();
}
?>
