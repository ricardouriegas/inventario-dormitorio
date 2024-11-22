-- Base de datos para el sistema de inventario del cuarto
CREATE DATABASE IF NOT EXISTS InventarioCuarto;
USE InventarioCuarto;

-- Tabla para los ítems
CREATE TABLE Items (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para los muebles
CREATE TABLE Muebles (
    id_mueble INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    material VARCHAR(255),
    dimensiones VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para los inmuebles
CREATE TABLE Inmuebles (
    id_inmueble INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    ubicacion VARCHAR(255),
    dimensiones VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Relación entre ítems y muebles (un ítem puede estar en un mueble)
CREATE TABLE Items_Muebles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_item INT NOT NULL,
    id_mueble INT NOT NULL,
    FOREIGN KEY (id_item) REFERENCES Items(id_item) ON DELETE CASCADE,
    FOREIGN KEY (id_mueble) REFERENCES Muebles(id_mueble) ON DELETE CASCADE
);

-- Relación entre muebles e inmuebles (un mueble puede estar en un inmueble)
CREATE TABLE Muebles_Inmuebles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_mueble INT NOT NULL,
    id_inmueble INT NOT NULL,
    FOREIGN KEY (id_mueble) REFERENCES Muebles(id_mueble) ON DELETE CASCADE,
    FOREIGN KEY (id_inmueble) REFERENCES Inmuebles(id_inmueble) ON DELETE CASCADE
);

-- Tabla para el historial de búsquedas (opcional, para gestionar "Buscar Item")
CREATE TABLE Historial_Busquedas (
    id_busqueda INT AUTO_INCREMENT PRIMARY KEY,
    id_item INT,
    fecha_busqueda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_item) REFERENCES Items(id_item) ON DELETE SET NULL
);
