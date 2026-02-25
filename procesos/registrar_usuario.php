<?php
/**
 * Procesar registro de usuario
 * Valida datos, inserta en users_data y users_login
 */

session_start();
require_once '../conexion.php';

if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Verificar que el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de solicitud no válido';
    header('Location: ../registro.php');
    exit();
}

// Función para limpiar y validar datos
function limpiar_dato($dato) {
    return trim(htmlspecialchars($dato));
}

// Recoger y limpiar datos del formulario
$nombre = limpiar_dato($_POST['nombre'] ?? '');
$apellidos = limpiar_dato($_POST['apellidos'] ?? '');
$email = limpiar_dato($_POST['email'] ?? '');
$telefono = limpiar_dato($_POST['telefono'] ?? '');
$fecha_nacimiento = limpiar_dato($_POST['fecha_nacimiento'] ?? '');
$direccion = limpiar_dato($_POST['direccion'] ?? '');
$sexo = limpiar_dato($_POST['sexo'] ?? '');
$usuario = limpiar_dato($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Guardar datos del formulario en sesión para repoblar en caso de error
$_SESSION['form_data'] = [
    'nombre' => $nombre,
    'apellidos' => $apellidos,
    'email' => $email,
    'telefono' => $telefono,
    'fecha_nacimiento' => $fecha_nacimiento,
    'direccion' => $direccion,
    'sexo' => $sexo,
    'usuario' => $usuario
];

// Validaciones
$errores = [];

// Validar campos obligatorios
if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
if (empty($apellidos)) $errores[] = 'Los apellidos son obligatorios';
if (empty($email)) $errores[] = 'El email es obligatorio';
if (empty($telefono)) $errores[] = 'El teléfono es obligatorio';
if (empty($fecha_nacimiento)) $errores[] = 'La fecha de nacimiento es obligatoria';
if (empty($usuario)) $errores[] = 'El usuario es obligatorio';
if (empty($password)) $errores[] = 'La contraseña es obligatoria';

// Validar formato de email
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El formato del email no es válido';
}

// Validar longitud de contraseña
if (!empty($password) && strlen($password) < 6) {
    $errores[] = 'La contraseña debe tener al menos 6 caracteres';
}

// Validar que las contraseñas coincidan
if ($password !== $password_confirm) {
    $errores[] = 'Las contraseñas no coinciden';
}

// Validar fecha de nacimiento (debe ser mayor de 13 años)
if (!empty($fecha_nacimiento)) {
    $fecha_obj = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_obj)->y;
    if ($edad < 13) {
        $errores[] = 'Debes tener al menos 13 años para registrarte';
    }
}

// Si hay errores, redirigir con mensaje
if (!empty($errores)) {
    $_SESSION['error'] = implode('. ', $errores);
    header('Location: ../registro.php');
    exit();
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT idUser FROM users_data WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('El email ya está registrado');
    }
    
    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT idLogin FROM users_login WHERE usuario = ?");
    $stmt->execute([$usuario]);
    if ($stmt->fetch()) {
        throw new Exception('El nombre de usuario ya está en uso');
    }
    
    // Insertar en users_data
    $sql_data = "INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_data = $pdo->prepare($sql_data);
    $stmt_data->execute([
        $nombre,
        $apellidos,
        $email,
        $telefono,
        $fecha_nacimiento,
        $direccion,
        $sexo
    ]);
    
    // Obtener el ID del usuario recién creado
    $idUser = $pdo->lastInsertId();
    
    // Encriptar contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar en users_login con rol 'user' por defecto
    $sql_login = "INSERT INTO users_login (idUser, usuario, password, rol) 
                  VALUES (?, ?, ?, 'user')";
    $stmt_login = $pdo->prepare($sql_login);
    $stmt_login->execute([
        $idUser,
        $usuario,
        $password_hash
    ]);
    
    // Confirmar transacción
    $pdo->commit();
    
    // Limpiar datos del formulario
    unset($_SESSION['form_data']);
    
    // Establecer mensaje de éxito
    $_SESSION['success'] = 'Registro exitoso. Por favor, inicia sesión con tus credenciales';
    
    // Redirigir al login
    header('Location: ../login.php');
    exit();
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Establecer mensaje de error
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../registro.php');
    exit();
}
?>
