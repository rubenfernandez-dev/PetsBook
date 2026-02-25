<?php
/**
 * Panel de administración de citas
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

$accion = $_GET['accion'] ?? 'seleccionar';
$idUser = $_GET['idUser'] ?? null;
$idCita = $_GET['id'] ?? null;

// Si es editar, obtener datos de la cita
$cita_editar = null;
if ($accion === 'editar' && $idCita) {
    $stmt = $pdo->prepare("SELECT * FROM citas WHERE idCita = ?");
    $stmt->execute([$idCita]);
    $cita_editar = $stmt->fetch();
    if (!$cita_editar) {
        $accion = 'seleccionar';
        $_SESSION['error'] = 'Cita no encontrada';
    } elseif (strtotime($cita_editar['fecha_cita']) < strtotime(date('Y-m-d'))) {
        $_SESSION['error'] = 'No se pueden editar citas pasadas';
        $accion = 'listar';
        $idUser = $cita_editar['idUser'];
    } else {
        $idUser = $cita_editar['idUser'];
    }
}

// Obtener lista de usuarios
$usuarios = [];
try {
    $stmt = $pdo->query("SELECT idUser, CONCAT(nombre, ' ', apellidos) as nombre_completo FROM users_data ORDER BY nombre");
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al cargar usuarios';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Citas - PetsBook</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="admin-header">
            <h1>Administración de Citas</h1>
            <?php if ($accion === 'seleccionar' || $accion === 'listar'): ?>
                <button onclick="window.location.href='citas-administracion.php'" class="btn btn-secondary">👥 Seleccionar Usuario</button>
            <?php else: ?>
                <button onclick="window.location.href='citas-administracion.php?idUser=<?php echo $idUser; ?>'" class="btn btn-secondary">← Volver al Listado</button>
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
        
        <?php if ($accion === 'seleccionar'): ?>
            <!-- SELECCIONAR USUARIO -->
            <div class="seleccion-usuario">
                <h2>Selecciona un usuario para ver sus citas</h2>
                <div class="lista-usuarios">
                    <?php foreach ($usuarios as $usuario): ?>
                        <div class="usuario-card">
                            <span class="usuario-nombre"><?php echo htmlspecialchars($usuario['nombre_completo']); ?></span>
                            <a href="?idUser=<?php echo $usuario['idUser']; ?>" class="btn btn-primary btn-sm">Ver Citas</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
        <?php elseif (($accion === 'listar' || !$accion) && $idUser): ?>
            <!-- LISTADO DE CITAS DEL USUARIO -->
            <?php
            try {
                // Obtener datos del usuario
                $stmt = $pdo->prepare("SELECT CONCAT(nombre, ' ', apellidos) as nombre_completo FROM users_data WHERE idUser = ?");
                $stmt->execute([$idUser]);
                $usuario_seleccionado = $stmt->fetch();
                
                if (!$usuario_seleccionado) {
                    echo '<div class="alert alert-error">Usuario no encontrado</div>';
                } else {
            ?>
                    <div class="usuario-info">
                        <h2>Citas de: <?php echo htmlspecialchars($usuario_seleccionado['nombre_completo']); ?></h2>
                        <button onclick="window.location.href='?accion=crear&idUser=<?php echo $idUser; ?>'" class="btn btn-primary">📅 Nueva Cita</button>
                    </div>
                    
                    <?php
                    $sql = "SELECT * FROM citas WHERE idUser = ? ORDER BY fecha_cita DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$idUser]);
                    $citas = $stmt->fetchAll();
                    
                    if (count($citas) > 0):
                        $hoy = date('Y-m-d');
                    ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Motivo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($citas as $cita):
                                        $esPasada = strtotime($cita['fecha_cita']) < strtotime($hoy);
                                    ?>
                                        <tr>
                                            <td><?php echo $cita['idCita']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($cita['fecha_cita'])); ?></td>
                                            <td><?php echo htmlspecialchars($cita['motivo_cita'] ?? 'Sin motivo'); ?></td>
                                            <td>
                                                <?php if ($esPasada): ?>
                                                    <span class="badge badge-pasada">Pasada</span>
                                                <?php else: ?>
                                                    <span class="badge badge-futura">Próxima</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="acciones">
                                                <?php if (!$esPasada): ?>
                                                    <a href="?accion=editar&id=<?php echo $cita['idCita']; ?>" class="btn-accion btn-editar">✏️ Editar</a>
                                                    <a href="../procesos/borrar_cita.php?id=<?php echo $cita['idCita']; ?>" 
                                                       onclick="return confirm('¿Estás seguro de eliminar esta cita?')" 
                                                       class="btn-accion btn-borrar">🗑️ Borrar</a>
                                                <?php else: ?>
                                                    <span class="texto-muted">No editable</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php
                    else:
                        echo '<div class="alert alert-info">Este usuario no tiene citas registradas.</div>';
                    endif;
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-error">Error al cargar citas: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
            
        <?php elseif ($accion === 'crear' || $accion === 'editar'): ?>
            <!-- FORMULARIO DE CREAR/EDITAR CITA -->
            <?php
            // Obtener datos del usuario
            $usuario_nombre = '';
            if ($idUser) {
                $stmt = $pdo->prepare("SELECT CONCAT(nombre, ' ', apellidos) as nombre_completo FROM users_data WHERE idUser = ?");
                $stmt->execute([$idUser]);
                $usuario = $stmt->fetch();
                $usuario_nombre = $usuario['nombre_completo'] ?? '';
            }
            ?>
            <div class="form-container">
                <h2><?php echo $accion === 'crear' ? 'Crear Nueva Cita' : 'Editar Cita'; ?></h2>
                <?php if ($usuario_nombre): ?>
                    <p class="form-info">Usuario: <strong><?php echo htmlspecialchars($usuario_nombre); ?></strong></p>
                <?php endif; ?>
                
                <form action="../procesos/<?php echo $accion === 'crear' ? 'crear_cita.php' : 'editar_cita.php'; ?>" 
                      method="POST" class="form-cita">
                    
                    <input type="hidden" name="idUser" value="<?php echo $idUser; ?>">
                    <?php if ($accion === 'editar'): ?>
                        <input type="hidden" name="idCita" value="<?php echo $cita_editar['idCita']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="fecha_cita">Fecha de la Cita *</label>
                        <input type="date" id="fecha_cita" name="fecha_cita" required
                               min="<?php echo date('Y-m-d'); ?>"
                               value="<?php echo $accion === 'editar' ? $cita_editar['fecha_cita'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="motivo_cita">Motivo de la Cita</label>
                        <textarea id="motivo_cita" name="motivo_cita" rows="5"
                                  placeholder="Describe el motivo de la cita..."><?php echo $accion === 'editar' ? htmlspecialchars($cita_editar['motivo_cita']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $accion === 'crear' ? '📅 Crear Cita' : '💾 Guardar Cambios'; ?>
                        </button>
                        <a href="citas-administracion.php?idUser=<?php echo $idUser; ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../footer.php'; ?>
    
    <script src="../js/scripts.js"></script>
</body>
</html>
