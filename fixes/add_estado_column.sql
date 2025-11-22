-- Agregar columna de estado a la tabla usuario
ALTER TABLE `usuario` ADD COLUMN `estado` ENUM('Activo', 'Inactivo') DEFAULT 'Activo' AFTER `rol`;

-- Actualizar usuarios existentes a estado Activo (por si acaso)
UPDATE `usuario` SET `estado` = 'Activo' WHERE `estado` IS NULL;
