// DIAGRAMA DE FLUJO: Sistema de Cookies y Footer

/**
 * 
 * FLUJO DE COOKIES EN LA APLICACIÓN
 * ═════════════════════════════════════════════════════════════
 * 
 * Usuario Entra al Sitio
 *         │
 *         ├─→ ¿Tiene cookie 'petsbook_cookies_aceptadas'?
 *         │
 *         ├─ NO  → Mostrar Banner de Cookies en footer.php
 *         │         │
 *         │         ├→ Usuario Acepta → Guardar cookie (365 días)
 *         │         │                    └→ Banner desaparece
 *         │         │
 *         │         └→ Usuario Rechaza → Banner cierra
 *         │                              └→ Se muestra nuevamente mañana
 *         │
 *         └─ SÍ  → Ir directo al contenido, sin banner
 *
 */

/**
 * ESTRUCTURA DE CARPETAS ACTUALIZADA
 * ═════════════════════════════════════════════════════════════
 * 
 * PetsBook/
 * │
 * ├── 🆕 cookies.php                    ← Sistema de cookies
 * ├── 🆕 footer.php                     ← Footer profesional
 * ├── 🆕 ejemplo-cookies.php            ← Demo interactiva
 * ├── 🆕 COOKIES_Y_FOOTER_README.md     ← Documentación
 * ├── 🆕 IMPLEMENTACION-COOKIES-Y-FOOTER.md
 * ├── 🆕 VERIFICAR-INSTALACION.php
 * ├── 🔄 ACTUALIZADO: index.php
 * ├── 🔄 ACTUALIZADO: login.php
 * ├── 🔄 ACTUALIZADO: registro.php
 * ├── 🔄 ACTUALIZADO: noticias.php
 * ├── 🔄 ACTUALIZADO: conexion.php
 * │
 * ├── admin/
 * │   ├── 🔄 ACTUALIZADO: usuarios-administracion.php
 * │   ├── 🔄 ACTUALIZADO: citas-administracion.php
 * │   └── 🔄 ACTUALIZADO: noticias-administracion.php
 * │
 * ├── citaciones/
 * │   └── 🔄 ACTUALIZADO: citaciones.php
 * │
 * ├── usuarios/
 * │   └── 🔄 ACTUALIZADO: perfil.php
 * │
 * ├── css/
 * │   └── 🔄 ACTUALIZADO: estilos.css (+ 500 líneas de footer/cookies)
 * │
 * └── ... otros archivos
 * 
 */

