-- Script de auditoría y plantillas de triggers
-- Uso: revisar y adaptar las llamadas a `generate_audit_triggers` para cada tabla
-- Requiere: MySQL compatible (XAMPP). Este script crea tablas de log y un procedimiento
-- que genera triggers dinámicamente (inserta snapshots como texto).

DELIMITER $$

-- Tabla para registrar cambios en datos
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `db_name` VARCHAR(64) NULL,
  `table_name` VARCHAR(64) NOT NULL,
  `pk_value` VARCHAR(255) NULL,
  `operation` VARCHAR(10) NOT NULL,
  `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `changed_by` VARCHAR(255) NULL,
  `old_values` TEXT NULL,
  `new_values` TEXT NULL,
  `statement` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla para guardar historial de definiciones de triggers (DDL que se creó/aplicó)
CREATE TABLE IF NOT EXISTS `trigger_history` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `trigger_name` VARCHAR(255) NOT NULL,
  `table_name` VARCHAR(64) NOT NULL,
  `ddl` LONGTEXT NOT NULL,
  `applied_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `applied_by` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Procedimiento que genera triggers de auditoría (INSERT/UPDATE/DELETE) para una tabla
-- Parámetros:
--  in_tbl: nombre de tabla
--  in_pk: nombre de columna PK (usada para pk_value)
--  in_cols: lista de columnas separadas por coma que se incluirán en snapshot (ej: 'id,nombre,precio')
CREATE PROCEDURE `generate_audit_triggers`(
  IN in_tbl VARCHAR(64),
  IN in_pk VARCHAR(64),
  IN in_cols TEXT
)
BEGIN
  DECLARE cols TEXT;
  DECLARE cols_new TEXT;
  DECLARE cols_old TEXT;
  DECLARE cols_keys TEXT;
  DECLARE json_new_expr TEXT;
  DECLARE json_old_expr TEXT;
  DECLARE sql_stmt TEXT;

  SET cols = REPLACE(in_cols, ' ', '');
  -- Crear expresiones NEW.col, OLD.col
  SET cols_new = REPLACE(cols, ',', ', NEW.');
  SET cols_new = CONCAT('NEW.', cols_new);
  SET cols_old = REPLACE(cols, ',', ', OLD.');
  SET cols_old = CONCAT('OLD.', cols_old);

  -- Crear lista de claves para JSON_OBJECT: 'col1', 'col2', ...
  SET cols_keys = REPLACE(cols, ',', ''', ''');

  SET json_new_expr = CONCAT('JSON_OBJECT(''', cols_keys, ''', ', cols_new, ')');
  SET json_old_expr = CONCAT('JSON_OBJECT(''', cols_keys, ''', ', cols_old, ')');

  -- DROP y CREATE trigger AFTER INSERT (genera JSON en new_values)
  SET @drop_insert = CONCAT('DROP TRIGGER IF EXISTS `', in_tbl, '_after_insert`;');
  PREPARE stmt_drop_insert FROM @drop_insert; EXECUTE stmt_drop_insert; DEALLOCATE PREPARE stmt_drop_insert;

  SET sql_stmt = CONCAT(
    'CREATE TRIGGER `', in_tbl, '_after_insert` AFTER INSERT ON `', in_tbl, '` FOR EACH ROW BEGIN ',
      'INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, new_values) VALUES (DATABASE(), ''', in_tbl, ''', NEW.', in_pk, ', ''INSERT'', NOW(), CURRENT_USER(), ', json_new_expr, ');',
    ' END;'
  );
  PREPARE stmt_insert FROM sql_stmt; EXECUTE stmt_insert; DEALLOCATE PREPARE stmt_insert;

  -- DROP y CREATE trigger AFTER UPDATE (guarda JSON en old_values y new_values)
  SET @drop_update = CONCAT('DROP TRIGGER IF EXISTS `', in_tbl, '_after_update`;');
  PREPARE stmt_drop_update FROM @drop_update; EXECUTE stmt_drop_update; DEALLOCATE PREPARE stmt_drop_update;

  SET sql_stmt = CONCAT(
    'CREATE TRIGGER `', in_tbl, '_after_update` AFTER UPDATE ON `', in_tbl, '` FOR EACH ROW BEGIN ',
      'INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values, new_values) VALUES (DATABASE(), ''', in_tbl, ''', NEW.', in_pk, ', ''UPDATE'', NOW(), CURRENT_USER(), ', json_old_expr, ', ', json_new_expr, ');',
    ' END;'
  );
  PREPARE stmt_update FROM sql_stmt; EXECUTE stmt_update; DEALLOCATE PREPARE stmt_update;

  -- DROP y CREATE trigger AFTER DELETE (guarda JSON en old_values)
  SET @drop_delete = CONCAT('DROP TRIGGER IF EXISTS `', in_tbl, '_after_delete`;');
  PREPARE stmt_drop_delete FROM @drop_delete; EXECUTE stmt_drop_delete; DEALLOCATE PREPARE stmt_drop_delete;

  SET sql_stmt = CONCAT(
    'CREATE TRIGGER `', in_tbl, '_after_delete` AFTER DELETE ON `', in_tbl, '` FOR EACH ROW BEGIN ',
      'INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values) VALUES (DATABASE(), ''', in_tbl, ''', OLD.', in_pk, ', ''DELETE'', NOW(), CURRENT_USER(), ', json_old_expr, ');',
    ' END;'
  );
  PREPARE stmt_delete FROM sql_stmt; EXECUTE stmt_delete; DEALLOCATE PREPARE stmt_delete;

END

DELIMITER ;

-- Ejemplo de uso (descomentar y adaptar):
-- CALL generate_audit_triggers('rutas', 'id', 'id,nombre,descripcion');
-- CALL generate_audit_triggers('paradas', 'id', 'id,latitud,longitud,nombre');

-- NOTAS IMPORTANTES:
-- - Ajusta `in_cols` a las columnas que quieras incluir en el snapshot; el procedimiento concatena los valores en un string con separador '|'.
-- - `audit_log.new_values` y `old_values` son TEXT con valores concatenados. Si tu versión de MySQL soporta JSON y prefieres guardar JSON, el procedimiento puede adaptarse.
-- - Para llevar un historial de cambios en las definiciones de triggers (DDL) puedes:
--    1) Mantener las definiciones de triggers en archivos SQL versionados (recomendado: git).
--    2) Ejecutar manualmente INSERT en `trigger_history` al modificar/crear triggers.
--    3) Implementar un procedimiento que copie la definición desde `INFORMATION_SCHEMA.TRIGGERS` y compare con la última versión.

-- Instrucciones rápidas para aplicar el script en XAMPP:
-- 1) Abrir `phpMyAdmin` o usar `mysql` en terminal.
-- 2) Ejecutar el contenido de este archivo (o importarlo) en la base de datos objetivo.
-- 3) Llamar a `CALL generate_audit_triggers('mi_tabla','id','id,col1,col2');` para cada tabla que quieras auditar.
