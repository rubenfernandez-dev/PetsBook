<?php
/**
 * Panel de administración de noticias
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

// Determinar acción
$accion = $_GET['accion'] ?? 'listar';
$idNoticia = $_GET['id'] ?? null;

// Si es editar, obtener datos de la noticia
$noticia_editar = null;
if ($accion === 'editar' && $idNoticia) {
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE idNoticia = ?");
    $stmt->execute([$idNoticia]);
    $noticia_editar = $stmt->fetch();
    if (!$noticia_editar) {
        $accion = 'listar';
        $_SESSION['error'] = 'Noticia no encontrada';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Noticias - PetsBook</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="admin-header">
            <h1>Administración de Noticias</h1>
            <?php if ($accion === 'listar'): ?>
                <button onclick="window.location.href='?accion=crear'" class="btn btn-primary">➕ Nueva Noticia</button>
            <?php else: ?>
                <button onclick="window.location.href='noticias-administracion.php'" class="btn btn-secondary">← Volver al Listado</button>
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
            <!-- LISTADO DE NOTICIAS -->
            <?php
            try {
                $sql = "SELECT 
                            n.idNoticia,
                            n.titulo,
                            n.fecha,
                            n.imagen,
                            CONCAT(ud.nombre, ' ', ud.apellidos) as autor
                        FROM noticias n
                        INNER JOIN users_data ud ON n.idUser = ud.idUser
                        ORDER BY n.fecha DESC";
                
                $stmt = $pdo->query($sql);
                $noticias = $stmt->fetchAll();
                
                if (count($noticias) > 0):
            ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Autor</th>
                                    <th>Imagen</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($noticias as $noticia): ?>
                                    <tr>
                                        <td><?php echo $noticia['idNoticia']; ?></td>
                                        <td><?php echo htmlspecialchars($noticia['titulo']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?></td>
                                        <td><?php echo htmlspecialchars($noticia['autor']); ?></td>
                                        <td><img src="../<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="" class="tabla-imagen"></td>
                                        <td class="acciones">
                                            <a href="?accion=editar&id=<?php echo $noticia['idNoticia']; ?>" class="btn-accion btn-editar">✏️ Editar</a>
                                            <a href="../procesos/borrar_noticia.php?id=<?php echo $noticia['idNoticia']; ?>" 
                                               onclick="return confirm('¿Estás seguro de eliminar esta noticia?')" 
                                               class="btn-accion btn-borrar">🗑️ Borrar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
            <?php
                else:
                    echo '<div class="alert alert-info">No hay noticias registradas.</div>';
                endif;
            } catch (PDOException $e) {
                echo '<div class="alert alert-error">Error al cargar noticias: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
            
        <?php elseif ($accion === 'crear' || $accion === 'editar'): ?>
            <!-- FORMULARIO DE CREAR/EDITAR NOTICIA -->
            <div class="form-container">
                <h2><?php echo $accion === 'crear' ? 'Crear Nueva Noticia' : 'Editar Noticia'; ?></h2>
                
                <form action="../procesos/<?php echo $accion === 'crear' ? 'crear_noticia.php' : 'editar_noticia.php'; ?>" 
                      method="POST" class="admin-form">
                    
                    <?php if ($accion === 'editar'): ?>
                        <input type="hidden" name="idNoticia" value="<?php echo $noticia_editar['idNoticia']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" required maxlength="200"
                               value="<?php echo $accion === 'editar' ? htmlspecialchars($noticia_editar['titulo']) : ''; ?>"
                               placeholder="Título de la noticia">
                    </div>
                    
                    <div class="form-group">
                        <label for="imagen">URL de la Imagen *</label>
                        <input type="text" id="imagen" name="imagen" required maxlength="255"
                               value="<?php echo $accion === 'editar' ? htmlspecialchars($noticia_editar['imagen']) : ''; ?>"
                               placeholder="img/nombre-imagen.jpg">
                        <small>Ruta relativa de la imagen (ej: img/noticia1.jpg)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha">Fecha de Publicación *</label>
                        <input type="date" id="fecha" name="fecha" required
                               value="<?php echo $accion === 'editar' ? $noticia_editar['fecha'] : date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="texto">Contenido *</label>
                        <textarea id="texto" name="texto" required rows="10"
                                  placeholder="Escribe el contenido de la noticia..."><?php echo $accion === 'editar' ? htmlspecialchars($noticia_editar['texto']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $accion === 'crear' ? '➕ Crear Noticia' : '💾 Guardar Cambios'; ?>
                        </button>
                        <a href="noticias-administracion.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../footer.php'; ?>
    
    <script src="../js/scripts.js"></script>
</body>
</html>
