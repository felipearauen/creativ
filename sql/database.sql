<?php
/**
 * Esquema local de la BD. Importar desde phpMyAdmin o:
 * mysql -u root < sql/database.sql
 */

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS tienda_db;
USE tienda_db;

-- Eliminar tablas si existen
DROP TABLE IF EXISTS detalle_ventas;
DROP TABLE IF EXISTS ventas;
DROP TABLE IF EXISTS cajas;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS turnos;

-- Crear tabla usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cajero', 'inventario', 'usuario') NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla turnos
CREATE TABLE turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dia_semana TINYINT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    descripcion VARCHAR(100)
);

-- Crear tabla productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    codigo_barras VARCHAR(50) UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(50) NOT NULL,
    precio_compra DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 5,
    estado ENUM('bueno', 'regular', 'malo') NOT NULL DEFAULT 'bueno',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla cajas
CREATE TABLE cajas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    monto_inicial DECIMAL(10,2) NOT NULL,
    monto_final DECIMAL(10,2),
    fecha DATE NOT NULL,
    hora_apertura TIME NOT NULL,
    hora_cierre TIME,
    estado ENUM('abierta', 'cerrada') NOT NULL DEFAULT 'abierta',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Crear tabla ventas
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    caja_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta') NOT NULL,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (caja_id) REFERENCES cajas(id)
);

-- Crear tabla detalle_ventas
CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- admin / password (hash bcrypt de "password")
INSERT INTO usuarios (nombre, usuario, password, rol) VALUES
('Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO turnos (dia_semana, hora_inicio, hora_fin, descripcion) VALUES
(1, '08:00:00', '16:00:00', 'Turno mañana L-V'),
(1, '16:00:00', '22:00:00', 'Turno tarde L-V'),
(6, '08:00:00', '14:00:00', 'Turno mañana S-D'),
(6, '14:00:00', '20:00:00', 'Turno tarde S-D');

ALTER TABLE productos ADD INDEX idx_codigo (codigo);
ALTER TABLE productos ADD INDEX idx_codigo_barras (codigo_barras);
ALTER TABLE detalle_ventas ADD INDEX idx_venta (venta_id);
ALTER TABLE detalle_ventas ADD INDEX idx_producto (producto_id);
