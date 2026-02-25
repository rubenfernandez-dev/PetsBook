<?php
/**
 * Sistema de gestión de cookies
 * Maneja cookies de preferencias de usuario
 */

// Función para establecer una cookie
function establecer_cookie($nombre, $valor, $dias = 365) {
    $tiempo_expiracion = time() + ($dias * 24 * 60 * 60);
    setcookie(
        $nombre,
        $valor,
        $tiempo_expiracion,
        "/PetsBook/",
        "",
        false,
        true // httponly para mayor seguridad
    );
}

// Función para obtener una cookie
function obtener_cookie($nombre, $valor_defecto = null) {
    return $_COOKIE[$nombre] ?? $valor_defecto;
}

// Función para eliminar una cookie
function eliminar_cookie($nombre) {
    setcookie(
        $nombre,
        "",
        time() - 3600,
        "/PetsBook/",
        "",
        false,
        true
    );
    unset($_COOKIE[$nombre]);
}

// Función para obtener todas las cookies de preferencias
function obtener_preferencias_usuario() {
    return [
        'tema' => obtener_cookie('petsbook_tema', 'claro'),
        'idioma' => obtener_cookie('petsbook_idioma', 'es'),
        'notificaciones' => obtener_cookie('petsbook_notificaciones', 'activadas'),
        'recordar_usuario' => obtener_cookie('petsbook_recordar', 'no'),
        'aceptar_cookies' => obtener_cookie('petsbook_cookies_aceptadas', 'no')
    ];
}

// Función para guardar preferencias
function guardar_preferencias($preferencias) {
    foreach ($preferencias as $nombre => $valor) {
        establecer_cookie('petsbook_' . $nombre, $valor);
    }
}

// Procesar cambios de preferencias por AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar_preferencias') {
    header('Content-Type: application/json');
    
    $tema = $_POST['tema'] ?? 'claro';
    $idioma = $_POST['idioma'] ?? 'es';
    $notificaciones = $_POST['notificaciones'] ?? 'activadas';
    
    // Validar valores
    if (!in_array($tema, ['claro', 'oscuro'])) {
        $tema = 'claro';
    }
    if (!in_array($idioma, ['es', 'en'])) {
        $idioma = 'es';
    }
    if (!in_array($notificaciones, ['activadas', 'desactivadas'])) {
        $notificaciones = 'activadas';
    }
    
    establecer_cookie('petsbook_tema', $tema);
    establecer_cookie('petsbook_idioma', $idioma);
    establecer_cookie('petsbook_notificaciones', $notificaciones);
    
    echo json_encode([
        'exitoso' => true,
        'mensaje' => 'Preferencias guardadas correctamente'
    ]);
    exit;
}

// Aceptar cookies (banner de consentimiento)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'aceptar_cookies') {
    establecer_cookie('petsbook_cookies_aceptadas', 'si', 365);
    header('Content-Type: application/json');
    echo json_encode(['exitoso' => true]);
    exit;
}
?>
