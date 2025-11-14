-- Agregar columnas faltantes a la tabla usuario
ALTER TABLE `usuario` ADD COLUMN `password` VARCHAR(255) DEFAULT NULL AFTER `telefono`;
ALTER TABLE `usuario` ADD COLUMN `rol` ENUM('admin', 'usuario', 'operador') DEFAULT 'usuario' AFTER `password`;
