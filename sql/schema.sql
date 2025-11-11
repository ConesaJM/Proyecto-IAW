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
-- 3. Tabla de Producto (Inventario)
-- --------------------------------------------------------

-- --------------------------------------------------------
-- 3. Tabla de Usuario (Inventario)
-- --------------------------------------------------------
CREATE TABLE USUARIO (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre  VARCHAR UNIQUE NOT NULL (50)
    contrasenhia VARCHAR NOT NULL(100)
    rol ENUM ('Administrador', 'Usuario')
)


CREATE TABLE MARCA (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    NOMBRE NOT NULL,
-- ESTA POR VER AUN
)

CREATE TABLE PRODUCTO (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    NOMBRE VARCHAR(50) NOT NULL,
    ACTIVO ENUM ('Medicamento', 'Antibiótico','Cuidado personal','Vitaminas','Otros') NOT NULL,
    RECETA BOOLEAN,
    PRECIO DECIMAL (10,2) NOT NULL
    STOCK_DISPONIBLE INT NOT NULL DEFAULT 0.00,
    MARCA_ID INT NOT NULL
FOREIGN KEY (MARCA_ID) REFERENCES MARCA(ID) ON DELETE SET NULL
);

CREATE TABLE CARRITO
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    F_COMPRA DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ID_USUARIO INT NULL
    ID_PRODUCTO INT NOT NULL
    TOTAL_COMPRA DECIMAL (10,2) NOT NULL DEFAULT 0.00,
FOREIGN KEY (ID_PRODUCTO) REFERENCES PRODUCTO(ID) ON DELETE SET NULL 
FOREIGN KEY (ID_USUARIO) REFERENCES USUARIO(ID) ON DELETE SET NULL

-----------------------------------------------------------
-- CREACION USUARIOS
-- --------------------------------------------------------
-- Creación de usuarios ADMIN (COMPLETO)
CREATE USER 'Mark'@'localhost' IDENTIFIED BY 'Admin_IAW_super';  
    GRANT ALL PRIVILEGES ON pharmasphere_db.* TO 'admin'@'localhost';
    FLUSH PRIVILEGES;

CREATE USER 'Javi'@'localhost' IDENTIFIED BY 'Admin_IAW_super';  
    GRANT ALL PRIVILEGES ON pharmasphere_db.* TO 'admin'@'localhost';
    FLUSH PRIVILEGES;

CREATE USER 'Ale'@'localhost' IDENTIFIED BY 'Admin_IAW_super';  
    GRANT ALL PRIVILEGES ON pharmasphere_db.* TO 'admin'@'localhost';
    FLUSH PRIVILEGES;
-- --------------------------------------------------------

