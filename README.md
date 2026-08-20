# PetsBook

Proyecto final del módulo (PHP + MySQL).

**Academic project / legacy project.**

## Qué es

Sitio web básico para una clínica veterinaria ficticia.

Incluye:

- Registro e inicio de sesión
- Noticias públicas
- Gestión de citas
- Perfil de usuario
- Panel de administración (usuarios, citas, noticias)

## Tecnologías usadas

- HTML
- CSS
- JavaScript
- PHP
- MySQL

## Instalación rápida

1. Copiar la carpeta `PetsBook` dentro de `htdocs` (XAMPP).
2.  Importar el archivo `database.sql` en phpMyAdmin.
3.   Completar la conexión en `config.local.php`.
4.    Abrir en el navegador: `http://localhost/PetsBook/`

## Instrucciones para corrección del proyecto

- Ruta de montaje recomendada para emular el entorno local: `C:/xampp/htdocs/PetsBook`
- Importar la copia de base de datos incluida en `database.sql` en su servidor.
- Configuración de conexión: el archivo `conexion.php` utiliza las variables definidas en `config.local.php`. En esta entrega, las cadenas de conexión se dejan vacías por seguridad y para evitar incompatibilidades entre entornos; tras importar la base de datos, completar en `config.local.php` los datos de su servidor (`host`, `port`, `name`, `user`, `pass`).
- Gestor de base de datos: el gestor usado durante el desarrollo no afecta a la corrección, ya que la evaluación se realiza importando `database.sql` en el servidor de corrección.

## Usuarios y roles

- **Visitante**: puede ver inicio, noticias, registro y login.
- **User**: además puede usar perfil y citaciones.
- **Admin**: además puede administrar usuarios, citas y noticias.

## Archivos principales

- `index.php` → portada
- `noticias.php` → noticias públicas
- `registro.php` → alta de usuarios
- `login.php` → inicio de sesión
- `usuarios/perfil.php` → perfil
- `citaciones/citaciones.php` → gestión de citas
- `admin/` → panel de administración
- `database.sql` → base de datos

## Nota

La contraseña se guarda cifrada en la base de datos usando funciones de PHP.
