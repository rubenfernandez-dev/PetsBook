<?php
/**
 * Procesar creación de noticia
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
$titulo = trim($_POST['titulo'] ?? '');
$imagen = trim($_POST['imagen'] ?? '');
$fecha = trim($_POST['fecha'] ?? '');
$texto = trim($_POST['texto'] ?? '');
$idUser = $_SESSION['idUser'];

// Validar campos obligatorios
if (empty($titulo) || empty($imagen) || empty($fecha) || empty($texto)) {
    $_SESSION['error'] = 'Todos los campos son obligatorios';
    header('Location: ../admin/noticias-administracion.php?accion=crear');
    exit();
}

// Validar longitud del título
if (strlen($titulo) > 200) {
    $_SESSION['error'] = 'El título no puede exceder 200 caracteres';
    header('Location: ../admin/noticias-administracion.php?accion=crear');
    exit();
}

// Validar formato de fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $_SESSION['error'] = 'Formato de fecha inválido';
    header('Location: ../admin/noticias-administracion.php?accion=crear');
    exit();
}

try {
    // Verificar que el título no esté duplicado
    $stmt = $pdo->prepare("SELECT idNoticia FROM noticias WHERE titulo = ?");
    $stmt->execute([$titulo]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Ya existe una noticia con ese título';
        header('Location: ../admin/noticias-administracion.php?accion=crear');
        exit();
    }
    
    // Insertar la noticia
    $sql = "INSERT INTO noticias (titulo, imagen, texto, fecha, idUser) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $titulo,
        $imagen,
        $texto,
        $fecha,
        $idUser
    ]);
    
    $_SESSION['success'] = 'Noticia creada exitosamente';
    header('Location: ../admin/noticias-administracion.php?accion=listar');
    exit();
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al crear la noticia: ' . $e->getMessage();
    header('Location: ../admin/noticias-administracion.php?accion=crear');
    exit();
}
?>
