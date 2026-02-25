<?php
/**
 * EJEMPLO DE USO: Sistema de Cookies en PetsBook
 * Este archivo demuestra cómo usar el sistema de cookies
 */

session_start();
require_once 'cookies.php';

// ============================================
// 1. OBTENER PREFERENCIAS DEL USUARIO
// ============================================

$preferencias = obtener_preferencias_usuario();

echo "=== PREFERENCIAS DEL USUARIO ===\n";
echo "Tema: " . $preferencias['tema'] . "\n";           // 'claro' o 'oscuro'
echo "Idioma: " . $preferencias['idioma'] . "\n";       // 'es' o 'en'
echo "Notificaciones: " . $preferencias['notificaciones'] . "\n"; // 'activadas' o 'desactivadas'
echo "Recordar usuario: " . $preferencias['recordar_usuario'] . "\n"; // 'si' o 'no'
echo "Cookies aceptadas: " . $preferencias['aceptar_cookies'] . "\n"; // 'si' o 'no'

// ============================================
// 2. ESTABLECER UNA COOKIE INDIVIDUAL
// ============================================

// Guardar que el usuario prefiere tema oscuro durante 30 días
establecer_cookie('petsbook_tema', 'oscuro', 30);

// ============================================
// 3. OBTENER UNA COOKIE INDIVIDUAL
// ============================================

$tema_usuario = obtener_cookie('petsbook_tema', 'claro');
echo "\nTema guardado: " . $tema_usuario . "\n";

// ============================================
// 4. GUARDAR MÚLTIPLES PREFERENCIAS
// ============================================

$nuevas_preferencias = [
    'tema' => 'oscuro',
    'idioma' => 'es',
    'notificaciones' => 'desactivadas'
];

guardar_preferencias($nuevas_preferencias);
echo "\nPreferencias guardadas correctamente\n";

// ============================================
// 5. ELIMINAR UNA COOKIE
// ============================================

// Si es necesario eliminar una cookie específica
eliminar_cookie('petsbook_recordar');
echo "Cookie 'recordar usuario' eliminada\n";

// ============================================
// 6. USAR EN HTML - APLICAR TEMA DINÁMICAMENTE
// ============================================

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejemplo de Cookies</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body.dark-theme {
            background-color: #1a1a1a;
            color: #e0e0e0;
        }
    </style>
</head>
<body<?php echo $preferencias['tema'] === 'oscuro' ? ' class="dark-theme"' : ''; ?>>
    
    <div class="container">
        <h1>Demostración del Sistema de Cookies</h1>
        
        <!-- ============================================ -->
        <!-- Formulario de Preferencias -->
        <!-- ============================================ -->
        
        <form id="form-preferencias" method="POST" style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2>Configura tus Preferencias</h2>
            
            <div style="margin-bottom: 15px;">
                <label for="tema">Tema:</label>
                <select id="tema" name="tema" style="padding: 8px; margin-left: 10px;">
                    <option value="claro" <?php echo $preferencias['tema'] === 'claro' ? 'selected' : ''; ?>>Claro</option>
                    <option value="oscuro" <?php echo $preferencias['tema'] === 'oscuro' ? 'selected' : ''; ?>>Oscuro</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="idioma">Idioma:</label>
                <select id="idioma" name="idioma" style="padding: 8px; margin-left: 10px;">
                    <option value="es" <?php echo $preferencias['idioma'] === 'es' ? 'selected' : ''; ?>>Español</option>
                    <option value="en" <?php echo $preferencias['idioma'] === 'en' ? 'selected' : ''; ?>>English</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="notificaciones">Notificaciones:</label>
                <select id="notificaciones" name="notificaciones" style="padding: 8px; margin-left: 10px;">
                    <option value="activadas" <?php echo $preferencias['notificaciones'] === 'activadas' ? 'selected' : ''; ?>>Activadas</option>
                    <option value="desactivadas" <?php echo $preferencias['notificaciones'] === 'desactivadas' ? 'selected' : ''; ?>>Desactivadas</option>
                </select>
            </div>
            
            <button type="submit" style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Guardar Preferencias
            </button>
        </form>
        
        <!-- ============================================ -->
        <!-- Información de Cookies Actuales -->
        <!-- ============================================ -->
        
        <div style="background: #e8f4f8; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3>Estado Actual de las Cookies</h3>
            <pre><?php
            echo "Cookies almacenadas en el navegador:\n\n";
            foreach ($_COOKIE as $nombre => $valor) {
                if (strpos($nombre, 'petsbook_') === 0) {
                    echo $nombre . " => " . htmlspecialchars($valor) . "\n";
                }
            }
            ?></pre>
        </div>
        
        <!-- ============================================ -->
        <!-- Información de Sesión -->
        <!-- ============================================ -->
        
        <div style="background: #f0f8f0; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3>Estado de la Sesión</h3>
            <p>
                Usuario logueado: 
                <?php echo isset($_SESSION['idUser']) ? 'Sí - ' . $_SESSION['nombre'] : 'No'; ?>
            </p>
            <p>
                Rol: 
                <?php echo isset($_SESSION['rol']) ? $_SESSION['rol'] : 'No definido'; ?>
            </p>
        </div>
        
    </div>
    
    <!-- ============================================ -->
    <!-- JavaScript para manejar cookies -->
    <!-- ============================================ -->
    
    <script>
        document.getElementById('form-preferencias').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('accion', 'guardar_preferencias');
            formData.append('tema', document.getElementById('tema').value);
            formData.append('idioma', document.getElementById('idioma').value);
            formData.append('notificaciones', document.getElementById('notificaciones').value);
            
            fetch('/PetsBook/cookies.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert('✓ ' + data.mensaje);
                
                // Aplicar tema
                const tema = document.getElementById('tema').value;
                if (tema === 'oscuro') {
                    document.body.classList.add('dark-theme');
                } else {
                    document.body.classList.remove('dark-theme');
                }
                
                // Recargar para mostrar cambios
                setTimeout(() => location.reload(), 500);
            })
            .catch(error => {
                alert('✗ Error al guardar: ' + error);
                console.error('Error:', error);
            });
        });
        
        // Aplicar tema al cargar
        window.addEventListener('load', function() {
            const tema = document.getElementById('tema').value;
            if (tema === 'oscuro') {
                document.body.classList.add('dark-theme');
            }
        });
    </script>
    
    <?php include 'footer.php'; ?>
    
</body>
</html>
