-- Agregar columna estado a tabla ruta y parada
ALTER TABLE `ruta` ADD COLUMN `estado` ENUM('Activo','Inactivo') DEFAULT 'Activo' AFTER `id_linea`;
ALTER TABLE `parada` ADD COLUMN `estado` ENUM('Activo','Inactivo') DEFAULT 'Activo' AFTER `longitud`;

-- Asegurarse que filas existentes estén en Activo
UPDATE `ruta` SET `estado` = 'Activo' WHERE `estado` IS NULL;
UPDATE `parada` SET `estado` = 'Activo' WHERE `estado` IS NULL;
