-- Base de datos S.I.G.S.M
CREATE DATABASE IF NOT EXISTS sigsm
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sigsm;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cedula VARCHAR(20) NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  contrasena VARCHAR(255) NOT NULL,
  rol VARCHAR(50) NOT NULL DEFAULT 'Operador',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_usuarios_cedula (cedula)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuarios iniciales (contraseña = cédula)
INSERT INTO usuarios (cedula, nombre, contrasena, rol) VALUES
  ('1234567', 'María González', '1234567', 'Administrador'),
  ('2345678', 'Carlos Rodríguez', '2345678', 'Supervisor'),
  ('3456789', 'Ana Martínez', '3456789', 'Operador'),
  ('4567890', 'Luis Fernández', '4567890', 'Operador')
ON DUPLICATE KEY UPDATE
  nombre = VALUES(nombre),
  contrasena = VALUES(contrasena),
  rol = VALUES(rol);
