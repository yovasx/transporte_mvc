-- Agregar columna estado a tabla ruta y parada si no existe
SET @db := 'transportedb';

-- Parada
SELECT COUNT(*) INTO @c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'parada' AND COLUMN_NAME = 'estado';
SET @sql = IF(@c = 0, 'ALTER TABLE `parada` ADD COLUMN `estado` ENUM(\'Activo\',\'Inactivo\') DEFAULT \'Activo\' AFTER `longitud`;', 'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Ruta
SELECT COUNT(*) INTO @c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'ruta' AND COLUMN_NAME = 'estado';
SET @sql = IF(@c = 0, 'ALTER TABLE `ruta` ADD COLUMN `estado` ENUM(\'Activo\',\'Inactivo\') DEFAULT \'Activo\' AFTER `id_linea`;', 'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Asegurarse que filas existentes estén en Activo
UPDATE `ruta` SET `estado` = 'Activo' WHERE `estado` IS NULL;
UPDATE `parada` SET `estado` = 'Activo' WHERE `estado` IS NULL;
