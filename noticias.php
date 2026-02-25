<?php
/**
 * Página pública de noticias
 * Muestra todas las noticias ordenadas por fecha
 */
session_start();
require_once 'conexion.php';
require_once 'cookies.php';

if (!$pdo) {
    die('Error de conexión a la base de datos');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - PetsBook</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="noticias-header">
            <h1>Noticias</h1>
            <p class="subtitulo">Últimas novedades sobre mascotas</p>
        </div>
        
        <?php
        try {
            // Consultar todas las noticias con datos del autor
            $sql = "SELECT 
                        n.idNoticia,
                        n.titulo,
                        n.imagen,
                        n.texto,
                        n.fecha,
                        n.idUser,
                        CONCAT(ud.nombre, ' ', ud.apellidos) as autor
                    FROM noticias n
                    INNER JOIN users_data ud ON n.idUser = ud.idUser
                    ORDER BY n.fecha DESC";
            
            $stmt = $pdo->query($sql);
            $noticias = $stmt->fetchAll();
            
            if (count($noticias) > 0):
        ?>
                <div class="noticias-grid">
                    <?php foreach ($noticias as $noticia): ?>
                        <article class="noticia-card">
                            <div class="noticia-imagen">
                                <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            </div>
                            <div class="noticia-contenido">
                                <h2 class="noticia-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h2>
                                <div class="noticia-meta">
                                    <span class="noticia-fecha">📅 <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?></span>
                                    <span class="noticia-autor">✍️ <?php echo htmlspecialchars($noticia['autor']); ?></span>
                                </div>
                                <div class="noticia-texto">
                                    <?php echo nl2br(htmlspecialchars($noticia['texto'])); ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
        <?php
            else:
                echo '<div class="alert alert-info">No hay noticias disponibles en este momento.</div>';
            endif;
            
        } catch (PDOException $e) {
            echo '<div class="alert alert-error">Error al cargar las noticias: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="js/scripts.js"></script>
</body>
</html>
