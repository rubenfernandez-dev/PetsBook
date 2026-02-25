<?php
/**
 * Cerrar sesión de usuario
 * Destruye la sesión y redirige al index
 */

session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir la sesión completamente
session_destroy();

// Redirigir al index
header('Location: index.php');
exit();
?>
