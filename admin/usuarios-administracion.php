<?php
/**
 * Panel de administración de usuarios
 * Solo accesible para administradores
 */
session_start();
require_once '../conexion.php';
require_once '../cookies.php';

if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Verificar que el usuario está logueado y es admin
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['error'] = 'Acceso denegado. Solo administradores.';
    header('Location: ../login.php');
    exit();
}

$accion = $_GET['accion'] ?? 'listar';
$idUser = $_GET['id'] ?? null;

// Si es editar, obtener datos del usuario
$usuario_editar = null;
if ($accion === 'editar' && $idUser) {
    try {
        $sql = "SELECT 
                    ud.idUser, ud.nombre, ud.apellidos, ud.email, ud.telefono,
                    ud.fecha_nacimiento, ud.direccion, ud.sexo,
                    ul.usuario, ul.rol
                FROM users_data ud
                INNER JOIN users_login ul ON ud.idUser = ul.idUser
                WHERE ud.idUser = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idUser]);
        $usuario_editar = $stmt->fetch();
        
        if (!$usuario_editar) {
            $accion = 'listar';
            $_SESSION['error'] = 'Usuario no encontrado';
        }
    } catch (PDOException $e) {
        $accion = 'listar';
        $_SESSION['error'] = 'Error al cargar usuario';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios - PetsBook</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="admin-header">
            <h1>Administración de Usuarios</h1>
            <?php if ($accion === 'listar'): ?>
                <button onclick="window.location.href='?accion=crear'" class="btn btn-primary">➕ Nuevo Usuario</button>
            <?php else: ?>
                <button onclick="window.location.href='usuarios-administracion.php'" class="btn btn-secondary">← Volver al Listado</button>
            <?php endif; ?>
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
        
        <?php if ($accion === 'listar'): ?>
            <!-- LISTADO DE USUARIOS -->
            <?php
            try {
                $sql = "SELECT 
                            ud.idUser,
                            ud.nombre,
                            ud.apellidos,
                            ud.email,
                            ud.telefono,
                            ul.usuario,
                            ul.rol,
                            (SELECT COUNT(*) FROM citas WHERE idUser = ud.idUser) as citas_count,
                            (SELECT COUNT(*) FROM noticias WHERE idUser = ud.idUser) as noticias_count
                        FROM users_data ud
                        INNER JOIN users_login ul ON ud.idUser = ul.idUser
                        ORDER BY ud.nombre";
                
                $stmt = $pdo->query($sql);
                $usuarios = $stmt->fetchAll();
                
                if (count($usuarios) > 0):
            ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Citas</th>
                                    <th>Noticias</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?php echo $usuario['idUser']; ?></td>
                                        <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                                        <td>
                                            <?php if ($usuario['rol'] === 'admin'): ?>
                                                <span class="badge badge-admin">Admin</span>
                                            <?php else: ?>
                                                <span class="badge badge-user">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo $usuario['citas_count']; ?></td>
                                        <td class="text-center"><?php echo $usuario['noticias_count']; ?></td>
                                        <td class="acciones">
                                            <a href="?accion=editar&id=<?php echo $usuario['idUser']; ?>" class="btn-accion btn-editar">✏️ Editar</a>
                                            <?php if ($usuario['idUser'] != $_SESSION['idUser']): ?>
                                                <a href="../procesos/borrar_usuario.php?id=<?php echo $usuario['idUser']; ?>" 
                                                   onclick="return confirm('¿Estás seguro de eliminar este usuario? Este usuario tiene <?php echo $usuario['citas_count']; ?> citas y <?php echo $usuario['noticias_count']; ?> noticias.')" 
                                                   class="btn-accion btn-borrar">🗑️ Borrar</a>
                                            <?php else: ?>
                                                <span class="texto-muted">No puedes borrarte</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
            <?php
                else:
                    echo '<div class="alert alert-info">No hay usuarios registrados.</div>';
                endif;
            } catch (PDOException $e) {
                echo '<div class="alert alert-error">Error al cargar usuarios: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
            
        <?php elseif ($accion === 'crear' || $accion === 'editar'): ?>
            <!-- FORMULARIO DE CREAR/EDITAR USUARIO -->
            <div class="form-container">
                <h2><?php echo $accion === 'crear' ? 'Crear Nuevo Usuario' : 'Editar Usuario'; ?></h2>
                
                <form action="../procesos/<?php echo $accion === 'crear' ? 'crear_usuario.php' : 'editar_usuario.php'; ?>" 
                      method="POST" class="admin-form">
                    
                    <?php if ($accion === 'editar'): ?>
                        <input type="hidden" name="idUser" value="<?php echo $usuario_editar['idUser']; ?>">
                    <?php endif; ?>
                    
                    <!-- Datos Personales -->
                    <div class="form-section">
                        <h3>Datos Personales</h3>
                        
                        <div class="form-group">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" required maxlength="100"
                                   value="<?php echo $accion === 'editar' ? htmlspecialchars($usuario_editar['nombre']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="apellidos">Apellidos *</label>
                            <input type="text" id="apellidos" name="apellidos" required maxlength="150"
                                   value="<?php echo $accion === 'editar' ? htmlspecialchars($usuario_editar['apellidos']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required maxlength="150"
                                   value="<?php echo $accion === 'editar' ? htmlspecialchars($usuario_editar['email']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono">Teléfono *</label>
                            <input type="tel" id="telefono" name="telefono" required maxlength="20"
                                   value="<?php echo $accion === 'editar' ? htmlspecialchars($usuario_editar['telefono']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                                   value="<?php echo $accion === 'editar' ? $usuario_editar['fecha_nacimiento'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select id="sexo" name="sexo">
                                <option value="">Selecciona una opción</option>
                                <option value="Masculino" <?php echo ($accion === 'editar' && $usuario_editar['sexo'] === 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                                <option value="Femenino" <?php echo ($accion === 'editar' && $usuario_editar['sexo'] === 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                                <option value="Otro" <?php echo ($accion === 'editar' && $usuario_editar['sexo'] === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                <option value="Prefiero no decir" <?php echo ($accion === 'editar' && $usuario_editar['sexo'] === 'Prefiero no decir') ? 'selected' : ''; ?>>Prefiero no decir</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea id="direccion" name="direccion" rows="3"><?php echo $accion === 'editar' ? htmlspecialchars($usuario_editar['direccion']) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Credenciales -->
                    <div class="form-section">
                        <h3>Credenciales de Acceso</h3>
                        
                        <div class="form-group">
                            <label for="usuario">Usuario *</label>
                            <input type="text" id="usuario" name="usuario" required maxlength="100"
                                   value="<?php echo $accion === 'editar' ? htmlspecialchars($usuario_editar['usuario']) : ''; ?>"
                                   <?php echo $accion === 'editar' ? 'readonly' : ''; ?>>
                            <?php if ($accion === 'editar'): ?>
                                <small>El usuario no se puede cambiar</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Contraseña <?php echo $accion === 'editar' ? '(dejar vacío para no cambiar)' : '*'; ?></label>
                            <input type="password" id="password" name="password" 
                                   <?php echo $accion === 'crear' ? 'required' : ''; ?> 
                                   minlength="6" placeholder="<?php echo $accion === 'editar' ? 'Dejar vacío para mantener actual' : 'Mínimo 6 caracteres'; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="rol">Rol *</label>
                            <select id="rol" name="rol" required>
                                <option value="user" <?php echo ($accion === 'editar' && $usuario_editar['rol'] === 'user') ? 'selected' : ''; ?>>Usuario</option>
                                <option value="admin" <?php echo ($accion === 'editar' && $usuario_editar['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $accion === 'crear' ? '➕ Crear Usuario' : '💾 Guardar Cambios'; ?>
                        </button>
                        <a href="usuarios-administracion.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../footer.php'; ?>
    
    <script src="../js/scripts.js"></script>
</body>
</html>
