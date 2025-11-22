<?php
/**
 * create_triggers_via_php.php
 * Crea triggers de auditoría directamente desde PHP evitando el uso
 * de PREPARE dentro de procedimientos almacenados (evita error 1295).
 * Uso: php create_triggers_via_php.php
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

$tasks = [
    // tabla => [pk, cols csv]
    'usuario' => ['id_usuario', 'id_usuario,nombre,apellido_paterno,apellido_materno,correo,direccion,telefono,password,rol,estado'],
    'ruta' => ['id_ruta', 'id_ruta,nombre_ruta,hora_inicio,hora_final,estado,id_linea,puntos'],
    'parada' => ['id_parada', 'id_parada,nombre_parada,latitud,longitud,estado'],
    'linea' => ['id_linea', 'id_linea,nombre_linea,color_linea,tramo_largo,tramo_corto'],
    'tarifa' => ['id_tarifa', 'id_tarifa,id_ruta,fecha_inicio,fecha_fin,monto_estudiante,monto_personaMayor,monto_personaRegular']
];

foreach ($tasks as $table => $info) {
    $pk = $info[0];
    $cols = $info[1];
    $colsArr = array_map('trim', explode(',', $cols));

    $triggerNames = [
        $table . '_after_insert',
        $table . '_after_update',
        $table . '_after_delete',
    ];

    // Drop existing triggers
    foreach ($triggerNames as $tname) {
        $dropSql = sprintf('DROP TRIGGER IF EXISTS `%s`', $tname);
        fwrite(STDOUT, "Dropping trigger if exists: $tname\n");
        try {
            $pdo->exec($dropSql);
        } catch (PDOException $e) {
            fwrite(STDERR, "Warning dropping $tname: " . $e->getMessage() . PHP_EOL);
        }
    }

    // Helper: build JSON_OBJECT pairs
    $pairsNew = [];
    $pairsOld = [];
    foreach ($colsArr as $c) {
        $colEsc = str_replace('`', '``', $c);
        $pairsNew[] = "'{$colEsc}', NEW.`{$colEsc}`";
        $pairsOld[] = "'{$colEsc}', OLD.`{$colEsc}`";
    }
    $jsonNew = 'JSON_OBJECT(' . implode(', ', $pairsNew) . ')';
    $jsonOld = 'JSON_OBJECT(' . implode(', ', $pairsOld) . ')';

    // CREATE AFTER INSERT
    $createInsert = "CREATE TRIGGER `{$table}_after_insert` AFTER INSERT ON `{$table}`\nFOR EACH ROW\nBEGIN\n    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, new_values)\n    VALUES (DATABASE(), '{$table}', NEW.`{$pk}`, 'INSERT', NOW(), CURRENT_USER(), {$jsonNew});\nEND";

    // CREATE AFTER UPDATE
    $createUpdate = "CREATE TRIGGER `{$table}_after_update` AFTER UPDATE ON `{$table}`\nFOR EACH ROW\nBEGIN\n    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values, new_values)\n    VALUES (DATABASE(), '{$table}', NEW.`{$pk}`, 'UPDATE', NOW(), CURRENT_USER(), {$jsonOld}, {$jsonNew});\nEND";

    // CREATE AFTER DELETE
    $createDelete = "CREATE TRIGGER `{$table}_after_delete` AFTER DELETE ON `{$table}`\nFOR EACH ROW\nBEGIN\n    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values)\n    VALUES (DATABASE(), '{$table}', OLD.`{$pk}`, 'DELETE', NOW(), CURRENT_USER(), {$jsonOld});\nEND";

    $stmts = [
        ['sql' => $createInsert, 'name' => $table . '_after_insert'],
        ['sql' => $createUpdate, 'name' => $table . '_after_update'],
        ['sql' => $createDelete, 'name' => $table . '_after_delete'],
    ];

    foreach ($stmts as $s) {
        fwrite(STDOUT, "Creating trigger: {$s['name']}\n");
        try {
            $pdo->exec($s['sql']);
            fwrite(STDOUT, "OK: {$s['name']} created\n");
        } catch (PDOException $e) {
            fwrite(STDERR, "ERROR creating {$s['name']}: " . $e->getMessage() . PHP_EOL);
        }
    }

}

// Sync trigger history if possible
try {
    $pdo->exec("CALL sync_trigger_history(NULL)");
    fwrite(STDOUT, "sync_trigger_history(NULL) called successfully.\n");
} catch (PDOException $e) {
    fwrite(STDERR, "Warning: could not call sync_trigger_history(NULL): " . $e->getMessage() . PHP_EOL);
}

fwrite(STDOUT, "All done.\n");

?>
