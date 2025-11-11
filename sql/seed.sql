
-------------------------------------------------------------------
-- INSERTS MARCA
-------------------------------------------------------------------
INSERT INTO MARCA (NOMBRE) VALUES
('Medicinis forte')
('Vitaminic pro')
('Abiotic')
('Cuidate')

-------------------------------------------------------------------
-- INSERTS PRODUCTOS 
-------------------------------------------------------------------

INSERT INTO PRODUCTO (NOMBRE, ACTIVO, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID) VALUES
('Aspirina', 'Medicamento', TRUE, 5.50, 100, 1),
('Paracetamol', 'Medicamento', TRUE, 3.20, 150, 1),
('Ibuprofeno', 'Medicamento', TRUE, 6.00, 120, 1),
('Antibiótico Amoxicilina', 'Antibiótico', TRUE, 8.30, 80, 3),
('Antibiótico Ciprofloxacino', 'Antibiótico', TRUE, 12.50, 60, 3),
('Shampoo Anti Caspa', 'Cuidado personal', FALSE, 7.00, 200, 4),
('Crema Hidratante', 'Cuidado personal', FALSE, 9.99, 250, 4),
('Protector Solar', 'Cuidado personal', FALSE, 15.50, 90, 4),
('Vitaminas A', 'Vitaminas', FALSE, 10.00, 300, 2),
('Vitaminas C', 'Vitaminas', FALSE, 11.50, 400, 2),
('Multivitamínico', 'Vitaminas', FALSE, 14.00, 110, 2),
('Suplemento Omega 3', 'Vitaminas', FALSE, 22.00, 75, 2),
('Vitamina D', 'Vitaminas', FALSE, 6.50, 50, 2),
('Gel Antibacterial', 'Otros', FALSE, 4.00, 500, 4),
('Alcohol en Gel', 'Otros', FALSE, 3.50, 450, 4),
('Mascarilla Facial', 'Cuidado personal', FALSE, 5.50, 180, 4),
('Cepillo de Dientes', 'Cuidado personal', FALSE, 2.99, 300, 4),
('Pasta Dental', 'Cuidado personal', FALSE, 3.20, 350, 4),
('Colonia Hombre', 'Cuidado personal', FALSE, 20.00, 150, 4),
('Colonia Mujer', 'Cuidado personal', FALSE, 18.50, 140, 4),
('Bálsamo Labial', 'Cuidado personal', FALSE, 2.80, 200, 4),
('Banda elástica para ejercicios', 'Otros', FALSE, 12.00, 100, 4),
('Termómetro digital', 'Otros', FALSE, 25.00, 60, 4),
('Guantes de Látex', 'Otros', FALSE, 5.00, 500, 4),
('Medicamento Antigripal', 'Medicamento', TRUE, 7.80, 110, 1),
('Jarabe para la tos', 'Medicamento', TRUE, 9.90, 95, 1),
('Parche Transdérmico', 'Medicamento', TRUE, 19.99, 85, 1),
('Lentes de Sol', 'Otros', FALSE, 35.00, 45, 4),
('Spray Nasal', 'Medicamento', TRUE, 8.00, 100, 1),
('Manta Eléctrica', 'Otros', FALSE, 40.00, 25, 4),
('Pastillas para la digestión', 'Medicamento', TRUE, 10.50, 200, 1),
('Pomada para el dolor', 'Medicamento', TRUE, 13.00, 95, 1),
('Crema para quemaduras', 'Medicamento', TRUE, 7.90, 120, 1),
('Suplemento Proteína', 'Vitaminas', FALSE, 29.99, 60, 2),
('Báscula Digital', 'Otros', FALSE, 45.00, 80, 4),
('Desodorante', 'Cuidado personal', FALSE, 3.60, 220, 4),
('Aceite Esencial', 'Cuidado personal', FALSE, 9.99, 140, 4),
('Pañuelos de Papel', 'Otros', FALSE, 1.50, 500, 4),
('Papel Higiénico', 'Otros', FALSE, 2.20, 1000, 4),
('Enjuague Bucal', 'Cuidado personal', FALSE, 4.50, 180, 4),
('Pasta Dental Sensible', 'Cuidado personal', FALSE, 4.20, 120, 4),
('Toallitas Húmedas', 'Cuidado personal', FALSE, 2.90, 250, 4),
('Crema Antiinflamatoria', 'Medicamento', TRUE, 14.50, 80, 1),
('Jabón Líquido', 'Cuidado personal', FALSE, 5.80, 300, 4),
('Cámara Termográfica', 'Otros', FALSE, 200.00, 10, 4),
('Termómetro de Mercurio', 'Otros', FALSE, 8.00, 40, 4),
('Cinta Métrica', 'Otros', FALSE, 1.99, 100, 4),
('Spray Desinfectante', 'Otros', FALSE, 3.00, 400, 4),
('Aceite de Oliva', 'Otros', FALSE, 7.50, 300, 4),
('Gel para Quemaduras', 'Medicamento', TRUE, 12.90, 70, 1),
('Vaselina', 'Cuidado personal', FALSE, 4.50, 150, 4),
('Lentes de Contacto', 'Cuidado personal', FALSE, 25.00, 30, 4),
('Jabón Antibacterial', 'Cuidado personal', FALSE, 2.60, 500, 4),
('Báscula de Cocina', 'Otros', FALSE, 20.00, 50, 4),
('Monitor de Presión Arterial', 'Otros', FALSE, 80.00, 20, 4);

-----------------------------------------------------------------
--- CREACION CARRITO
-----------------------------------------------------------------

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


