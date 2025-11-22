<?php
/**
 * import_sql.php
 * Importa archivos SQL respetando directivas DELIMITER (útil cuando el cliente mysql falla con DELIMITER).
 * Uso:
 *   php import_sql.php path/to/file1.sql [path/to/file2.sql ...]
 * Si no se pasan archivos, por defecto importa los scripts de auditoría del proyecto.
 */

chdir(__DIR__ . '/../'); // situarnos en la raíz del proyecto

require_once __DIR__ . '/../config/config.php';

$files = array_slice($argv, 1);
if (empty($files)) {
    $files = [
        __DIR__ . '/add_audit_triggers.sql',
        __DIR__ . '/sync_trigger_history.sql'
    ];
}

// Conectar usando datos de config
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    fwrite(STDERR, "Error al conectar a la base de datos: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

foreach ($files as $file) {
    if (!file_exists($file)) {
        fwrite(STDOUT, "Archivo no encontrado: $file\n");
        continue;
    }

    fwrite(STDOUT, "Importando: $file\n");

    $lines = file($file);
    $delimiter = ";";
    $buffer = '';

    foreach ($lines as $line) {
        // Saltar comentarios tipo -- o #
        $trim = ltrim($line);
        if (stripos($trim, 'DELIMITER') === 0) {
            // Cambiar delimitador
            $parts = preg_split('/\s+/', $trim);
            $delimiter = isset($parts[1]) ? trim($parts[1]) : ';';
            //flush buffer if any (unlikely)
            $buffer = '';
            continue;
        }

        $buffer .= $line;

        // Comprobar si el buffer termina con el delimitador (ignorando espacios finales)
        $r = rtrim($buffer);
        if ($delimiter === '') {
            continue; // no hay delimitador válido
        }
        $delimLen = strlen($delimiter);
        if ($delimLen > 0 && substr($r, -$delimLen) === $delimiter) {
            $stmt = substr($r, 0, -$delimLen);
            $stmt = trim($stmt);
            if ($stmt !== '') {
                try {
                    $pdo->exec($stmt);
                    fwrite(STDOUT, "OK: Ejecutado statement (longitud " . strlen($stmt) . ")\n");
                } catch (PDOException $e) {
                    fwrite(STDERR, "ERROR al ejecutar statement: " . $e->getMessage() . PHP_EOL);
                    fwrite(STDERR, "Statement:\n" . $stmt . PHP_EOL);
                    // continuar con siguientes statements
                }
            }
            $buffer = '';
        }
    }

    // Ejecutar cualquier restante sin delimitador final
    $left = trim($buffer);
    if ($left !== '') {
        try {
            $pdo->exec($left);
            fwrite(STDOUT, "OK: Ejecutado statement final (longitud " . strlen($left) . ")\n");
        } catch (PDOException $e) {
            fwrite(STDERR, "ERROR al ejecutar statement final: " . $e->getMessage() . PHP_EOL);
            fwrite(STDERR, "Statement:\n" . $left . PHP_EOL);
        }
    }

    fwrite(STDOUT, "Importación completada para: $file\n\n");
}

// Opcional: llamar al procedimiento de sincronización si existe
try {
    $pdo->exec("CALL sync_trigger_history();");
    fwrite(STDOUT, "CALL sync_trigger_history() ejecutado (si existe).\n");
} catch (PDOException $e) {
    // Intentar llamar pasando NULL (algunas versiones requieren el parámetro)
    try {
        $pdo->exec("CALL sync_trigger_history(NULL);");
        fwrite(STDOUT, "CALL sync_trigger_history(NULL) ejecutado (intento alternativo).\n");
    } catch (PDOException $e2) {
        fwrite(STDOUT, "Nota: no se pudo ejecutar CALL sync_trigger_history(): " . $e->getMessage() . PHP_EOL);
    }
}

fwrite(STDOUT, "Importación de scripts finalizada.\n");

?>
