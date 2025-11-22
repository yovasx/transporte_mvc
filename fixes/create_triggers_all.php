<?php
/**
 * create_triggers_all.php
 * Crea los triggers de auditoría (usando el procedimiento generate_audit_triggers)
 * para las tablas principales del proyecto y sincroniza el historial de triggers.
 * Uso: php create_triggers_all.php
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
    'tarifa' => ['id_tarifa', 'id_tarifa,id_ruta,fecha_inicio,fecha_fin,monto_estudiante,monto_personalMayor,monto_personaRegular']
];

foreach ($tasks as $table => $info) {
    $pk = $info[0];
    $cols = $info[1];
    $sql = sprintf("CALL generate_audit_triggers('%s','%s','%s')", $table, $pk, $cols);
    fwrite(STDOUT, "Executing: $sql\n");
    try {
        $pdo->exec($sql);
        fwrite(STDOUT, "OK: Triggers created for table $table\n");
    } catch (PDOException $e) {
        fwrite(STDERR, "ERROR creating triggers for $table: " . $e->getMessage() . PHP_EOL);
    }
}

// Sync trigger history
try {
    $pdo->exec("CALL sync_trigger_history(NULL)");
    fwrite(STDOUT, "sync_trigger_history(NULL) called successfully.\n");
} catch (PDOException $e) {
    fwrite(STDERR, "Warning: could not call sync_trigger_history(NULL): " . $e->getMessage() . PHP_EOL);
}

fwrite(STDOUT, "All done.\n");

?>
