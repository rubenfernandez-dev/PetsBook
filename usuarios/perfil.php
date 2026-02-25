<?php
/**
 * Perfil del usuario
 * Muestra datos personales y credenciales
 */
session_start();
require_once '../conexion.php';
require_once '../cookies.php';

if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Verificar que el usuario está logueado
if (!isset($_SESSION['idUser'])) {
    $_SESSION['error'] = 'Debes iniciar sesión para acceder a tu perfil';
    header('Location: ../login.php');
    exit();
}

$idUser = $_SESSION['idUser'];

// Obtener datos del usuario (JOIN de users_data y users_login)
try {
    $sql = "SELECT 
                ud.idUser,
                ud.nombre,
                ud.apellidos,
                ud.email,
                ud.telefono,
                ud.fecha_nacimiento,
                ud.direccion,
                ud.sexo,
                ul.usuario,
                ul.rol
            FROM users_data ud
            INNER JOIN users_login ul ON ud.idUser = ul.idUser
            WHERE ud.idUser = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUser]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        $_SESSION['error'] = 'No se encontraron datos del usuario';
        header('Location: ../index.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al cargar el perfil';
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - PetsBook</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="perfil-header">
            <h1>Mi Perfil</h1>
            <a href="actualizar.php" class="btn btn-primary">✏️ Editar Perfil</a>
        </div>
        
        <?php
        // Mostrar mensajes
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        
        <div class="perfil-container">
            <!-- Datos Personales -->
            <div class="perfil-seccion">
                <h2>👤 Datos Personales</h2>
                <div class="perfil-datos">
                    <div class="perfil-item">
                        <span class="perfil-label">Nombre:</span>
                        <span class="perfil-valor"><?php echo htmlspecialchars($usuario['nombre']); ?></span>
                    </div>
                    <div class="perfil-item">
                        <span class="perfil-label">Apellidos:</span>
                        <span class="perfil-valor"><?php echo htmlspecialchars($usuario['apellidos']); ?></span>
                    </div>
                    <div class="perfil-item">
                        <span class="perfil-label">Email:</span>
                        <span class="perfil-valor"><?php echo htmlspecialchars($usuario['email']); ?></span>
                    </div>
                    <div class="perfil-item">
                        <span class="perfil-label">Teléfono:</span>
                        <span class="perfil-valor"><?php echo htmlspecialchars($usuario['telefono']); ?></span>
                    </div>
                    <div class="perfil-item">
                        <span class="perfil-label">Fecha de Nacimiento:</span>
                        <span class="perfil-valor"><?php echo date('d/m/Y', strtotime($usuario['fecha_nacimiento'])); ?></span>
                    </div>
                    <div class="perfil-item">
                        <span class="perfil-label">Sexo:</span>
                        <span class="perfil-valor"><?php echo htmlspecialchars($usuario['sexo'] ?: 'No especificado'); ?></span>
                    </div>
                    <div class="perfil-item perfil-item-full">
                        <span class="perfil-label">Dirección:</span>
                        <span class="perfil-valor"><?php echo htmlspecialchars($usuario['direccion'] ?: 'No especificada'); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Credenciales de Acceso -->
            <div class="perfil-seccion">
                <h2>🔑 Credenciales de Acceso</h2>
                <div class="perfil-datos">
                    <div class="perfil-item">
                        <span class="perfil-label">Usuario:</span>
                        <span class="perfil-valor"><?php echo htmlspecialchars($usuario['usuario']); ?></span>
                    </div>
                    <div class="perfil-item">
                        <span class="perfil-label">Rol:</span>
                        <span class="perfil-valor">
                            <?php if ($usuario['rol'] === 'admin'): ?>
                                <span class="badge badge-admin">Administrador</span>
                            <?php else: ?>
                                <span class="badge badge-user">Usuario</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="perfil-item perfil-item-full">
                        <span class="perfil-label">Contraseña:</span>
                        <span class="perfil-valor">**********</span>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas -->
            <div class="perfil-seccion">
                <h2>📊 Estadísticas</h2>
                <div class="perfil-stats">
                    <?php
                    // Contar citas
                    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM citas WHERE idUser = ?");
                    $stmt->execute([$idUser]);
                    $citas_count = $stmt->fetchColumn();
                    
                    // Contar noticias (si es admin)
                    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM noticias WHERE idUser = ?");
                    $stmt->execute([$idUser]);
                    $noticias_count = $stmt->fetchColumn();
                    ?>
                    <div class="stat-card">
                        <span class="stat-numero"><?php echo $citas_count; ?></span>
                        <span class="stat-label">Citas</span>
                    </div>
                    <?php if ($usuario['rol'] === 'admin'): ?>
                        <div class="stat-card">
                            <span class="stat-numero"><?php echo $noticias_count; ?></span>
                            <span class="stat-label">Noticias Publicadas</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../footer.php'; ?>
    
    <script src="../js/scripts.js"></script>
</body>
</html>
