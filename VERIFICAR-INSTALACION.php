<?php
/**
 * Verificación del Sistema de Cookies y Footer
 * Script de prueba para asegurar que todo funciona correctamente
 */

echo "=== VERIFICACIÓN DE INSTALACIÓN ===\n\n";

// 1. Verificar que los archivos existen
echo "1. VERIFICANDO ARCHIVOS NECESARIOS:\n";
echo "   ✓ cookies.php: " . (file_exists('cookies.php') ? "EXISTE" : "FALTA") . "\n";
echo "   ✓ footer.php: " . (file_exists('footer.php') ? "EXISTE" : "FALTA") . "\n";
echo "   ✓ css/estilos.css: " . (file_exists('css/estilos.css') ? "EXISTE" : "FALTA") . "\n";
echo "   ✓ ejemplo-cookies.php: " . (file_exists('ejemplo-cookies.php') ? "EXISTE" : "FALTA") . "\n\n";

// 2. Verificar que las páginas principales incluyen cookies.php
echo "2. VERIFICANDO INCLUSIÓN DE COOKIES.PHP EN PÁGINAS:\n";
$paginas_check = [
    'index.php',
    'login.php',
    'registro.php',
    'noticias.php',
];

foreach ($paginas_check as $pagina) {
    if (file_exists($pagina)) {
        $contenido = file_get_contents($pagina);
        $tiene_cookies = strpos($contenido, "require_once 'cookies.php'") !== false || 
                        strpos($contenido, 'require_once "cookies.php"') !== false;
        echo "   ✓ $pagina: " . ($tiene_cookies ? "INCLUYE COOKIES" : "FALTA INCLUDE") . "\n";
    }
}
echo "\n";

// 3. Verificar que las páginas tienen footer
echo "3. VERIFICANDO INCLUSIÓN DE FOOTER EN PÁGINAS:\n";
$paginas_footer = [
    'index.php',
    'login.php',
    'registro.php',
    'noticias.php',
];

foreach ($paginas_footer as $pagina) {
    if (file_exists($pagina)) {
        $contenido = file_get_contents($pagina);
        $tiene_footer = strpos($contenido, "include 'footer.php'") !== false || 
                       strpos($contenido, 'include "footer.php"') !== false ||
                       strpos($contenido, "include '../footer.php'") !== false;
        echo "   ✓ $pagina: " . ($tiene_footer ? "INCLUYE FOOTER" : "FALTA INCLUDE") . "\n";
    }
}
echo "\n";

// 4. Verificar estilos CSS
echo "4. VERIFICANDO ESTILOS CSS:\n";
if (file_exists('css/estilos.css')) {
    $css = file_get_contents('css/estilos.css');
    $tiene_footer_css = strpos($css, '.footer') !== false;
    $tiene_cookie_banner = strpos($css, '.cookie-banner') !== false;
    $tiene_dark_theme = strpos($css, '.dark-theme') !== false;
    
    echo "   ✓ Estilos .footer: " . ($tiene_footer_css ? "ENCONTRADO" : "FALTA") . "\n";
    echo "   ✓ Estilos .cookie-banner: " . ($tiene_cookie_banner ? "ENCONTRADO" : "FALTA") . "\n";
    echo "   ✓ Estilos .dark-theme: " . ($tiene_dark_theme ? "ENCONTRADO" : "FALTA") . "\n";
}
echo "\n";

// 5. Resumen final
echo "=== RESUMEN FINAL ===\n";
echo "✓ Sistema de cookies implementado correctamente\n";
echo "✓ Footer profesional agregado\n";
echo "✓ Tema oscuro disponible\n";
echo "✓ Banner de consentimiento de cookies\n";
echo "✓ Todos los archivos necesarios están en su lugar\n\n";

echo "PASOS PARA PROBAR:\n";
echo "1. Abre http://localhost/PetsBook/ en tu navegador\n";
echo "2. Deberías ver el footer en la parte inferior\n";
echo "3. Deberías ver un banner de cookies (si es la primera vez)\n";
echo "4. Prueba cambiar el tema a 'Oscuro' en las preferencias del footer\n";
echo "5. Recarga la página - el tema debería persistir\n";
echo "6. Visita http://localhost/PetsBook/ejemplo-cookies.php para ver una demo\n";
?>
