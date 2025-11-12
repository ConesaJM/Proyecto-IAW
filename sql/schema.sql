-- --------------------------------------------------------
-- Archivo: sql/schema.sql
-- Descripción: Creación de la ESTRUCTURA Y BASE DE DATOS del Supermercado.
-- --------------------------------------------------------

-- --------------------------------------------------------
-- 1. Creación de la Base de Datos
-- --------------------------------------------------------
-- Creamos la base de datos si no existe.
-- Usamos utf8mb4 para soportar emojis y caracteres especiales.
CREATE DATABASE IF NOT EXISTS pharmasphere_db 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Selección de la Base de Datos
-- --------------------------------------------------------
-- Establecemos que esta base de datos va a ser nuestro esquema principal.
USE pharmasphere_db;


-- --------------------------------------------------------
-- 3. CREACIÓN DE TABLAS
-- --------------------------------------------------------


-- TABLA USUARIO

CREATE TABLE USUARIO (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    NOMBRE VARCHAR (150) UNIQUE NOT NULL,
    CONTRASENHIA VARCHAR NOT NULL(255),
    ROL ENUM ('Administrador', 'Usuario')
);

-- TABLA MARCA

CREATE TABLE MARCA (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    NOMBRE VARCHAR(150) NOT NULL,
);

-- TABLA PRODUCTO

CREATE TABLE PRODUCTO (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    NOMBRE VARCHAR(150) NOT NULL,
    ACTIVO ENUM ('Medicamento', 'Antibiótico','Cuidado personal','Vitaminas','Otros') NOT NULL,
    RECETA BOOLEAN,
    PRECIO DECIMAL (10,2) NOT NULL,
    STOCK_DISPONIBLE INT NOT NULL DEFAULT 0.00,
    MARCA_ID INT NOT NULL,
FOREIGN KEY (MARCA_ID) REFERENCES MARCA(ID) ON DELETE RESTRIC
);

-- TABLA CARRITO

CREATE TABLE CARRITO (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    F_COMPRA DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ID_USUARIO INT NULL,
    ID_PRODUCTO INT NOT NULL,
    TOTAL_COMPRA DECIMAL (10,2) NOT NULL DEFAULT 0.00,
FOREIGN KEY (ID_PRODUCTO) REFERENCES PRODUCTO(ID) ON DELETE RESTRIC, 
FOREIGN KEY (ID_USUARIO) REFERENCES USUARIO(ID) ON DELETE SET NULL
);
-----------------------------------------------------------
-- CREACION USUARIOS
-- --------------------------------------------------------
-- Creación de usuarios ADMIN (COMPLETO)
CREATE USER 'admin_pharma'@'localhost' IDENTIFIED BY 'AdminAdmin_IAW_pharma_IAW_super';  
    GRANT ALL PRIVILEGES ON pharmasphere_db.* TO 'admin'@'localhost';
    FLUSH PRIVILEGES;
-- --------------------------------------------------------

