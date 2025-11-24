
-- --------------------------------------------------------
-- PASO PREVIO: Insertar Marcas (Necesario por la Foreign Key)
-- --------------------------------------------------------
INSERT INTO MARCA (NOMBRE) VALUES 
('Bayer'), ('Pfizer'), ('Cinfa'), ('Johnson & Johnson'), ('Nestlé Health'), 
('Roche'), ('GSK'), ('Sanofi'), ('Nivea'), ('Isdin');

-- --------------------------------------------------------
-- INSERTS DE PRODUCTOS (50 Registros)
-- --------------------------------------------------------

-- 1. Medicamentos Generales (Sin Receta)
INSERT INTO PRODUCTO (NOMBRE, CATEGORIA, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID) VALUES
('Aspirina C Efervescente 10 comp', 'Medicamento', FALSE, 8.50, 100, 1),
('Paracetamol 500mg 20 comp', 'Medicamento', FALSE, 2.50, 200, 3),
('Ibuprofeno 400mg 20 comp', 'Medicamento', FALSE, 3.10, 180, 3),
('Almax Forte 24 sobres', 'Medicamento', FALSE, 9.80, 50, 3),
('Gaviscon Forte Suspensión', 'Medicamento', FALSE, 11.20, 45, 7),
('Frenadol Complex 10 sobres', 'Medicamento', FALSE, 8.95, 120, 4),
('Bisolvon Antitusivo Jarabe', 'Medicamento', FALSE, 7.40, 60, 1),
('Dormidina 25mg 14 comp', 'Medicamento', FALSE, 6.50, 30, 3),
('Thrombocid Pomada 60g', 'Medicamento', FALSE, 5.75, 40, 8),
('Reflex Spray 130ml', 'Medicamento', FALSE, 12.30, 25, 7);

-- 2. Antibióticos y Medicamentos con Receta
INSERT INTO PRODUCTO (NOMBRE, CATEGORIA, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID) VALUES
('Amoxicilina 500mg 24 caps', 'Antibiótico', TRUE, 4.20, 80, 3),
('Augmentine 875/125mg 30 sobres', 'Antibiótico', TRUE, 6.50, 60, 7),
('Azitromicina 500mg 3 comp', 'Antibiótico', TRUE, 5.10, 40, 3),
('Ciprofloxacino 500mg 10 comp', 'Antibiótico', TRUE, 3.90, 30, 2),
('Monurol 3g 2 sobres', 'Antibiótico', TRUE, 9.10, 50, 5),
('Nolotil Ampollas 5u', 'Medicamento', TRUE, 3.50, 90, 8),
('Enantyum 25mg 20 comp', 'Medicamento', TRUE, 4.80, 100, 6),
('Ventolin Inhalador 100mcg', 'Medicamento', TRUE, 5.60, 70, 7),
('Orfidal 1mg 50 comp', 'Medicamento', TRUE, 2.80, 60, 2),
('Lexatin 1.5mg 30 caps', 'Medicamento', TRUE, 3.20, 55, 6);

-- 3. Cuidado Personal
INSERT INTO PRODUCTO (NOMBRE, CATEGORIA, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID) VALUES
('Gel de Baño pH Neutro 750ml', 'Cuidado personal', FALSE, 4.50, 50, 9),
('Champú Anticaída 400ml', 'Cuidado personal', FALSE, 15.90, 30, 10),
('Desodorante Roll-On 24h', 'Cuidado personal', FALSE, 2.99, 80, 9),
('Crema Hidratante Lata Azul', 'Cuidado personal', FALSE, 5.50, 65, 9),
('Fotoprotector Fusion Water SPF50', 'Cuidado personal', FALSE, 24.95, 40, 10),
('Pasta de Dientes Blanqueadora', 'Cuidado personal', FALSE, 3.80, 90, 7),
('Enjuague Bucal Menta Fresca', 'Cuidado personal', FALSE, 6.20, 45, 4),
('Bálsamo Labial Reparador', 'Cuidado personal', FALSE, 4.10, 100, 10),
('Crema de Manos Reparadora', 'Cuidado personal', FALSE, 3.50, 60, 9),
('Loción Corporal Aloe Vera', 'Cuidado personal', FALSE, 7.99, 35, 9);

-- 4. Primeros Auxilios
INSERT INTO PRODUCTO (NOMBRE, CATEGORIA, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID) VALUES
('Alcohol 96º 250ml', 'Primeros auxilios', FALSE, 1.50, 150, 3),
('Agua Oxigenada 250ml', 'Primeros auxilios', FALSE, 1.20, 140, 3),
('Povidona Yodada 50ml', 'Primeros auxilios', FALSE, 3.50, 80, 3),
('Tiritas Resistentes al Agua 20u', 'Primeros auxilios', FALSE, 2.90, 200, 4),
('Gasas Estériles 100u', 'Primeros auxilios', FALSE, 4.50, 90, 3),
('Venda Elástica 5m x 5cm', 'Primeros auxilios', FALSE, 1.80, 60, 3),
('Esparadrapo de Tela 5m', 'Primeros auxilios', FALSE, 2.10, 75, 3),
('Termómetro Digital Rápido', 'Primeros auxilios', FALSE, 8.90, 40, 1),
('Tijeras de Botiquín Acero', 'Primeros auxilios', FALSE, 5.50, 30, 3),
('Guantes de Látex Caja 100u', 'Primeros auxilios', FALSE, 7.90, 50, 3);

