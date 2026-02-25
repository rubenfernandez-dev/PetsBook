# 🐾 PetsBook - Sistema de Gestión Veterinaria

Sistema web completo de gestión veterinaria desarrollado en PHP con MySQL. Permite la gestión de usuarios, citas veterinarias y publicación de noticias.

## 📋 Características Principales

### Para Usuarios
- ✅ Registro e inicio de sesión seguro
- 📅 Gestión de citas veterinarias (crear, editar, borrar)
- 📰 Visualización de noticias sobre mascotas
- 👤 Gestión de perfil personal
- 🔒 Cambio de contraseña encriptada

### Para Administradores
- 👥 CRUD completo de usuarios
- 📅 Gestión de citas de todos los usuarios
- 📰 CRUD completo de noticias
- 📊 Panel de administración centralizado
- 🔐 Control de roles (admin/user)

## 🗄️ Estructura de la Base de Datos

### Base de datos: `proyecto_final`

#### Tabla `users_data`
```sql
- idUser (INT PK AI)
- nombre (VARCHAR 100)
- apellidos (VARCHAR 150)
- email (VARCHAR 150 UNIQUE)
- telefono (VARCHAR 20)
- fecha_nacimiento (DATE)
- direccion (TEXT)
- sexo (VARCHAR 20)
```

#### Tabla `users_login`
```sql
- idLogin (INT PK AI)
- idUser (INT UNIQUE FK → users_data)
- usuario (VARCHAR 100 UNIQUE)
- password (VARCHAR 255) - encriptada con password_hash()
- rol (ENUM 'admin','user')
```

#### Tabla `citas`
```sql
- idCita (INT PK AI)
- idUser (INT FK → users_data)
- fecha_cita (DATE)
- motivo_cita (TEXT)
```

#### Tabla `noticias`
```sql
- idNoticia (INT PK AI)
- titulo (VARCHAR 200 UNIQUE)
- imagen (VARCHAR 255)
- texto (TEXT)
- fecha (DATE)
- idUser (INT FK → users_data)
```

## 📁 Estructura del Proyecto

```
PetsBook/
│
├── database.sql                    # Script de creación de BD
├── conexion.php                    # Conexión PDO a MySQL
├── navbar.php                      # Barra de navegación dinámica
├── index.php                       # Página principal
├── registro.php                    # Formulario de registro
├── login.php                       # Formulario de login
├── logout.php                      # Cerrar sesión
├── noticias.php                    # Listado público de noticias
│
├── usuarios/
│   ├── perfil.php                  # Ver perfil del usuario
│   └── actualizar.php              # Editar perfil y cambiar contraseña
│
├── citaciones/
│   ├── citaciones.php              # CRUD de citas del usuario
│   ├── crear.php                   # Redirección
│   ├── editar.php                  # Redirección
│   └── borrar.php                  # Redirección
│
├── admin/
│   ├── usuarios-administracion.php # CRUD de usuarios
│   ├── citas-administracion.php    # CRUD de citas
│   └── noticias-administracion.php # CRUD de noticias
│
├── procesos/
│   ├── registrar_usuario.php       # Procesar registro
│   ├── login_usuario.php           # Procesar login
│   ├── crear_cita.php              # Procesar creación de cita
│   ├── editar_cita.php             # Procesar edición de cita
│   ├── borrar_cita.php             # Procesar borrado de cita
│   ├── crear_noticia.php           # Procesar creación de noticia
│   ├── editar_noticia.php          # Procesar edición de noticia
│   ├── borrar_noticia.php          # Procesar borrado de noticia
│   ├── crear_usuario.php           # Procesar creación de usuario
│   ├── editar_usuario.php          # Procesar edición de usuario
│   └── borrar_usuario.php          # Procesar borrado de usuario
│
├── css/
│   └── estilos.css                 # Estilos CSS completos
│
├── js/
│   └── scripts.js                  # Scripts JavaScript
│
└── img/                            # Carpeta para imágenes
```

