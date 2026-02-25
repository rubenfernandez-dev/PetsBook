<?php
/**
 * Archivo de conexión a la base de datos
 * Utiliza PDO para conexiones seguras con MySQL
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'proyecto_final');
define('DB_USER', 'root');
define('DB_PASS', 'naiara17adrian12');
define('DB_CHARSET', 'utf8mb4');

// Variable global de conexión PDO
$pdo = null;

try {
    // Crear DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    // Opciones de PDO
    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lanzar excepciones en caso de error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Modo de fetch asociativo por defecto
        PDO::ATTR_EMULATE_PREPARES   => false,                   // Desactivar emulación de prepared statements
    ];
    
    // Crear instancia de PDO
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
    
} catch (PDOException $e) {
    // Capturar errores de conexión
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