-- 5. Vitaminas y Nutrición
INSERT INTO PRODUCTO (NOMBRE, CATEGORIA, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID) VALUES
('Supradyn Activo 30 comp', 'Vitaminas', FALSE, 12.50, 40, 1),
('Vitamina C 1000mg Efervescente', 'Vitaminas', FALSE, 6.90, 60, 3),
('Multicentrum Mujer 30 comp', 'Vitaminas', FALSE, 14.20, 35, 2),
('Magnesio Total 5 100 comp', 'Vitaminas', FALSE, 8.80, 50, 5),
('Colágeno con Magnesio 300g', 'Vitaminas', FALSE, 16.50, 25, 5),
('Leche Infantil Inicio 1 800g', 'Nutricion', FALSE, 18.90, 40, 5),
('Papilla Cereales Sin Gluten', 'Nutricion', FALSE, 5.60, 50, 5),
('Ensure NutriVigor Chocolate', 'Nutricion', FALSE, 19.90, 20, 8),
('Batido Proteico Vainilla', 'Nutricion', FALSE, 2.50, 80, 5),
('Pedialyte Suero Oral 500ml', 'Nutricion', FALSE, 4.90, 60, 8);
-- -------------------------------------------------------------
-- INSERTAR USUARIO
-- -------------------------------------------------------------

INSERT INTO USUARIO (NOMBRE, CONTRASENHIA, ROL) VALUES
('Mark','1234','Administrador'), 
('Alejandro', '5678','Administrador'), 
('Javier','9090','Administrador');

-- ---------------------------------------------------------------
-- INSERTAR CARRITO
-- ---------------------------------------------------------------
-- DATOS PRUEBA
INSERT INTO CARRITO (ID_USUARIO, ID_PRODUCTO, TOTAL_COMPRA) VALUES
(1, 1, 5.50),  
(1, 3, 6.00),  
(2, 7, 10.00), 
(2, 14, 4.00), 
(3, 22, 25.00), 
(3, 5, 9.99),  
(1, 18, 7.90), 
(2, 8, 22.00), 
(1, 6, 15.50), 
(3, 12, 11.50); 


-- -----------------------------------------------------------------
-- INSERTAR PROVEEDOR
-- -----------------------------------------------------------------
INSERT INTO PROVEEDOR
 (NOMBRE, CONTACTO_NOMBRE, TELEFONO, EMAIL, DIRECCION) 
 VALUES
('Milagrito', 'Juan de Dios', '+34 611 111 411', 'diosmilagroso@milagrito.com', 'Maria de la Ó 27, Nave 3, Madrid'),
('XuxesPro', 'Armando Puerta', '+34 610 987 654', 'apu@xuxespro.com', 'Av. de la Innovación 45, Barcelona'),
('Curabien', 'Roberto Garcia', '+34 600 555 888', 'Robien@curabien.es', 'Calle del Comercio 12, Valencia'),
('ShampúNántene', 'Ana Vaquerizo', '+34 763 222 122', 'vaquerizate@shampunantene.com', 'Campoamor km 4, Sevilla'),
('MenosxMenos', 'Xi jinping Geronimo', '+34 546 564 546', 'jipitin@menosxmenos', 'Paseo de la flor 4, Girona');


-- ---------------------------------------------------------------
-- INSERTAR PRODUCTO_PROVEEDOR
-- ---------------------------------------------------------------

INSERT INTO PRODUCTO_PROVEEDOR 
(ID_PRODUCTO, ID_PROVEEDOR, PRECIO_COMPRA, FECHA_ULTIMA_COMPRA) 
VALUES

(1, 1, 3.50, '2023-10-01'),  
(2, 1, 1.80, '2023-10-05'), 
(3, 1, 3.50, '2023-10-10'),  
(4, 1, 5.00, '2023-09-20'), 
(5, 1, 7.50, '2023-09-25'),  
(6, 3, 4.00, '2023-11-01'),  
(7, 3, 5.50, '2023-11-02'),  
(8, 3, 9.00, '2023-08-15'),  
(16, 3, 3.00, '2023-11-10'), 
(19, 3, 12.00, '2023-10-30'), 
(9, 2, 6.00, '2023-09-15'),  
(10, 2, 7.00, '2023-09-18'), 
(11, 2, 8.50, '2023-09-20'), 
(12, 2, 14.00, '2023-10-05'), 
(33, 2, 18.00, '2023-10-12'), 
(23, 4, 15.00, '2023-07-20'), 
(24, 4, 2.50, '2023-10-01'), 
(34, 4, 28.00, '2023-06-15'), 
(45, 4, 120.00, '2023-05-10'),
(55, 4, 50.00, '2023-08-22');