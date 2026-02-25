<?php
/**
 * Footer profesional
 * Pie de página con enlaces, redes sociales e información
 */

require_once 'cookies.php';

$preferencias = obtener_preferencias_usuario();
$año_actual = date('Y');
?>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-grid">
            <!-- Sección: Sobre nosotros -->
            <div class="footer-section">
                <h4 class="footer-title">🐾 PetsBook</h4>
                <p>Tu plataforma de confianza para la gestión de citas veterinarias y noticias sobre mascotas.</p>
                <div class="footer-social">
                    <a href="https://facebook.com" target="_blank" aria-label="Facebook" class="social-link">
                        <span>f</span>
                    </a>
                    <a href="https://twitter.com" target="_blank" aria-label="Twitter" class="social-link">
                        <span>𝕏</span>
                    </a>
                    <a href="https://instagram.com" target="_blank" aria-label="Instagram" class="social-link">
                        <span>📷</span>
                    </a>
                    <a href="https://youtube.com" target="_blank" aria-label="YouTube" class="social-link">
                        <span>▶</span>
                    </a>
                </div>
            </div>

            <!-- Sección: Enlaces útiles -->
            <div class="footer-section">
                <h5 class="footer-subtitle">Navegación</h5>
                <ul class="footer-links">
                    <li><a href="/PetsBook/index.php">Inicio</a></li>
                    <li><a href="/PetsBook/noticias.php">Noticias</a></li>
                    <?php if (isset($_SESSION['idUser'])): ?>
                        <li><a href="/PetsBook/citaciones/citaciones.php">Mis Citas</a></li>
                        <li><a href="/PetsBook/usuarios/perfil.php">Mi Perfil</a></li>
                    <?php else: ?>
                        <li><a href="/PetsBook/login.php">Iniciar Sesión</a></li>
                        <li><a href="/PetsBook/registro.php">Registro</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Sección: Información legal -->
            <div class="footer-section">
                <h5 class="footer-subtitle">Legal</h5>
                <ul class="footer-links">
                    <li><a href="#terminos" data-modal="terminos">Términos de servicio</a></li>
                    <li><a href="#privacidad" data-modal="privacidad">Política de privacidad</a></li>
                    <li><a href="#cookies" data-modal="cookies">Política de cookies</a></li>
                    <li><a href="#contacto" data-modal="contacto">Contacto</a></li>
                </ul>
            </div>

            <!-- Sección: Preferencias -->
            <div class="footer-section">
                <h5 class="footer-subtitle">Preferencias</h5>
                <div class="footer-preferences">
                    <label class="preference-item">
                        <span>🌙 Tema:</span>
                        <select id="preferencia-tema" class="pref-select">
                            <option value="claro" <?php echo $preferencias['tema'] === 'claro' ? 'selected' : ''; ?>>Claro</option>
                            <option value="oscuro" <?php echo $preferencias['tema'] === 'oscuro' ? 'selected' : ''; ?>>Oscuro</option>
                        </select>
                    </label>
                    <label class="preference-item">
                        <span>📢 Notificaciones:</span>
                        <select id="preferencia-notificaciones" class="pref-select">
                            <option value="activadas" <?php echo $preferencias['notificaciones'] === 'activadas' ? 'selected' : ''; ?>>Activadas</option>
                            <option value="desactivadas" <?php echo $preferencias['notificaciones'] === 'desactivadas' ? 'selected' : ''; ?>>Desactivadas</option>
                        </select>
                    </label>
                </div>
            </div>
        </div>

        <!-- Línea divisoria -->
        <div class="footer-divider"></div>

        <!-- Pie final -->
        <div class="footer-bottom">
            <p>&copy; <?php echo $año_actual; ?> PetsBook. Todos los derechos reservados.</p>
            <p class="footer-credits">Hecho con 💜 para amantes de las mascotas</p>
        </div>
    </div>

    <!-- Banner de aceptación de cookies (mostrar si no está aceptado) -->
    <?php if ($preferencias['aceptar_cookies'] !== 'si'): ?>
    <div class="cookie-banner" id="cookie-banner">
        <div class="cookie-content">
            <p>🍪 Utilizamos cookies para mejorar tu experiencia. Al continuar navegando, aceptas nuestra <a href="#" data-modal="cookies">política de cookies</a>.</p>
        </div>
        <div class="cookie-actions">
            <button id="aceptar-cookies" class="btn-cookie-accept">Aceptar</button>
            <button id="rechazar-cookies" class="btn-cookie-reject">Rechazar</button>
        </div>
    </div>
    <?php endif; ?>
</footer>

<script>
// Gestionar cambios de preferencias
document.getElementById('preferencia-tema')?.addEventListener('change', function() {
    guardar_preferencias();
});

document.getElementById('preferencia-notificaciones')?.addEventListener('change', function() {
    guardar_preferencias();
});

function guardar_preferencias() {
    const tema = document.getElementById('preferencia-tema')?.value || 'claro';
    const notificaciones = document.getElementById('preferencia-notificaciones')?.value || 'activadas';
    
    const formData = new FormData();
    formData.append('accion', 'guardar_preferencias');
    formData.append('tema', tema);
    formData.append('notificaciones', notificaciones);
    
    fetch('/PetsBook/cookies.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Preferencias guardadas:', data);
        // Aplicar tema si cambió
        if (tema === 'oscuro') {
            document.body.classList.add('dark-theme');
        } else {
            document.body.classList.remove('dark-theme');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Aceptar cookies
document.getElementById('aceptar-cookies')?.addEventListener('click', function() {
    const formData = new FormData();
    formData.append('accion', 'aceptar_cookies');
    
    fetch('/PetsBook/cookies.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exitoso) {
            document.getElementById('cookie-banner').style.display = 'none';
        }
    });
});

// Rechazar cookies (solo rechaza el banner)
document.getElementById('rechazar-cookies')?.addEventListener('click', function() {
    document.getElementById('cookie-banner').style.display = 'none';
});

// Aplicar tema guardado al cargar
window.addEventListener('load', function() {
    const tema = document.getElementById('preferencia-tema')?.value || 'claro';
    if (tema === 'oscuro') {
        document.body.classList.add('dark-theme');
    }
});
</script>
