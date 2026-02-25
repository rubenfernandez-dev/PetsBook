<?php
/**
 * Archivo de conexión a la base de datos
 * Utiliza PDO para conexiones seguras con MySQL
 */

// Configuración de la base de datos (sin secretos en el repositorio)
$dbConfig = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '3306',
    'name' => getenv('DB_NAME') ?: 'proyecto_final',
    'user' => getenv('DB_USER') ?: 'root',
    'pass' => getenv('DB_PASS') ?: '',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
];

// Configuración local privada (no versionada)
$localConfigFile = __DIR__ . '/config.local.php';
if (file_exists($localConfigFile)) {
    $localConfig = require $localConfigFile;
    if (is_array($localConfig)) {
        $dbConfig = array_merge($dbConfig, $localConfig);
    }
}

// Variable global de conexión PDO
$pdo = null;

try {
    // Crear DSN (Data Source Name)
    $dsn = "mysql:host=" . $dbConfig['host']
        . ";port=" . $dbConfig['port']
        . ";dbname=" . $dbConfig['name']
        . ";charset=" . $dbConfig['charset'];
    
    // Opciones de PDO
    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lanzar excepciones en caso de error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Modo de fetch asociativo por defecto
        PDO::ATTR_EMULATE_PREPARES   => false,                   // Desactivar emulación de prepared statements
    ];
    
    // Crear instancia de PDO
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $opciones);
    
} catch (PDOException $e) {
    // Capturar errores de conexión
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
