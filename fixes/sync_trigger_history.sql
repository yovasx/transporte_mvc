-- Procedimiento para sincronizar/guardar DDL de triggers
-- Recorre los triggers del schema actual y guarda la definición
-- en `trigger_history` si difiere de la última versión guardada.
-- Uso: CALL sync_trigger_history();  -- sincroniza todos
--       CALL sync_trigger_history('mi_trigger_name'); -- sincroniza uno

DELIMITER $$

CREATE PROCEDURE `sync_trigger_history`(IN p_trigger_name VARCHAR(255))
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

    -- Obtener la última DDL guardada para este trigger
    SELECT ddl INTO lastddl FROM trigger_history WHERE trigger_name = t_name ORDER BY applied_at DESC LIMIT 1;

    -- Si no existe o cambió, insertar nuevo registro
    IF lastddl IS NULL OR lastddl <> t_ddl THEN
      INSERT INTO trigger_history (trigger_name, table_name, ddl, applied_by)
      VALUES (t_name, t_table, t_ddl, CURRENT_USER());
    END IF;

  END LOOP;

  CLOSE cur;
END$$

DELIMITER ;

-- Ejemplo de uso:
-- CALL sync_trigger_history();
-- CALL sync_trigger_history('rutas_after_insert');