## 🚀 Instalación

### Requisitos Previos
- PHP 7.4 o superior
- MySQL 5.7 o superior / MariaDB 10.3+
- Servidor web (Apache, Nginx, o XAMPP/WAMP/MAMP)
- Extensión PDO de PHP habilitada

### Pasos de Instalación

1. **Clonar o descargar el proyecto**
   ```bash
   # Colocar los archivos en la carpeta del servidor web
   # Por ejemplo: C:\xampp\htdocs\PetsBook
   ```

2. **Crear la base de datos**
   ```bash
   # Abrir MySQL/MariaDB y ejecutar:
   mysql -u root -p < database.sql
   # O importar database.sql desde phpMyAdmin
   ```

3. **Configurar la conexión**
   - Abrir `conexion.php`
   - Verificar/modificar las credenciales:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'proyecto_final');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Crear carpeta de imágenes**
   ```bash
   # Asegurarse de que existe la carpeta img/
   # Y que tenga permisos de escritura
   ```

5. **Acceder al sistema**
   ```
   http://localhost/PetsBook/
   ```

## 👤 Usuarios de Prueba

Para crear un usuario administrador inicial, ejecutar este SQL después de crear la base de datos:

```sql
-- Crear usuario admin de prueba
INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo)
VALUES ('Admin', 'Sistema', 'admin@petsbook.com', '123456789', '1990-01-01', 'Oficina Central', 'Otro');

-- Obtener el ID insertado y usarlo aquí (normalmente será 1)
INSERT INTO users_login (idUser, usuario, password, rol)
VALUES (1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Contraseña: password
```

**Credenciales por defecto:**
- Usuario: `admin`
- Contraseña: `password`

## 🔐 Seguridad Implementada

✅ **Contraseñas encriptadas** con `password_hash()` y `password_verify()`  
✅ **Consultas parametrizadas** PDO para prevenir SQL Injection  
✅ **Validación de sesiones** en todas las páginas protegidas  
✅ **Validación de roles** (admin/user)  
✅ **Protección CSRF** mediante verificación de método POST  
✅ **Sanitización de datos** con `htmlspecialchars()` y `trim()`  
✅ **Transacciones PDO** para operaciones críticas  
✅ **Session regeneration** para prevenir session fixation  
✅ **Verificación de permisos** antes de editar/borrar  
✅ **Validación de fechas** (solo citas futuras editables)  

## 📱 Funcionalidades Detalladas

### Sistema de Registro y Login
- Formulario de registro con validación completa
- Verificación de edad mínima (13 años)
- Validación de emails y usuarios únicos
- Login con verificación de contraseña encriptada
- Mensajes de error/éxito con sesiones
- Repoblado de formularios en caso de error

### Gestión de Citas
- **Usuario normal:**
  - Ver todas sus citas (pasadas y futuras)
  - Crear nuevas citas
  - Editar/borrar solo citas futuras
  - Validación de fechas
  
- **Administrador:**
  - Seleccionar cualquier usuario
  - Ver todas las citas del usuario
  - Crear/editar/borrar citas para cualquier usuario

### Gestión de Noticias
- Vista pública de todas las noticias
- Visualización de autor y fecha
- **Solo administradores:**
  - Crear noticias con título único
  - Editar noticias existentes
  - Borrar noticias
  - Gestión de imágenes

### Gestión de Perfil
- Ver datos personales completos
- Editar información personal
- Cambiar contraseña (requiere contraseña actual)
- Usuario de login no modificable
- Estadísticas de citas y noticias

### Panel de Administración
- **Usuarios:**
  - Listar todos los usuarios
  - Crear usuarios (user o admin)
  - Editar información y rol
  - Borrar usuarios (con CASCADE)
  - No puede borrarse a sí mismo
  
- **Citas:**
  - Ver citas por usuario
  - CRUD completo
  - Validación de fechas
  
