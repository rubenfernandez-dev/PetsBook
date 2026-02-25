<?php
/**
 * Gestión de citas del usuario
 * Listar, crear, editar y borrar citas propias
 */
session_start();
require_once '../conexion.php';
require_once '../cookies.php';

if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Verificar que el usuario está logueado
if (!isset($_SESSION['idUser'])) {
    $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta página';
    header('Location: ../login.php');
    exit();
}

$idUser = $_SESSION['idUser'];
$accion = $_GET['accion'] ?? 'listar';
$idCita = $_GET['id'] ?? null;

// Si es editar, obtener datos de la cita
$cita_editar = null;
if ($accion === 'editar' && $idCita) {
    $stmt = $pdo->prepare("SELECT * FROM citas WHERE idCita = ? AND idUser = ?");
    $stmt->execute([$idCita, $idUser]);
    $cita_editar = $stmt->fetch();
    if (!$cita_editar) {
        $accion = 'listar';
        $_SESSION['error'] = 'Cita no encontrada o no tienes permiso para editarla';
    } elseif (strtotime($cita_editar['fecha_cita']) < strtotime(date('Y-m-d'))) {
        $accion = 'listar';
        $_SESSION['error'] = 'No se pueden editar citas pasadas';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas - PetsBook</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="citas-header">
            <h1>Mis Citas</h1>
            <?php if ($accion === 'listar'): ?>
                <button onclick="window.location.href='?accion=crear'" class="btn btn-primary">📅 Nueva Cita</button>
            <?php else: ?>
                <button onclick="window.location.href='citaciones.php'" class="btn btn-secondary">← Volver al Listado</button>
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
            <!-- LISTADO DE CITAS -->
            <?php
            try {
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
                                    <tr class="<?php echo $esPasada ? 'cita-pasada' : 'cita-futura'; ?>">
                                        <td><?php echo $cita['idCita']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($cita['fecha_cita'])); ?></td>
                                        <td><?php echo htmlspecialchars($cita['motivo_cita'] ?? 'Sin motivo especificado'); ?></td>
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
                    echo '<div class="alert alert-info">No tienes citas registradas. ¡Agenda tu primera cita!</div>';
                endif;
            } catch (PDOException $e) {
                echo '<div class="alert alert-error">Error al cargar citas: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
            
        <?php elseif ($accion === 'crear' || $accion === 'editar'): ?>
            <!-- FORMULARIO DE CREAR/EDITAR CITA -->
            <div class="form-container">
                <h2><?php echo $accion === 'crear' ? 'Agendar Nueva Cita' : 'Editar Cita'; ?></h2>
                
                <form action="../procesos/<?php echo $accion === 'crear' ? 'crear_cita.php' : 'editar_cita.php'; ?>" 
                      method="POST" class="form-cita">
                    
                    <?php if ($accion === 'editar'): ?>
                        <input type="hidden" name="idCita" value="<?php echo $cita_editar['idCita']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="fecha_cita">Fecha de la Cita *</label>
                        <input type="date" id="fecha_cita" name="fecha_cita" required
                               min="<?php echo date('Y-m-d'); ?>"
                               value="<?php echo $accion === 'editar' ? $cita_editar['fecha_cita'] : ''; ?>">
                        <small>Solo puedes agendar citas futuras</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="motivo_cita">Motivo de la Cita</label>
                        <textarea id="motivo_cita" name="motivo_cita" rows="5"
                                  placeholder="Describe el motivo de tu cita..."><?php echo $accion === 'editar' ? htmlspecialchars($cita_editar['motivo_cita']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $accion === 'crear' ? '📅 Agendar Cita' : '💾 Guardar Cambios'; ?>
                        </button>
                        <a href="citaciones.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../footer.php'; ?>
    
    <script src="../js/scripts.js"></script>
</body>
</html>
