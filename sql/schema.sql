-- --------------------------------------------------------
-- Archivo: sql/schema.sql
-- Descripción: Creación de la ESTRUCTURA Y BASE DE DATOS del Supermercado.
-- --------------------------------------------------------

-- --------------------------------------------------------
-- 1. Creación de la Base de Datos
-- --------------------------------------------------------
-- Creamos la base de datos si no existe.
-- Usamos utf8mb4 para soportar emojis y caracteres especiales.
CREATE DATABASE IF NOT EXISTS supermercado_db 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Selección de la Base de Datos
-- --------------------------------------------------------
-- Establecemos que esta base de datos va a ser nuestro esquema principal.
USE supermercado_db;

-- --------------------------------------------------------
-- 3. Tabla de Productos (Inventario)
-- --------------------------------------------------------
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    categoria VARCHAR(100),
    stock INT NOT NULL DEFAULT 0,
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0.00
);

-- USUARIOS
-- --------------------------------------------------------
-- Creación de usuario ADMIN (COMPLETO)
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'Admin_IAW_super';
    GRANT ALL PRIVILEGES ON supermercado.* TO 'admin'@'localhost';
    FLUSH PRIVILEGES;
-- --------------------------------------------------------

-- --------------------------------------------------------
-- Creación de usuario básico (FALTA AÑADIR PRIVILEGIOS)
CREATE USER 'user'@'localhost' IDENTIFIED BY 'Usuario_IAW_super';
-- --------------------------------------------------------