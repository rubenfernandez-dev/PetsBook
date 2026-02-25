<?php
/**
 * Página principal
 * Página de inicio con últimas noticias destacadas
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
    <title>Inicio - PetsBook</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        ?>

        <!-- Banner de bienvenida -->
        <section class="hero">
            <h1>🐾 Bienvenido a PetsBook</h1>
            <p class="hero-subtitle">Tu plataforma de gestión veterinaria y noticias sobre mascotas</p>
            
            <?php if (!isset($_SESSION['idUser'])): ?>
                <div class="hero-actions">
                    <a href="registro.php" class="btn btn-primary btn-lg">Registrarse</a>
                    <a href="login.php" class="btn btn-secondary btn-lg">Iniciar Sesión</a>
                </div>
            <?php else: ?>
                <div class="hero-welcome">
                    <h2>¡Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>! 👋</h2>
                    <p>Bienvenido de nuevo a tu plataforma de gestión veterinaria</p>
                </div>
            <?php endif; ?>
        </section>
        
        <!-- Sección de características -->
        <section class="features">
            <h2>¿Qué puedes hacer en PetsBook?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📅</div>
                    <h3>Gestiona tus Citas</h3>
                    <p>Agenda, edita y administra citas veterinarias para tus mascotas de forma fácil y rápida.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📰</div>
                    <h3>Lee Noticias</h3>
                    <p>Mantente informado con las últimas noticias sobre el cuidado de mascotas y veterinaria.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👤</div>
                    <h3>Tu Perfil</h3>
                    <p>Administra tu información personal y mantén tus datos actualizados.</p>
                </div>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <div class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h3>Panel Admin</h3>
                    <p>Accede al panel de administración para gestionar usuarios, citas y noticias.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Últimas noticias -->
        <section class="latest-news">
            <div class="section-header">
                <h2>📰 Últimas Noticias</h2>
                <a href="noticias.php" class="btn btn-link">Ver todas →</a>
            </div>
            
            <?php
            try {
                // Obtener las 3 últimas noticias
                $sql = "SELECT 
                            n.idNoticia,
                            n.titulo,
                            n.imagen,
                            n.texto,
                            n.fecha,
                            CONCAT(ud.nombre, ' ', ud.apellidos) as autor
                        FROM noticias n
                        INNER JOIN users_data ud ON n.idUser = ud.idUser
                        ORDER BY n.fecha DESC
                        LIMIT 3";
                
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
                                    <h3 class="noticia-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                    <div class="noticia-meta">
                                        <span class="noticia-fecha">📅 <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?></span>
                                        <span class="noticia-autor">✍️ <?php echo htmlspecialchars($noticia['autor']); ?></span>
                                    </div>
                                    <div class="noticia-texto">
                                        <?php 
                                        $texto = htmlspecialchars($noticia['texto']);
                                        echo strlen($texto) > 150 ? substr($texto, 0, 150) . '...' : $texto;
                                        ?>
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
                echo '<div class="alert alert-error">Error al cargar noticias.</div>';
            }
            ?>
        </section>
        
        <!-- Llamadas a la acción -->
        <?php if (isset($_SESSION['idUser'])): ?>
        <section class="cta-section">
            <div class="cta-card">
                <h3>📅 ¿Necesitas agendar una cita?</h3>
                <p>Programa una cita veterinaria para tu mascota de forma rápida y sencilla.</p>
                <a href="citaciones/citaciones.php?accion=crear" class="btn btn-primary">Agendar Cita</a>
            </div>
            <div class="cta-card">
                <h3>👤 Actualiza tu perfil</h3>
                <p>Mantén tu información personal siempre actualizada.</p>
                <a href="usuarios/perfil.php" class="btn btn-secondary">Ver Perfil</a>
            </div>
        </section>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="js/scripts.js"></script>
</body>
</html>
