<?php
/**
 * Barra de navegación dinámica
 * Cambia según el rol del usuario (visitante, user, admin)
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determinar si hay usuario logueado
$logueado = isset($_SESSION['idUser']);
$rol = $_SESSION['rol'] ?? 'visitante';
$nombre_usuario = $_SESSION['nombre'] ?? '';
?>
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">
            <a href="<?php echo $logueado ? '/PetsBook/index.php' : '/PetsBook/index.php'; ?>">🐾 PetsBook</a>
        </div>
        
        <ul class="navbar-menu">
            <li><a href="<?php echo $logueado ? '/PetsBook/index.php' : '/PetsBook/index.php'; ?>" class="nav-link">Inicio</a></li>
            <li><a href="<?php echo $logueado ? '/PetsBook/noticias.php' : '/PetsBook/noticias.php'; ?>" class="nav-link">Noticias</a></li>
            
            <?php if (!$logueado): ?>
                <!-- Menú para visitantes -->
                <li><a href="/PetsBook/registro.php" class="nav-link">Registro</a></li>
                <li><a href="/PetsBook/login.php" class="nav-link btn-login">Iniciar Sesión</a></li>
                
            <?php elseif ($rol === 'admin'): ?>
                <!-- Menú para administradores -->
                <li><a href="/PetsBook/citaciones/citaciones.php" class="nav-link">Citas</a></li>
                <li class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Administración</a>
                    <ul class="dropdown-menu">
                        <li><a href="/PetsBook/admin/usuarios-administracion.php">Usuarios</a></li>
                        <li><a href="/PetsBook/admin/citas-administracion.php">Citas</a></li>
                        <li><a href="/PetsBook/admin/noticias-administracion.php">Noticias</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle">👤 <?php echo htmlspecialchars($nombre_usuario); ?></a>
                    <ul class="dropdown-menu">
                        <li><a href="/PetsBook/usuarios/perfil.php">Mi Perfil</a></li>
                        <li><a href="/PetsBook/logout.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
                
            <?php else: ?>
                <!-- Menú para usuarios normales -->
                <li><a href="/PetsBook/citaciones/citaciones.php" class="nav-link">Citas</a></li>
                <li class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle">👤 <?php echo htmlspecialchars($nombre_usuario); ?></a>
                    <ul class="dropdown-menu">
                        <li><a href="/PetsBook/usuarios/perfil.php">Mi Perfil</a></li>
                        <li><a href="/PetsBook/logout.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
