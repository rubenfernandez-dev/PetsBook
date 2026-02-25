-- =============================================
-- Base de datos: proyecto_final
-- Fecha de creación: 2026-02-03
-- =============================================

-- Crear la base de datos
DROP DATABASE IF EXISTS proyecto_final;
CREATE DATABASE proyecto_final;
USE proyecto_final;

-- =============================================
-- Tabla: users_data
-- Descripción: Almacena los datos personales de los usuarios
-- =============================================
CREATE TABLE users_data (
    idUser INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    direccion TEXT,
    sexo VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: users_login
-- Descripción: Almacena las credenciales y roles de usuarios
-- =============================================
CREATE TABLE users_login (
    idLogin INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    idUser INT UNIQUE NOT NULL,
    usuario VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') NOT NULL,
    CONSTRAINT fk_users_login_idUser 
        FOREIGN KEY (idUser) 
        REFERENCES users_data(idUser)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: citas
-- Descripción: Almacena las citas de los usuarios
-- =============================================
CREATE TABLE citas (
    idCita INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    idUser INT NOT NULL,
    fecha_cita DATE NOT NULL,
    motivo_cita TEXT,
    CONSTRAINT fk_citas_idUser 
        FOREIGN KEY (idUser) 
        REFERENCES users_data(idUser)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: noticias
-- Descripción: Almacena las noticias publicadas por usuarios
-- =============================================
CREATE TABLE noticias (
    idNoticia INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    titulo VARCHAR(200) UNIQUE NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    texto TEXT NOT NULL,
    fecha DATE NOT NULL,
    idUser INT NOT NULL,
    CONSTRAINT fk_noticias_idUser 
        FOREIGN KEY (idUser) 
        REFERENCES users_data(idUser)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Índices adicionales para optimización
-- =============================================
CREATE INDEX idx_users_data_email ON users_data(email);
CREATE INDEX idx_users_login_usuario ON users_login(usuario);
CREATE INDEX idx_citas_fecha ON citas(fecha_cita);
CREATE INDEX idx_noticias_fecha ON noticias(fecha);
