-- --------------------------------------------------------
-- Archivo: sql/schema.sql
-- Descripción: Creación de la ESTRUCTURA Y BASE DE DATOS del Supermercado.
-- --------------------------------------------------------

-- --------------------------------------------------------
-- 1. Creación de la Base de Datos
-- --------------------------------------------------------
-- Creamos la base de datos si no existe.
-- Usamos utf8mb4 para soportar emojis y caracteres especiales.
DROP DATABASE IF EXISTS pharmasphere_db;

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



-- TABLA MARCA

CREATE TABLE IF NOT EXISTS MARCA (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    NOMBRE VARCHAR(150) NOT NULL UNIQUE
);

-- TABLA PRODUCTO

CREATE TABLE IF NOT EXISTS PRODUCTO (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    NOMBRE VARCHAR(150) NOT NULL UNIQUE,
    CATEGORIA ENUM ('Medicamento', 'Antibiótico','Cuidado personal', 
    'Primeros auxilios', 'Nutricion', 'Vitaminas','Otros') NOT NULL,
    RECETA BOOLEAN,
    PRECIO DECIMAL (10,2) NOT NULL,
    STOCK_DISPONIBLE INT NOT NULL DEFAULT 0,
    MARCA_ID INT NOT NULL,
FOREIGN KEY (MARCA_ID) REFERENCES MARCA(ID) ON DELETE RESTRICT
);

-- TABLA CARRITO

CREATE TABLE IF NOT EXISTS CARRITO (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    F_COMPRA DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ID_USUARIO INT NULL,
    ID_PRODUCTO INT NOT NULL,
    TOTAL_COMPRA DECIMAL (10,2) NOT NULL DEFAULT 0.00,
FOREIGN KEY (ID_PRODUCTO) REFERENCES PRODUCTO(ID) ON DELETE RESTRICT, 
FOREIGN KEY (ID_USUARIO) REFERENCES USUARIO(ID) ON DELETE SET NULL
);

-- TABLA AUDITORIA

CREATE TABLE IF NOT EXISTS AUDITORIA (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    NOMBRE VARCHAR (150),
    DETALLE TEXT,
    FECHA TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLA PROVEEDORES

CREATE TABLE IF NOT EXISTS PROVEEDOR (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    NOMBRE VARCHAR(150) NOT NULL UNIQUE,
    CONTACTO_NOMBRE VARCHAR(100),
    TELEFONO VARCHAR(20),
    EMAIL VARCHAR(150) UNIQUE,
    DIRECCION TEXT
);

CREATE TABLE IF NOT EXISTS PRODUCTO_PROVEEDOR (
    ID_PRODUCTO INT NOT NULL,
    ID_PROVEEDOR INT NOT NULL,
    PRECIO_COMPRA DECIMAL(10, 2), 
    FECHA_ULTIMA_COMPRA DATE,
    PRIMARY KEY (ID_PRODUCTO, ID_PROVEEDOR), 
    FOREIGN KEY (ID_PRODUCTO) REFERENCES PRODUCTO(ID) ON DELETE CASCADE,
    FOREIGN KEY (ID_PROVEEDOR) REFERENCES PROVEEDOR(ID) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- CREACION USUARIOS
-- --------------------------------------------------------

-- ELIMINAR SI EXISTE USUARIO 

DROP USER IF EXISTS 'admin_pharma'@'localhost';

-- Creación de usuarios ADMIN (COMPLETO)
CREATE USER 'admin_pharma'@'localhost' IDENTIFIED BY 'Admin_IAW_pharma';  
    GRANT ALL PRIVILEGES ON pharmasphere_db.* TO 'admin_pharma'@'localhost';
    FLUSH PRIVILEGES;
-- --------------------------------------------------------

