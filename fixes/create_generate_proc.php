<?php
/**
 * create_generate_proc.php
 * Crea o reemplaza el procedimiento almacenado `generate_audit_triggers` usando PDO,
 * evitando problemas con directivas DELIMITER en el cliente.
 * Uso: php create_generate_proc.php
 */

chdir(__DIR__ . '/../');
require_once __DIR__ . '/../config/config.php';

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    fwrite(STDERR, "Error al conectar a la base de datos: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

// Drop if exists
try {
    $pdo->exec("DROP PROCEDURE IF EXISTS generate_audit_triggers");
    fwrite(STDOUT, "Dropped existing procedure generate_audit_triggers (if any).\n");
} catch (PDOException $e) {
    fwrite(STDOUT, "Warning: could not drop procedure: " . $e->getMessage() . PHP_EOL);
}

$sql = <<<'SQL'
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
SQL;

try {
    $pdo->exec($sql);
    fwrite(STDOUT, "Procedure generate_audit_triggers created successfully.\n");
} catch (PDOException $e) {
    fwrite(STDERR, "ERROR creating procedure generate_audit_triggers: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, "Done.\n");

?>