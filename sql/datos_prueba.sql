-- Datos de prueba para tienda_db
-- Ejecutar DESPUÉS de importar sql/database.sql
--
-- Usuarios (todos con password: password)
--   admin      / password   -> admin
--   cajero1    / password   -> cajero
--   invent1    / password   -> inventario
--   vendedor1  / password   -> usuario
--
-- phpMyAdmin: seleccionar tienda_db -> Importar este archivo
-- CLI: mysql -u root tienda_db < sql/datos_prueba.sql

USE tienda_db;

-- DELETE (no TRUNCATE): MySQL no deja vaciar tablas referenciadas por FK
DELETE FROM detalle_ventas;
DELETE FROM ventas;
DELETE FROM cajas;
DELETE FROM productos;
-- dejamos al admin del esquema; turnos no se tocan
DELETE FROM usuarios WHERE usuario <> 'admin';

ALTER TABLE detalle_ventas AUTO_INCREMENT = 1;
ALTER TABLE ventas AUTO_INCREMENT = 1;
ALTER TABLE cajas AUTO_INCREMENT = 1;
ALTER TABLE productos AUTO_INCREMENT = 1;

-- -------------------------------------------------
-- Usuarios adicionales
-- Hash bcrypt de la palabra "password"
-- -------------------------------------------------
INSERT INTO usuarios (nombre, usuario, password, rol, estado) VALUES
('María López', 'cajero1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cajero', TRUE),
('Carlos Ruiz', 'invent1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'inventario', TRUE),
('Ana Pérez', 'vendedor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario', TRUE),
('Pedro Gómez', 'cajero2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cajero', TRUE);

-- -------------------------------------------------
-- Catálogo de productos
-- -------------------------------------------------
INSERT INTO productos
(codigo, codigo_barras, nombre, descripcion, categoria, precio_compra, precio_venta, stock, stock_minimo, estado)
VALUES
('AB001', '7501000000011', 'Arroz 1kg', 'Arroz blanco de grano largo', 'Abarrotes', 18.50, 28.00, 80, 15, 'bueno'),
('AB002', '7501000000028', 'Frijol negro 1kg', 'Frijol negro empacado', 'Abarrotes', 22.00, 35.00, 60, 10, 'bueno'),
('AB003', '7501000000035', 'Aceite vegetal 1L', 'Aceite de cocina', 'Abarrotes', 28.00, 42.00, 45, 10, 'bueno'),
('AB004', '7501000000042', 'Azúcar 1kg', 'Azúcar refinada', 'Abarrotes', 16.00, 25.00, 70, 12, 'bueno'),
('AB005', '7501000000059', 'Sal de mesa 1kg', 'Sal yodada', 'Abarrotes', 5.00, 10.00, 100, 20, 'bueno'),
('AB006', '7501000000066', 'Harina de trigo 1kg', 'Harina para panadería', 'Abarrotes', 12.00, 20.00, 55, 10, 'bueno'),
('AB007', '7501000000073', 'Pasta spaghetti 500g', 'Pasta seca', 'Abarrotes', 8.50, 15.00, 90, 15, 'bueno'),
('AB008', '7501000000080', 'Atún en agua 140g', 'Lata de atún', 'Abarrotes', 12.00, 22.00, 40, 8, 'bueno'),

('BE001', '7502000000018', 'Agua embotellada 1.5L', 'Agua purificada', 'Bebidas', 6.00, 12.00, 120, 25, 'bueno'),
('BE002', '7502000000025', 'Refresco cola 600ml', 'Bebida gaseosa', 'Bebidas', 9.00, 18.00, 95, 20, 'bueno'),
('BE003', '7502000000032', 'Jugo de naranja 1L', 'Jugo natural pasteurizado', 'Bebidas', 14.00, 25.00, 35, 8, 'bueno'),
('BE004', '7502000000049', 'Leche entera 1L', 'Leche pasteurizada', 'Bebidas', 15.00, 24.00, 50, 10, 'bueno'),
('BE005', '7502000000056', 'Café soluble 100g', 'Café instantáneo', 'Bebidas', 45.00, 68.00, 25, 5, 'bueno'),

('SN001', '7503000000015', 'Galletas surtidas 200g', 'Paquete de galletas', 'Snacks', 10.00, 18.00, 65, 12, 'bueno'),
('SN002', '7503000000022', 'Papas fritas 45g', 'Botana salada', 'Snacks', 7.00, 14.00, 80, 15, 'bueno'),
('SN003', '7503000000039', 'Chocolate barra 40g', 'Chocolate con leche', 'Snacks', 8.00, 15.00, 70, 10, 'bueno'),
('SN004', '7503000000046', 'Chicles menta', 'Paquete 5 piezas', 'Snacks', 3.00, 7.00, 150, 30, 'bueno'),

('LI001', '7504000000012', 'Jabón de barra', 'Jabón de tocador', 'Limpieza', 6.50, 12.00, 48, 10, 'bueno'),
('LI002', '7504000000029', 'Detergente 1kg', 'Detergente en polvo', 'Limpieza', 22.00, 38.00, 30, 6, 'bueno'),
('LI003', '7504000000036', 'Cloro 1L', 'Blanqueador', 'Limpieza', 10.00, 18.00, 40, 8, 'bueno'),
('LI004', '7504000000043', 'Papel higiénico 4 rollos', 'Paquete familiar', 'Limpieza', 28.00, 45.00, 35, 8, 'bueno'),

('CA001', '7505000000019', 'Pan blanco pieza', 'Bolillo / pan del día', 'Panadería', 2.00, 4.50, 8, 20, 'regular'),
('CA002', '7505000000026', 'Huevo cartón 12 pzas', 'Huevo blanco', 'Lácteos y huevo', 32.00, 48.00, 22, 5, 'bueno'),
('CA003', '7505000000033', 'Queso panela 400g', 'Queso fresco', 'Lácteos y huevo', 38.00, 58.00, 12, 4, 'bueno'),

-- stock bajo / malo para probar alertas en la UI
('AB009', '7501000000097', 'Salsa cátsup 370g', 'Botella de cátsup', 'Abarrotes', 14.00, 24.00, 3, 8, 'bueno'),
('LI005', '7504000000050', 'Esponja cocina', 'Esponja abrasiva', 'Limpieza', 4.00, 9.00, 2, 10, 'regular'),
('SN005', '7503000000053', 'Dulces surtidos 100g', 'Bolsa de dulces', 'Snacks', 9.00, 16.00, 0, 5, 'malo');

-- -------------------------------------------------
-- Cajas (cerradas de días previos + una abierta hoy)
-- admin = id 1, cajero1 = id 2, cajero2 = id 5 (si el admin quedó como 1)
-- Usamos subconsultas por usuario para no depender del id exacto
-- -------------------------------------------------
INSERT INTO cajas (usuario_id, monto_inicial, monto_final, fecha, hora_apertura, hora_cierre, estado)
SELECT id, 500.00, 1280.50, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '08:05:00', '16:10:00', 'cerrada'
FROM usuarios WHERE usuario = 'cajero1';

INSERT INTO cajas (usuario_id, monto_inicial, monto_final, fecha, hora_apertura, hora_cierre, estado)
SELECT id, 500.00, 980.00, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:00:00', '15:55:00', 'cerrada'
FROM usuarios WHERE usuario = 'cajero1';

INSERT INTO cajas (usuario_id, monto_inicial, monto_final, fecha, hora_apertura, hora_cierre, estado)
SELECT id, 400.00, 760.25, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '16:00:00', '21:45:00', 'cerrada'
FROM usuarios WHERE usuario = 'cajero2';

-- caja abierta de hoy (para entrar al POS sin abrir otra)
INSERT INTO cajas (usuario_id, monto_inicial, monto_final, fecha, hora_apertura, hora_cierre, estado)
SELECT id, 500.00, NULL, CURDATE(), '08:15:00', NULL, 'abierta'
FROM usuarios WHERE usuario = 'cajero1';

-- -------------------------------------------------
-- Ventas de prueba (últimos días)
-- -------------------------------------------------

-- Venta 1: hace 2 días, efectivo
INSERT INTO ventas (usuario_id, caja_id, total, metodo_pago, fecha_venta)
SELECT u.id, c.id, 91.00, 'efectivo', CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 09:20:00')
FROM usuarios u
JOIN cajas c ON c.usuario_id = u.id
WHERE u.usuario = 'cajero1'
  AND c.fecha = DATE_SUB(CURDATE(), INTERVAL 2 DAY)
LIMIT 1;

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT v.id, p.id, 2, 28.00, 56.00
FROM ventas v, productos p
WHERE p.codigo = 'AB001'
ORDER BY v.id DESC LIMIT 1;

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 1, 35.00, 35.00
FROM ventas v, productos p
WHERE p.codigo = 'AB002';

-- Venta 2: hace 2 días, tarjeta
INSERT INTO ventas (usuario_id, caja_id, total, metodo_pago, fecha_venta)
SELECT u.id, c.id, 108.00, 'tarjeta', CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 11:45:00')
FROM usuarios u
JOIN cajas c ON c.usuario_id = u.id
WHERE u.usuario = 'cajero1'
  AND c.fecha = DATE_SUB(CURDATE(), INTERVAL 2 DAY)
LIMIT 1;

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 2, 42.00, 84.00
FROM ventas v, productos p WHERE p.codigo = 'AB003';

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 2, 12.00, 24.00
FROM ventas v, productos p WHERE p.codigo = 'BE001';

-- Venta 3: ayer mañana
INSERT INTO ventas (usuario_id, caja_id, total, metodo_pago, fecha_venta)
SELECT u.id, c.id, 140.00, 'efectivo', CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 10:05:00')
FROM usuarios u
JOIN cajas c ON c.usuario_id = u.id
WHERE u.usuario = 'cajero1'
  AND c.fecha = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
  AND c.hora_apertura = '08:00:00'
LIMIT 1;

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 3, 18.00, 54.00
FROM ventas v, productos p WHERE p.codigo = 'BE002';

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 2, 24.00, 48.00
FROM ventas v, productos p WHERE p.codigo = 'BE004';

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 1, 38.00, 38.00
FROM ventas v, productos p WHERE p.codigo = 'LI002';

-- Venta 4: ayer tarde (cajero2)
INSERT INTO ventas (usuario_id, caja_id, total, metodo_pago, fecha_venta)
SELECT u.id, c.id, 133.00, 'tarjeta', CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 18:30:00')
FROM usuarios u
JOIN cajas c ON c.usuario_id = u.id
WHERE u.usuario = 'cajero2'
  AND c.fecha = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
LIMIT 1;

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 1, 68.00, 68.00
FROM ventas v, productos p WHERE p.codigo = 'BE005';

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 1, 45.00, 45.00
FROM ventas v, productos p WHERE p.codigo = 'LI004';

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 2, 10.00, 20.00
FROM ventas v, productos p WHERE p.codigo = 'AB005';

-- Venta 5: hoy (caja abierta de cajero1)
INSERT INTO ventas (usuario_id, caja_id, total, metodo_pago, fecha_venta)
SELECT u.id, c.id, 72.00, 'efectivo', CONCAT(CURDATE(), ' 09:40:00')
FROM usuarios u
JOIN cajas c ON c.usuario_id = u.id
WHERE u.usuario = 'cajero1'
  AND c.fecha = CURDATE()
  AND c.estado = 'abierta'
LIMIT 1;

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 2, 15.00, 30.00
FROM ventas v, productos p WHERE p.codigo = 'AB007';

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 2, 14.00, 28.00
FROM ventas v, productos p WHERE p.codigo = 'SN002';

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 1, 14.00, 14.00
FROM ventas v, productos p WHERE p.codigo = 'BE002';

-- Venta 6: hoy tarjeta
INSERT INTO ventas (usuario_id, caja_id, total, metodo_pago, fecha_venta)
SELECT u.id, c.id, 106.00, 'tarjeta', CONCAT(CURDATE(), ' 11:15:00')
FROM usuarios u
JOIN cajas c ON c.usuario_id = u.id
WHERE u.usuario = 'cajero1'
  AND c.fecha = CURDATE()
  AND c.estado = 'abierta'
LIMIT 1;

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 1, 48.00, 48.00
FROM ventas v, productos p WHERE p.codigo = 'CA002';

INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
SELECT MAX(v.id), p.id, 1, 58.00, 58.00
FROM ventas v, productos p WHERE p.codigo = 'CA003';

-- -------------------------------------------------
-- Verificación rápida
-- -------------------------------------------------
-- SELECT COUNT(*) AS productos FROM productos;
-- SELECT COUNT(*) AS ventas FROM ventas;
-- SELECT usuario, rol FROM usuarios;
