CREATE DATABASE IF NOT EXISTS bd_alquiler_maquinaria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bd_alquiler_maquinaria;

CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('Administrador', 'Encargado') NOT NULL,
    estado TINYINT(1) DEFAULT 1
);

CREATE TABLE cliente (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    documento VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(120),
    direccion VARCHAR(200)
);

CREATE TABLE categoria_equipo (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE equipo (
    id_equipo INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    marca VARCHAR(80),
    modelo VARCHAR(80),
    precio_alquiler DECIMAL(10,2) NOT NULL,
    estado_operativo ENUM('Disponible', 'Ocupado', 'Mantenimiento') DEFAULT 'Disponible',
    FOREIGN KEY (id_categoria) REFERENCES categoria_equipo(id_categoria)
);

CREATE TABLE mantenimiento_equipo (
    id_mantenimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_equipo INT NOT NULL,
    fecha_ingreso DATE NOT NULL,
    fecha_salida DATE NULL,
    tipo_mantenimiento VARCHAR(80) NOT NULL,
    descripcion TEXT NOT NULL,
    costo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado_mantenimiento ENUM('En Taller', 'Finalizado') NOT NULL DEFAULT 'En Taller',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_equipo) REFERENCES equipo(id_equipo) ON DELETE CASCADE
);

CREATE TABLE contrato_alquiler (
    id_contrato INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cliente INT NOT NULL,
    numero_contrato VARCHAR(50) NOT NULL UNIQUE,
    fecha_contrato DATE NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin_prevista DATE NOT NULL,
    importe_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado ENUM('Activo', 'Devuelto', 'Vencido') DEFAULT 'Activo',
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
);

CREATE TABLE detalle_contrato (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_contrato INT NOT NULL,
    id_equipo INT NOT NULL,
    precio_alquiler DECIMAL(10,2) NOT NULL,
    cantidad_dias INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_contrato) REFERENCES contrato_alquiler(id_contrato),
    FOREIGN KEY (id_equipo) REFERENCES equipo(id_equipo)
);

CREATE TABLE devolucion (
    id_devolucion INT AUTO_INCREMENT PRIMARY KEY,
    id_detalle INT NOT NULL UNIQUE,
    fecha_devolucion DATE NOT NULL,
    observacion TEXT,
    condicion_equipo VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_detalle) REFERENCES detalle_contrato(id_detalle)
);

CREATE TABLE notificacion (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_contrato INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_generacion DATETIME NOT NULL,
    fecha_envio DATETIME NULL,
    estado ENUM('Pendiente', 'Enviado', 'Leido') DEFAULT 'Pendiente',
    FOREIGN KEY (id_contrato) REFERENCES contrato_alquiler(id_contrato)
);

INSERT INTO usuario (nombre, apellido, correo, contrasena, rol, estado) VALUES
('Darwin', 'Admin', 'admin@empresam.com', '123456', 'Administrador', 1),
('Carlos', 'Encargado', 'encargado@empresam.com', '123456', 'Encargado', 1);bd_alquiler_maquinariabd_alquiler_maquinaria