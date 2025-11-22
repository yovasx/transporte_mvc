<?php
/**
 * create_sync_proc.php
 * Crea o reemplaza el procedimiento almacenado `sync_trigger_history` usando PDO,
 * evitando problemas con directivas DELIMITER en el cliente.
 * Uso: php create_sync_proc.php
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
    $pdo->exec("DROP PROCEDURE IF EXISTS sync_trigger_history");
    fwrite(STDOUT, "Dropped existing procedure (if any).\n");
} catch (PDOException $e) {
    fwrite(STDOUT, "Warning: could not drop procedure: " . $e->getMessage() . PHP_EOL);
}

$sql = <<<SQL
CREATE PROCEDURE sync_trigger_history(IN p_trigger_name VARCHAR(255))
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE t_name VARCHAR(255);
  DECLARE t_table VARCHAR(64);
  DECLARE t_ddl LONGTEXT;
  DECLARE lastddl LONGTEXT;

  DECLARE cur CURSOR FOR
    SELECT TRIGGER_NAME, EVENT_OBJECT_TABLE, ACTION_STATEMENT
    FROM INFORMATION_SCHEMA.TRIGGERS
    WHERE TRIGGER_SCHEMA = DATABASE()
      AND (p_trigger_name IS NULL OR TRIGGER_NAME = p_trigger_name);

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO t_name, t_table, t_ddl;
    IF done THEN
      LEAVE read_loop;
    END IF;

    SELECT ddl INTO lastddl FROM trigger_history WHERE trigger_name = t_name ORDER BY applied_at DESC LIMIT 1;

    IF lastddl IS NULL OR lastddl <> t_ddl THEN
      INSERT INTO trigger_history (trigger_name, table_name, ddl, applied_by)
      VALUES (t_name, t_table, t_ddl, CURRENT_USER());
    END IF;

  END LOOP;

  CLOSE cur;
END
SQL;

try {
    $pdo->exec($sql);
    fwrite(STDOUT, "Procedure sync_trigger_history created successfully.\n");
} catch (PDOException $e) {
    fwrite(STDERR, "ERROR creating procedure: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

// Optionally call it now
try {
    $pdo->exec("CALL sync_trigger_history(NULL)");
    fwrite(STDOUT, "Called sync_trigger_history(NULL) successfully.\n");
} catch (PDOException $e) {
    fwrite(STDOUT, "Note: could not call sync_trigger_history immediately: " . $e->getMessage() . PHP_EOL);
}

fwrite(STDOUT, "Done.\n");

?>
