<?php
/**
 * Procesar edición de noticia
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

// Verificar que el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no válido';
    header('Location: ../admin/noticias-administracion.php');
    exit();
}

// Recoger y limpiar datos
$idNoticia = intval($_POST['idNoticia'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$imagen = trim($_POST['imagen'] ?? '');
$fecha = trim($_POST['fecha'] ?? '');
$texto = trim($_POST['texto'] ?? '');

// Validar que existe el ID
if ($idNoticia <= 0) {
    $_SESSION['error'] = 'ID de noticia inválido';
    header('Location: ../admin/noticias-administracion.php');
    exit();
}

// Validar campos obligatorios
if (empty($titulo) || empty($imagen) || empty($fecha) || empty($texto)) {
    $_SESSION['error'] = 'Todos los campos son obligatorios';
    header('Location: ../admin/noticias-administracion.php?accion=editar&id=' . $idNoticia);
    exit();
}

// Validar longitud del título
if (strlen($titulo) > 200) {
    $_SESSION['error'] = 'El título no puede exceder 200 caracteres';
    header('Location: ../admin/noticias-administracion.php?accion=editar&id=' . $idNoticia);
    exit();
}

// Validar formato de fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $_SESSION['error'] = 'Formato de fecha inválido';
    header('Location: ../admin/noticias-administracion.php?accion=editar&id=' . $idNoticia);
    exit();
}

try {
    // Verificar que la noticia existe
    $stmt = $pdo->prepare("SELECT idNoticia FROM noticias WHERE idNoticia = ?");
    $stmt->execute([$idNoticia]);
    if (!$stmt->fetch()) {
        $_SESSION['error'] = 'La noticia no existe';
        header('Location: ../admin/noticias-administracion.php');
        exit();
    }
    
    // Verificar que el título no esté duplicado (excepto la misma noticia)
    $stmt = $pdo->prepare("SELECT idNoticia FROM noticias WHERE titulo = ? AND idNoticia != ?");
    $stmt->execute([$titulo, $idNoticia]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Ya existe otra noticia con ese título';
        header('Location: ../admin/noticias-administracion.php?accion=editar&id=' . $idNoticia);
        exit();
    }
    
    // Actualizar la noticia
    $sql = "UPDATE noticias 
            SET titulo = ?, imagen = ?, texto = ?, fecha = ?
            WHERE idNoticia = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $titulo,
        $imagen,
        $texto,
        $fecha,
        $idNoticia
    ]);
    
    $_SESSION['success'] = 'Noticia actualizada exitosamente';
    header('Location: ../admin/noticias-administracion.php');
    exit();
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al actualizar la noticia: ' . $e->getMessage();
    header('Location: ../admin/noticias-administracion.php?accion=editar&id=' . $idNoticia);
    exit();
}
?>