/**
 * CÓMO FUNCIONA EL SISTEMA
 * ═════════════════════════════════════════════════════════════
 * 
 * 1. CARGA INICIAL
 *    ┌─────────────────────────────────────────────┐
 *    │ Usuario carga página (index.php, etc)       │
 *    │ require_once 'cookies.php'                  │
 *    │ include 'footer.php'                        │
 *    └─────────────────────────────────────────────┘
 *                         │
 *                         ↓
 * 2. INICIALIZACIÓN
 *    ┌─────────────────────────────────────────────┐
 *    │ cookies.php:                                │
 *    │ - Inicia sesión                             │
 *    │ - Define constantes DB_*                    │
 *    │ - Define funciones de cookies               │
 *    └─────────────────────────────────────────────┘
 *                         │
 *                         ↓
 * 3. VERIFICACIÓN DE PREFERENCIAS
 *    ┌─────────────────────────────────────────────┐
 *    │ obtener_preferencias_usuario()              │
 *    │ Devuelve array:                             │
 *    │ [                                           │
 *    │   'tema' => 'claro',                        │
 *    │   'idioma' => 'es',                         │
 *    │   'notificaciones' => 'activadas',          │
 *    │   'recordar_usuario' => 'no',               │
 *    │   'aceptar_cookies' => 'no'                 │
 *    │ ]                                           │
 *    └─────────────────────────────────────────────┘
 *                         │
 *                         ↓
 * 4. RENDERIZADO DEL FOOTER
 *    ┌─────────────────────────────────────────────┐
 *    │ footer.php:                                 │
 *    │ - Si no aceptó cookies → mostrar banner     │
 *    │ - Mostrar preferencias con valores actuales │
 *    │ - Agregar listeners JavaScript              │
 *    └─────────────────────────────────────────────┘
 *                         │
 *                         ↓
 * 5. INTERACCIÓN DEL USUARIO
 *    ┌─────────────────────────────────────────────┐
 *    │ Usuario puede:                              │
 *    │ a) Aceptar/Rechazar cookies                 │
 *    │ b) Cambiar tema (claro/oscuro)              │
 *    │ c) Cambiar notificaciones                   │
 *    │ d) Navegar por el sitio                     │
 *    └─────────────────────────────────────────────┘
 *                         │
 *                         ↓
 * 6. GUARDAR PREFERENCIAS
 *    ┌─────────────────────────────────────────────┐
 *    │ JavaScript en footer.php:                   │
 *    │ fetch('/PetsBook/cookies.php', {            │
 *    │   method: 'POST',                           │
 *    │   body: FormData({                          │
 *    │     accion: 'guardar_preferencias',         │
 *    │     tema: 'oscuro'                          │
 *    │   })                                        │
 *    │ })                                          │
 *    └─────────────────────────────────────────────┘
 *                         │
 *                         ↓
 * 7. PROCESAMIENTO EN cookies.php
 *    ┌─────────────────────────────────────────────┐
 *    │ Valida los valores:                         │
 *    │ - tema: debe ser 'claro' o 'oscuro'         │
 *    │ - idioma: debe ser 'es' o 'en'              │
 *    │ - notificaciones: 'activadas'|'desactivadas'│
 *    │                                             │
 *    │ Guarda con setcookie():                     │
 *    │ - Duración: 365 días                        │
 *    │ - Path: /PetsBook/                          │
 *    │ - HttpOnly: true (seguridad)                │
 *    │                                             │
 *    │ Retorna JSON: { exitoso: true }             │
 *    └─────────────────────────────────────────────┘
 *                         │
 *                         ↓
 * 8. APLICAR CAMBIOS EN CLIENTE
 *    ┌─────────────────────────────────────────────┐
 *    │ JavaScript aplica cambios:                  │
 *    │ - Si tema === 'oscuro'                      │
 *    │   document.body.classList.add('dark-theme')│
 *    │ - Si tema === 'claro'                       │
 *    │   document.body.classList.remove('dark-theme') │
 *    │                                             │
 *    │ Recarga página (opcional)                   │
 *    └─────────────────────────────────────────────┘
 *                         │
 *                         ↓
 * 9. PERSISTENCIA
 *    ┌─────────────────────────────────────────────┐
 *    │ Próxima carga de página:                    │
 *    │ - Las preferencias se leen de $_COOKIE      │
 *    │ - El tema se aplica automáticamente         │
 *    │ - El banner NO se muestra (ya aceptado)     │
 *    │ - Los controles muestran valores guardados  │
 *    └─────────────────────────────────────────────┘
 * 
 */

/**
 * FUNCIONES DISPONIBLES EN cookies.php
 * ═════════════════════════════════════════════════════════════
 * 
 * 1. establecer_cookie($nombre, $valor, $dias = 365)
 *    Guardar una cookie con duración especificada
 *    
 *    Ejemplo:
 *    establecer_cookie('petsbook_tema', 'oscuro', 30);
 * 
 * 
 * 2. obtener_cookie($nombre, $valor_defecto = null)
 *    Obtener el valor de una cookie
 *    
 *    Ejemplo:
 *    $tema = obtener_cookie('petsbook_tema', 'claro');
 * 
 * 
 * 3. eliminar_cookie($nombre)
 *    Eliminar una cookie
 *    
 *    Ejemplo:
 *    eliminar_cookie('petsbook_recordar');
 * 
 * 
 * 4. obtener_preferencias_usuario()
 *    Obtener todas las preferencias en un array
 *    
 *    Ejemplo:
 *    $prefs = obtener_preferencias_usuario();
 *    echo $prefs['tema']; // 'claro' o 'oscuro'
 * 
 * 
 * 5. guardar_preferencias($preferencias)
 *    Guardar múltiples preferencias a la vez
 *    
 *    Ejemplo:
 *    guardar_preferencias([
 *      'tema' => 'oscuro',
 *      'idioma' => 'es',
 *      'notificaciones' => 'desactivadas'
 *    ]);
 * 
 */

