# Sistema de Cookies y Footer Mejorado - PetsBook

## 📋 Descripción General

Se ha implementado un sistema completo de gestión de cookies y un footer profesional mejorado en la aplicación PetsBook.

---

## 🍪 Sistema de Cookies

### Funcionalidades

#### 1. **Gestión de Preferencias de Usuario**
Las cookies almacenan las siguientes preferencias:
- **Tema**: Claro/Oscuro (``petsbook_tema``)
- **Idioma**: Español/Inglés (``petsbook_idioma``)
- **Notificaciones**: Activadas/Desactivadas (``petsbook_notificaciones``)
- **Recordar Usuario**: Sí/No (``petsbook_recordar``)
- **Aceptación de Cookies**: Sí/No (``petsbook_cookies_aceptadas``)

#### 2. **Duración de Cookies**
- Las cookies persisten durante **365 días** por defecto
- Se establece `httponly=true` para mayor seguridad
- Las cookies se restablecen en `/PetsBook/` como ruta

#### 3. **Banner de Consentimiento**
- Se muestra automáticamente si el usuario no ha aceptado las cookies
- Opciones para aceptar o rechazar
- Desaparece una vez aceptado

---

## 📁 Archivos Creados y Modificados

### Nuevos Archivos

#### [cookies.php](cookies.php)
Sistema de gestión de cookies con las siguientes funciones:

```php
// Establecer una cookie
establecer_cookie($nombre, $valor, $dias = 365);

// Obtener una cookie
obtener_cookie($nombre, $valor_defecto = null);

// Eliminar una cookie
eliminar_cookie($nombre);

// Obtener todas las preferencias
obtener_preferencias_usuario();

// Guardar preferencias
guardar_preferencias($preferencias);
```

**Rutas POST:**
- `POST /PetsBook/cookies.php?accion=guardar_preferencias` - Guardar preferencias
- `POST /PetsBook/cookies.php?accion=aceptar_cookies` - Aceptar banner de cookies

#### [footer.php](footer.php)
Footer profesional mejorado con:
- Información sobre PetsBook
- Enlaces de navegación
- Información legal (términos, privacidad, cookies)
- Controles de preferencias en tiempo real
- Redes sociales
- Banner de aceptación de cookies
- Diseño responsivo

### Archivos Modificados

#### CSS
- [css/estilos.css](css/estilos.css) - Agregados estilos para:
  - Footer con gradiente profesional
  - Tema oscuro (`dark-theme`)
  - Banner de cookies
  - Animaciones suave
  - Diseño responsivo

#### Páginas PHP Actualizadas
Las siguientes páginas ahora incluyen `cookies.php` y `footer.php`:

1. [index.php](index.php)
2. [login.php](login.php)
3. [registro.php](registro.php)
4. [noticias.php](noticias.php)
5. [citaciones/citaciones.php](citaciones/citaciones.php)
6. [usuarios/perfil.php](usuarios/perfil.php)
7. [admin/usuarios-administracion.php](admin/usuarios-administracion.php)
8. [admin/citas-administracion.php](admin/citas-administracion.php)
9. [admin/noticias-administracion.php](admin/noticias-administracion.php)

---

## 🎨 Características del Footer

### Secciones
1. **Sobre Nosotros**: Logo de PetsBook y descripción
2. **Navegación**: Enlaces rápidos a páginas principales
3. **Legal**: Enlaces a términos, privacidad y contacto
4. **Preferencias**: Controles de tema y notificaciones

### Elementos Visuales
- Gradiente profesional en color gris azulado
- Iconos emoji para fácil identificación
- Enlaces con efecto hover con animación
- Redes sociales con diseño circular
- Banda separadora con degradado

### Tema Oscuro
El footer se adapta automáticamente al tema oscuro con colores alternativos:
- Fondo oscuro: `#0d1117` a `#161b22`
- Color de acento: `#238636` (verde)
- Bordes y controles ajustados

---

## 🚀 Uso en Desarrollo

### Incluir en una Página
```php
<?php
session_start();
require_once 'cookies.php';
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <!-- Contenido de la página -->
    
    <?php include 'footer.php'; ?>
</body>
</html>
```

### Guardar Preferencias desde JavaScript
```javascript
function guardar_preferencias() {
    const tema = document.getElementById('preferencia-tema').value;
    const notificaciones = document.getElementById('preferencia-notificaciones').value;
    
    const formData = new FormData();
    formData.append('accion', 'guardar_preferencias');
    formData.append('tema', tema);
    formData.append('notificaciones', notificaciones);
    
    fetch('/PetsBook/cookies.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => console.log('Guardado:', data));
}
```

### Acceder a Preferencias en PHP
```php
<?php
require_once 'cookies.php';

$preferencias = obtener_preferencias_usuario();

echo $preferencias['tema'];           // 'claro' o 'oscuro'
echo $preferencias['idioma'];         // 'es' o 'en'
echo $preferencias['notificaciones']; // 'activadas' o 'desactivadas'
?>
```

---

## 🔒 Seguridad

- Las cookies se establecen con `httponly=true` para proteger contra ataques XSS
- Las cookies se validan en servidor antes de procesarse
- Los valores se escapan correctamente en HTML
- Se utiliza PDO para cualquier consulta a base de datos
- Las rutas POST están protegidas contra CSRF

---

## 📱 Responsividad

El footer es completamente responsivo:
- **Desktop**: Grid de 4 columnas
- **Tablet**: Grid adaptativo
- **Móvil**: Una sola columna, acciones apiladas

---

## 🎯 Próximas Mejoras (Opcional)

1. Implementar idioma seleccionable (actualmente solo "es")
2. Guardar tema en base de datos para usuarios logueados
3. Agregar más redes sociales
4. Modal para política de privacidad completa
5. Estadísticas de aceptación de cookies

---

## ✅ Verificación

Para verificar que el sistema funciona correctamente:

1. Abre cualquier página del sitio
2. Deberías ver el footer en la parte inferior
3. El banner de cookies debe aparecer si es la primera visita
4. Haz clic en "Aceptar" para guardar la preferencia
5. Cambia el tema a "Oscuro" en las preferencias
6. Recarga la página y el tema debería persistir

---

**Fecha de Implementación**: 3 de Febrero de 2026  
**Versión**: 1.0  
**Compatible con**: PHP 7.4+, MySQL 5.7+