- **Noticias:**
  - CRUD completo
  - Validación de títulos únicos
  - Gestión de contenido

## 🎨 Interfaz de Usuario

- Diseño responsive con CSS Grid y Flexbox
- Navegación dinámica según rol de usuario
- Alertas con auto-cierre después de 5 segundos
- Tablas con hover effects
- Formularios con validación HTML5 y JavaScript
- Badges para estados (admin, user, pasada, futura)
- Cards para noticias y características
- Footer informativo

## 🔄 Flujo de Navegación

```
Visitante → index.php
           ├── registro.php → procesos/registrar_usuario.php → login.php
           ├── login.php → procesos/login_usuario.php → index.php (logged)
           └── noticias.php

Usuario → index.php (logged)
         ├── noticias.php
         ├── citaciones/citaciones.php
         │   └── CRUD citas propias
         ├── usuarios/perfil.php
         │   └── usuarios/actualizar.php
         └── logout.php → index.php

Admin → index.php (logged)
       ├── Todas las opciones de Usuario
       ├── admin/usuarios-administracion.php (CRUD usuarios)
       ├── admin/citas-administracion.php (CRUD citas)
       └── admin/noticias-administracion.php (CRUD noticias)
```

## 📝 Validaciones Implementadas

### Registro de Usuario
- Campos obligatorios completos
- Email válido y único
- Usuario único
- Contraseña mínimo 6 caracteres
- Confirmación de contraseña
- Edad mínima 13 años

### Citas
- Fecha obligatoria
- Fecha futura o actual
- Solo editar/borrar citas futuras
- Validación de propiedad (usuario)

### Noticias
- Título único y obligatorio
- Título máximo 200 caracteres
- Imagen obligatoria
- Texto obligatorio
- Fecha válida

### Usuarios (Admin)
- Email único
- Usuario único
- Contraseña mínimo 6 caracteres (al crear)
- Rol válido (user/admin)
- No eliminar a sí mismo

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 7.4+
- **Base de Datos:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** HTML5, CSS3, JavaScript ES6
- **Conexión BD:** PDO (PHP Data Objects)
- **Seguridad:** password_hash(), prepared statements, sesiones
- **Estilos:** CSS Grid, Flexbox, diseño responsive
- **Charset:** UTF-8 / UTF8MB4

## 📊 Características Técnicas

- **Arquitectura:** MVC simplificado
- **Patrón de diseño:** Separación de vistas y procesos
- **Manejo de errores:** Try-catch con PDOException
- **Transacciones:** BEGIN, COMMIT, ROLLBACK
- **Sesiones:** session_start(), session_regenerate_id()
- **Redirecciones:** header('Location: ...')
- **Mensajes:** Sistema de mensajes con sesiones

## 🐛 Debugging y Mantenimiento

### Errores Comunes y Soluciones

1. **Error de conexión a la base de datos**
   - Verificar credenciales en `conexion.php`
   - Asegurarse de que MySQL esté corriendo
   - Verificar que la base de datos `proyecto_final` existe

2. **Headers already sent**
   - No debe haber salida antes de `header()`
   - Verificar que no hay espacios antes de `<?php`
   - Usar `exit()` después de cada `header()`

3. **Sesión no funciona**
   - Verificar que `session_start()` esté al inicio
   - Verificar permisos de carpeta de sesiones
   - No usar salidas antes de session_start()

4. **Imágenes no se cargan**
   - Verificar ruta en el campo imagen
   - Usar rutas relativas (img/nombre.jpg)
   - Verificar permisos de carpeta img/

## 📄 Licencia

Este proyecto es de código abierto para fines educativos.

## 👨‍💻 Autor

Proyecto desarrollado como sistema de gestión veterinaria completo con PHP y MySQL.

---

**Fecha de creación:** Febrero 2026  
**Versión:** 1.0.0  
**Estado:** ✅ Completamente funcional y libre de errores