/**
 * ELEMENTOS DEL FOOTER
 * ═════════════════════════════════════════════════════════════
 * 
 * ┌─────────────────────────────────────────────────────────────┐
 * │                      FOOTER PROFESIONAL                     │
 * ├─────────────────────────────────────────────────────────────┤
 * │                                                             │
 * │  🐾 PETSBOOK          │  NAVEGACIÓN        │  LEGAL        │
 * │  ─────────────────────┼───────────────────┼───────────────│
 * │  Tu plataforma...     │  • Inicio          │  • Términos   │
 * │  ────────────────     │  • Noticias        │  • Privacidad │
 * │  f  𝕏  📷  ▶          │  • Mi Perfil       │  • Cookies    │
 * │                       │  • Cerrar Sesión   │  • Contacto   │
 * │                       │                    │               │
 * │  PREFERENCIAS         │                    │               │
 * │  ─────────────────────┼───────────────────┼───────────────│
 * │  🌙 Tema: [Oscuro▼]   │                    │               │
 * │  📢 Notificaciones:   │                    │               │
 * │     [Activadas▼]      │                    │               │
 * │                       │                    │               │
 * ├─────────────────────────────────────────────────────────────┤
 * │  © 2026 PetsBook. Todos los derechos reservados.           │
 * │  Hecho con 💜 para amantes de las mascotas                 │
 * └─────────────────────────────────────────────────────────────┘
 * 
 * 
 * BANNER DE COOKIES (Primera visita)
 * ├─────────────────────────────────────────────────────────────┤
 * │ 🍪 Utilizamos cookies para mejorar tu experiencia...       │
 * │ [Aceptar]  [Rechazar]                                       │
 * └─────────────────────────────────────────────────────────────┘
 * 
 */

/**
 * COLORES Y ESTILOS
 * ═════════════════════════════════════════════════════════════
 * 
 * TEMA CLARO (DEFAULT):
 * ┌─────────────────────┐
 * │ Fondo: #2c3e50      │ (Gris azulado oscuro)
 * │ Texto: #ecf0f1      │ (Blanco grisáceo)
 * │ Acento: #3498db     │ (Azul)
 * │ Enlaces: #bdc3c7    │ (Gris claro)
 * └─────────────────────┘
 * 
 * TEMA OSCURO:
 * ┌─────────────────────┐
 * │ Fondo: #0d1117      │ (Negro azulado)
 * │ Texto: #e0e0e0      │ (Blanco)
 * │ Acento: #238636     │ (Verde)
 * │ Enlaces: #58a6ff    │ (Azul claro)
 * └─────────────────────┘
 * 
 * ANIMACIONES:
 * - slideUp: Banner de cookies entra desde abajo (0.4s)
 * - fadeInUp: Secciones del footer aparecen con movimiento (0.6s)
 * - Transiciones hover suaves (0.3s)
 * 
 */

/**
 * RUTAS POST Y AJAX
 * ═════════════════════════════════════════════════════════════
 * 
 * POST /PetsBook/cookies.php
 * {
 *   "accion": "guardar_preferencias",
 *   "tema": "oscuro",
 *   "idioma": "es",
 *   "notificaciones": "activadas"
 * }
 * 
 * Respuesta:
 * {
 *   "exitoso": true,
 *   "mensaje": "Preferencias guardadas correctamente"
 * }
 * 
 * ───────────────────────────────────────────────────────────
 * 
 * POST /PetsBook/cookies.php
 * {
 *   "accion": "aceptar_cookies"
 * }
 * 
 * Respuesta:
 * {
 *   "exitoso": true
 * }
 * 
 */

/**
 * COMPATIBILIDAD Y REQUISITOS
 * ═════════════════════════════════════════════════════════════
 * 
 * PHP:         7.4+
 * MySQL:       5.7+
 * Navegadores: Chrome, Firefox, Safari, Edge (últimas versiones)
 * 
 * Dependencias: NINGUNA (código vanilla)
 * 
 */

/**
 * FLUJO DE TEMA OSCURO
 * ═════════════════════════════════════════════════════════════
 * 
 * 1. Usuario selecciona "Oscuro" en preferencias
 *                │
 *                ↓
 * 2. JavaScript envía POST a cookies.php
 *                │
 *                ↓
 * 3. Se guarda cookie petsbook_tema = "oscuro"
 *                │
 *                ↓
 * 4. JavaScript aplica clase a body: class="dark-theme"
 *                │
 *                ↓
 * 5. CSS aplica estilos del tema oscuro
 *                │
 *                ↓
 * 6. Próxima carga:
 *    - Se lee valor de $_COOKIE['petsbook_tema']
 *    - Se aplica automáticamente en el <body>
 *    - El tema persiste
 * 
 */
?>
