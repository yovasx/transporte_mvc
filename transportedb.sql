-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-11-2025 a las 03:08:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `transportedb`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generate_audit_triggers` (IN `in_tbl` VARCHAR(64), IN `in_pk` VARCHAR(64), IN `in_cols` TEXT)   BEGIN
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sync_trigger_history` (IN `p_trigger_name` VARCHAR(255))   BEGIN
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_log`
--

CREATE TABLE `audit_log` (
  `id` bigint(20) NOT NULL,
  `db_name` varchar(64) DEFAULT NULL,
  `table_name` varchar(64) NOT NULL,
  `pk_value` varchar(255) DEFAULT NULL,
  `operation` varchar(10) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `changed_by` varchar(255) DEFAULT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `statement` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `audit_log`
--

INSERT INTO `audit_log` (`id`, `db_name`, `table_name`, `pk_value`, `operation`, `changed_at`, `changed_by`, `old_values`, `new_values`, `statement`) VALUES
(1, 'transportedb', 'usuario', '48', 'INSERT', '2025-11-19 12:50:52', 'root@localhost', NULL, '48|PruebaAudit|Perez|Lopez|prueba_audit@example.com|Calle Test 1|5551234|testpass|usuario|Activo', NULL),
(2, 'transportedb', 'usuario', '47', 'UPDATE', '2025-11-19 13:21:23', 'root@localhost', '{\"id_usuario\": 47, \"nombre\": \"carlos\", \"apellido_paterno\": \"aliaga\", \"apellido_materno\": \"vasquez\", \"correo\": \"carlos@gmail.com\", \"direccion\": \"plaza virroel\", \"telefono\": \"788223\", \"password\": \"$2y$10$4f.PAf40yDWbCA0EYmjjMOv1Wiinw9cuWGWEBNLzi89TMfBjgriRO\", \"rol\": \"usuario\", \"estado\": \"Activo\"}', '{\"id_usuario\": 47, \"nombre\": \"carlos_test_update\", \"apellido_paterno\": \"aliaga\", \"apellido_materno\": \"vasquez\", \"correo\": \"carlos@gmail.com\", \"direccion\": \"plaza virroel\", \"telefono\": \"788223\", \"password\": \"$2y$10$4f.PAf40yDWbCA0EYmjjMOv1Wiinw9cuWGWEBNLzi89TMfBjgriRO\", \"rol\": \"usuario\", \"estado\": \"Activo\"}', NULL),
(3, 'transportedb', 'usuario', '46', 'UPDATE', '2025-11-19 13:22:37', 'root@localhost', '{\"id_usuario\": 46, \"nombre\": \"luis\", \"apellido_paterno\": \"pardo\", \"apellido_materno\": \"condori\", \"correo\": \"luis@gmail.com\", \"direccion\": \"buenos aires\", \"telefono\": \"64837647\", \"password\": \"$2y$10$c4Ba1lFarIOVQC0SVs8WNOmYeGjGiZqXQaybgKfDLP1FksKAm8feC\", \"rol\": \"usuario\", \"estado\": \"Activo\"}', '{\"id_usuario\": 46, \"nombre\": \"lui\", \"apellido_paterno\": \"pardo\", \"apellido_materno\": \"condori\", \"correo\": \"luis@gmail.com\", \"direccion\": \"buenos aires\", \"telefono\": \"64837647\", \"password\": \"$2y$10$c4Ba1lFarIOVQC0SVs8WNOmYeGjGiZqXQaybgKfDLP1FksKAm8feC\", \"rol\": \"usuario\", \"estado\": \"Activo\"}', NULL),
(4, 'transportedb', 'usuario', '47', 'UPDATE', '2025-11-19 13:22:47', 'root@localhost', '{\"id_usuario\": 47, \"nombre\": \"carlos_test_update\", \"apellido_paterno\": \"aliaga\", \"apellido_materno\": \"vasquez\", \"correo\": \"carlos@gmail.com\", \"direccion\": \"plaza virroel\", \"telefono\": \"788223\", \"password\": \"$2y$10$4f.PAf40yDWbCA0EYmjjMOv1Wiinw9cuWGWEBNLzi89TMfBjgriRO\", \"rol\": \"usuario\", \"estado\": \"Activo\"}', '{\"id_usuario\": 47, \"nombre\": \"carlos_test_update\", \"apellido_paterno\": \"aliaga\", \"apellido_materno\": \"vasquez\", \"correo\": \"carlos@gmail.com\", \"direccion\": \"plaza virroel\", \"telefono\": \"788223\", \"password\": \"$2y$10$4f.PAf40yDWbCA0EYmjjMOv1Wiinw9cuWGWEBNLzi89TMfBjgriRO\", \"rol\": \"usuario\", \"estado\": \"Inactivo\"}', NULL),
(5, 'transportedb', 'usuario', '46', 'UPDATE', '2025-11-19 13:25:21', 'root@localhost', '{\"id_usuario\": 46, \"nombre\": \"lui\", \"apellido_paterno\": \"pardo\", \"apellido_materno\": \"condori\", \"correo\": \"luis@gmail.com\", \"direccion\": \"buenos aires\", \"telefono\": \"64837647\", \"password\": \"$2y$10$c4Ba1lFarIOVQC0SVs8WNOmYeGjGiZqXQaybgKfDLP1FksKAm8feC\", \"rol\": \"usuario\", \"estado\": \"Activo\"}', '{\"id_usuario\": 46, \"nombre\": \"luis\", \"apellido_paterno\": \"pardo\", \"apellido_materno\": \"condori\", \"correo\": \"luis@gmail.com\", \"direccion\": \"buenos aires\", \"telefono\": \"64837647\", \"password\": \"$2y$10$c4Ba1lFarIOVQC0SVs8WNOmYeGjGiZqXQaybgKfDLP1FksKAm8feC\", \"rol\": \"usuario\", \"estado\": \"Activo\"}', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea`
--

CREATE TABLE `linea` (
  `id_linea` int(11) NOT NULL,
  `nombre_linea` varchar(100) NOT NULL,
  `color_linea` varchar(50) DEFAULT NULL,
  `tramo_largo` varchar(200) DEFAULT NULL,
  `tramo_corto` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `linea`
--
DELIMITER $$
CREATE TRIGGER `linea_after_delete` AFTER DELETE ON `linea` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values)
    VALUES (DATABASE(), 'linea', OLD.`id_linea`, 'DELETE', NOW(), CURRENT_USER(), JSON_OBJECT('id_linea', OLD.`id_linea`, 'nombre_linea', OLD.`nombre_linea`, 'color_linea', OLD.`color_linea`, 'tramo_largo', OLD.`tramo_largo`, 'tramo_corto', OLD.`tramo_corto`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `linea_after_insert` AFTER INSERT ON `linea` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, new_values)
    VALUES (DATABASE(), 'linea', NEW.`id_linea`, 'INSERT', NOW(), CURRENT_USER(), JSON_OBJECT('id_linea', NEW.`id_linea`, 'nombre_linea', NEW.`nombre_linea`, 'color_linea', NEW.`color_linea`, 'tramo_largo', NEW.`tramo_largo`, 'tramo_corto', NEW.`tramo_corto`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `linea_after_update` AFTER UPDATE ON `linea` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values, new_values)
    VALUES (DATABASE(), 'linea', NEW.`id_linea`, 'UPDATE', NOW(), CURRENT_USER(), JSON_OBJECT('id_linea', OLD.`id_linea`, 'nombre_linea', OLD.`nombre_linea`, 'color_linea', OLD.`color_linea`, 'tramo_largo', OLD.`tramo_largo`, 'tramo_corto', OLD.`tramo_corto`), JSON_OBJECT('id_linea', NEW.`id_linea`, 'nombre_linea', NEW.`nombre_linea`, 'color_linea', NEW.`color_linea`, 'tramo_largo', NEW.`tramo_largo`, 'tramo_corto', NEW.`tramo_corto`));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parada`
--

CREATE TABLE `parada` (
  `id_parada` int(11) NOT NULL,
  `nombre_parada` varchar(100) NOT NULL,
  `latitud` float DEFAULT NULL,
  `longitud` float DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parada`
--

INSERT INTO `parada` (`id_parada`, `nombre_parada`, `latitud`, `longitud`, `estado`) VALUES
(1, 'plaza del estudiante', -16.5, -68.15, 'Activo'),
(4, 'stadium', -16.52, -68.13, 'Inactivo'),
(5, 'plaza del estudiante', 19.4326, -99.1332, 'Inactivo'),
(6, 'primera parada', -16.505, -68.1315, 'Activo'),
(7, 'centro random', -16.4983, -68.1353, 'Inactivo'),
(8, 'Prueba', -16.4913, -68.1395, 'Activo');

--
-- Disparadores `parada`
--
DELIMITER $$
CREATE TRIGGER `parada_after_delete` AFTER DELETE ON `parada` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values)
    VALUES (DATABASE(), 'parada', OLD.`id_parada`, 'DELETE', NOW(), CURRENT_USER(), JSON_OBJECT('id_parada', OLD.`id_parada`, 'nombre_parada', OLD.`nombre_parada`, 'latitud', OLD.`latitud`, 'longitud', OLD.`longitud`, 'estado', OLD.`estado`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `parada_after_insert` AFTER INSERT ON `parada` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, new_values)
    VALUES (DATABASE(), 'parada', NEW.`id_parada`, 'INSERT', NOW(), CURRENT_USER(), JSON_OBJECT('id_parada', NEW.`id_parada`, 'nombre_parada', NEW.`nombre_parada`, 'latitud', NEW.`latitud`, 'longitud', NEW.`longitud`, 'estado', NEW.`estado`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `parada_after_update` AFTER UPDATE ON `parada` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values, new_values)
    VALUES (DATABASE(), 'parada', NEW.`id_parada`, 'UPDATE', NOW(), CURRENT_USER(), JSON_OBJECT('id_parada', OLD.`id_parada`, 'nombre_parada', OLD.`nombre_parada`, 'latitud', OLD.`latitud`, 'longitud', OLD.`longitud`, 'estado', OLD.`estado`), JSON_OBJECT('id_parada', NEW.`id_parada`, 'nombre_parada', NEW.`nombre_parada`, 'latitud', NEW.`latitud`, 'longitud', NEW.`longitud`, 'estado', NEW.`estado`));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ruta`
--

CREATE TABLE `ruta` (
  `id_ruta` int(11) NOT NULL,
  `nombre_ruta` varchar(100) NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_final` time DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `id_linea` int(11) DEFAULT NULL,
  `puntos` longtext DEFAULT NULL COMMENT 'Puntos de la ruta en formato JSON'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ruta`
--

INSERT INTO `ruta` (`id_ruta`, `nombre_ruta`, `hora_inicio`, `hora_final`, `estado`, `id_linea`, `puntos`) VALUES
(1, '143', '01:00:00', '02:00:00', 'Activo', NULL, NULL),
(2, '234', '01:00:00', '02:00:00', 'Activo', NULL, NULL),
(3, 'ruta3', '01:00:00', '02:00:00', 'Inactivo', NULL, NULL),
(7, '201', '06:00:00', '23:00:00', 'Activo', NULL, NULL),
(8, '14 de septiembre', '06:00:00', '23:00:00', 'Inactivo', NULL, NULL),
(9, 'f', '06:00:00', '23:00:00', 'Activo', NULL, '[[-16.490343048203215,-68.1376361846924],[-16.490343048203215,-68.1376361846924]]');

--
-- Disparadores `ruta`
--
DELIMITER $$
CREATE TRIGGER `ruta_after_delete` AFTER DELETE ON `ruta` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values)
    VALUES (DATABASE(), 'ruta', OLD.`id_ruta`, 'DELETE', NOW(), CURRENT_USER(), JSON_OBJECT('id_ruta', OLD.`id_ruta`, 'nombre_ruta', OLD.`nombre_ruta`, 'hora_inicio', OLD.`hora_inicio`, 'hora_final', OLD.`hora_final`, 'estado', OLD.`estado`, 'id_linea', OLD.`id_linea`, 'puntos', OLD.`puntos`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `ruta_after_insert` AFTER INSERT ON `ruta` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, new_values)
    VALUES (DATABASE(), 'ruta', NEW.`id_ruta`, 'INSERT', NOW(), CURRENT_USER(), JSON_OBJECT('id_ruta', NEW.`id_ruta`, 'nombre_ruta', NEW.`nombre_ruta`, 'hora_inicio', NEW.`hora_inicio`, 'hora_final', NEW.`hora_final`, 'estado', NEW.`estado`, 'id_linea', NEW.`id_linea`, 'puntos', NEW.`puntos`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `ruta_after_update` AFTER UPDATE ON `ruta` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values, new_values)
    VALUES (DATABASE(), 'ruta', NEW.`id_ruta`, 'UPDATE', NOW(), CURRENT_USER(), JSON_OBJECT('id_ruta', OLD.`id_ruta`, 'nombre_ruta', OLD.`nombre_ruta`, 'hora_inicio', OLD.`hora_inicio`, 'hora_final', OLD.`hora_final`, 'estado', OLD.`estado`, 'id_linea', OLD.`id_linea`, 'puntos', OLD.`puntos`), JSON_OBJECT('id_ruta', NEW.`id_ruta`, 'nombre_ruta', NEW.`nombre_ruta`, 'hora_inicio', NEW.`hora_inicio`, 'hora_final', NEW.`hora_final`, 'estado', NEW.`estado`, 'id_linea', NEW.`id_linea`, 'puntos', NEW.`puntos`));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sindicato`
--

CREATE TABLE `sindicato` (
  `id_sindicato` int(11) NOT NULL,
  `nombre_sindicato` varchar(100) NOT NULL,
  `idioma` enum('aymara','espa?ol','ingles') DEFAULT 'espa?ol'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifa`
--

CREATE TABLE `tarifa` (
  `id_tarifa` int(11) NOT NULL,
  `id_ruta` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `monto_estudiante` decimal(10,2) DEFAULT NULL,
  `monto_personaMayor` decimal(10,2) DEFAULT NULL,
  `monto_personaRegular` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `tarifa`
--
DELIMITER $$
CREATE TRIGGER `tarifa_after_delete` AFTER DELETE ON `tarifa` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values)
    VALUES (DATABASE(), 'tarifa', OLD.`id_tarifa`, 'DELETE', NOW(), CURRENT_USER(), JSON_OBJECT('id_tarifa', OLD.`id_tarifa`, 'id_ruta', OLD.`id_ruta`, 'fecha_inicio', OLD.`fecha_inicio`, 'fecha_fin', OLD.`fecha_fin`, 'monto_estudiante', OLD.`monto_estudiante`, 'monto_personaMayor', OLD.`monto_personaMayor`, 'monto_personaRegular', OLD.`monto_personaRegular`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tarifa_after_insert` AFTER INSERT ON `tarifa` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, new_values)
    VALUES (DATABASE(), 'tarifa', NEW.`id_tarifa`, 'INSERT', NOW(), CURRENT_USER(), JSON_OBJECT('id_tarifa', NEW.`id_tarifa`, 'id_ruta', NEW.`id_ruta`, 'fecha_inicio', NEW.`fecha_inicio`, 'fecha_fin', NEW.`fecha_fin`, 'monto_estudiante', NEW.`monto_estudiante`, 'monto_personaMayor', NEW.`monto_personaMayor`, 'monto_personaRegular', NEW.`monto_personaRegular`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tarifa_after_update` AFTER UPDATE ON `tarifa` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values, new_values)
    VALUES (DATABASE(), 'tarifa', NEW.`id_tarifa`, 'UPDATE', NOW(), CURRENT_USER(), JSON_OBJECT('id_tarifa', OLD.`id_tarifa`, 'id_ruta', OLD.`id_ruta`, 'fecha_inicio', OLD.`fecha_inicio`, 'fecha_fin', OLD.`fecha_fin`, 'monto_estudiante', OLD.`monto_estudiante`, 'monto_personaMayor', OLD.`monto_personaMayor`, 'monto_personaRegular', OLD.`monto_personaRegular`), JSON_OBJECT('id_tarifa', NEW.`id_tarifa`, 'id_ruta', NEW.`id_ruta`, 'fecha_inicio', NEW.`fecha_inicio`, 'fecha_fin', NEW.`fecha_fin`, 'monto_estudiante', NEW.`monto_estudiante`, 'monto_personaMayor', NEW.`monto_personaMayor`, 'monto_personaRegular', NEW.`monto_personaRegular`));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trigger_history`
--

CREATE TABLE `trigger_history` (
  `id` bigint(20) NOT NULL,
  `trigger_name` varchar(255) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `ddl` longtext NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `applied_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trigger_history`
--

INSERT INTO `trigger_history` (`id`, `trigger_name`, `table_name`, `ddl`, `applied_at`, `applied_by`) VALUES
(1, 'usuario_after_insert', 'usuario', 'INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, new_values) VALUES (DATABASE(), \'usuario\', NEW.id_usuario, \'INSERT\', NOW(), CURRENT_USER(), CONCAT(NEW.id_usuario, \'|\', NEW.nombre, \'|\', NEW.apellido_paterno, \'|\', NEW.apellido_materno, \'|\', NEW.correo, \'|\', NEW.direccion, \'|\', NEW.telefono, \'|\', NEW.password, \'|\', NEW.rol, \'|\', NEW.estado))', '2025-11-19 12:44:27', 'root@localhost'),
(2, 'linea_after_insert', 'linea', 'BEGIN\n    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, new_values)\n    VALUES (DATABASE(), \'linea\', NEW.`id_linea`, \'INSERT\', NOW(), CURRENT_USER(), JSON_OBJECT(\'id_linea\', NEW.`id_linea`, \'nombre_linea\', NEW.`nombre_linea`, \'color_linea\', NEW.`color_linea`, \'tramo_largo\', NEW.`tramo_largo`, \'tramo_corto\', NEW.`tramo_corto`));\nEND', '2025-11-19 13:20:28', 'root@localhost'),
(3, 'linea_after_update', 'linea', 'BEGIN\n    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values, new_values)\n    VALUES (DATABASE(), \'linea\', NEW.`id_linea`, \'UPDATE\', NOW(), CURRENT_USER(), JSON_OBJECT(\'id_linea\', OLD.`id_linea`, \'nombre_linea\', OLD.`nombre_linea`, \'color_linea\', OLD.`color_linea`, \'tramo_largo\', OLD.`tramo_largo`, \'tramo_corto\', OLD.`tramo_corto`), JSON_OBJECT(\'id_linea\', NEW.`id_linea`, \'nombre_linea\', NEW.`nombre_linea`, \'color_linea\', NEW.`color_linea`, \'tramo_largo\', NEW.`tramo_largo`, \'tramo_corto\', NEW.`tramo_corto`));\nEND', '2025-11-19 13:21:01', 'root@localhost');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) DEFAULT NULL,
  `correo` varchar(150) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` enum('admin','usuario','operador') DEFAULT 'usuario',
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `apellido_materno` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido_paterno`, `correo`, `direccion`, `telefono`, `password`, `rol`, `estado`, `apellido_materno`) VALUES
(1, 'victor', 'gutierrez', 'victor@gmail.com', 'av. 6 de marzo', '68544345', NULL, 'usuario', 'Activo', 'colque'),
(10, 'juan', 'perez', 'juanito@gmail.com', '6 de agosto', '742345344', NULL, 'usuario', 'Activo', 'guzman'),
(13, 'pedro', 'callizaya', 'pedro123@gmail.com', 'av. buch', '734243', NULL, 'usuario', 'Activo', 'perez'),
(42, 'ijbiñi', 'tik', 'ijbinipro@gmail.com', 'calle_de_la_seriedad', '666', NULL, 'usuario', 'Inactivo', 'tok'),
(43, 'pedrox', 'vargas', 'pedrito@gmail.com', 'av. arce', '68544345', '$2y$10$FG03r.JQ4bZfpLRPEbOEY.6pdECtUV0GZeiE3k5L8Dy7jHR63mWIi', 'usuario', 'Activo', 'quisbert'),
(44, 'yovani', 'Andia', 'yovas@gmail.com', 'Santiago II', '69961678', '$2y$10$SKwZ0r.wfz4QX8ffzzZrGOmPwnXCw1Bu8u.u3.DKMs/RJNRAOblDW', 'admin', 'Activo', 'Quispe'),
(45, 'valeria', 'mendez', 'vale@gmail.com', 'av. buch', '75644765', '$2y$10$Iq5cTrk9d5eDhEggNbow4.wY4ynh6Gl8DPexkkRmgfk7Cu9xmnZJK', 'usuario', 'Activo', 'gutierrez'),
(46, 'luis', 'pardo', 'luis@gmail.com', 'buenos aires', '64837647', '$2y$10$c4Ba1lFarIOVQC0SVs8WNOmYeGjGiZqXQaybgKfDLP1FksKAm8feC', 'usuario', 'Activo', 'condori'),
(47, 'carlos_test_update', 'aliaga', 'carlos@gmail.com', 'plaza virroel', '788223', '$2y$10$4f.PAf40yDWbCA0EYmjjMOv1Wiinw9cuWGWEBNLzi89TMfBjgriRO', 'usuario', 'Inactivo', 'vasquez'),
(48, 'PruebaAudit_upd', 'Perez', 'prueba_audit@example.com', 'Calle Test 1', '5551234', 'testpass', 'usuario', 'Activo', 'Lopez');

--
-- Disparadores `usuario`
--
DELIMITER $$
CREATE TRIGGER `usuario_after_delete` AFTER DELETE ON `usuario` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values)
    VALUES (DATABASE(), 'usuario', OLD.`id_usuario`, 'DELETE', NOW(), CURRENT_USER(), JSON_OBJECT('id_usuario', OLD.`id_usuario`, 'nombre', OLD.`nombre`, 'apellido_paterno', OLD.`apellido_paterno`, 'apellido_materno', OLD.`apellido_materno`, 'correo', OLD.`correo`, 'direccion', OLD.`direccion`, 'telefono', OLD.`telefono`, 'password', OLD.`password`, 'rol', OLD.`rol`, 'estado', OLD.`estado`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `usuario_after_insert` AFTER INSERT ON `usuario` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, new_values)
    VALUES (DATABASE(), 'usuario', NEW.`id_usuario`, 'INSERT', NOW(), CURRENT_USER(), JSON_OBJECT('id_usuario', NEW.`id_usuario`, 'nombre', NEW.`nombre`, 'apellido_paterno', NEW.`apellido_paterno`, 'apellido_materno', NEW.`apellido_materno`, 'correo', NEW.`correo`, 'direccion', NEW.`direccion`, 'telefono', NEW.`telefono`, 'password', NEW.`password`, 'rol', NEW.`rol`, 'estado', NEW.`estado`));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `usuario_after_update` AFTER UPDATE ON `usuario` FOR EACH ROW BEGIN
    INSERT INTO audit_log (db_name, table_name, pk_value, operation, changed_at, changed_by, old_values, new_values)
    VALUES (DATABASE(), 'usuario', NEW.`id_usuario`, 'UPDATE', NOW(), CURRENT_USER(), JSON_OBJECT('id_usuario', OLD.`id_usuario`, 'nombre', OLD.`nombre`, 'apellido_paterno', OLD.`apellido_paterno`, 'apellido_materno', OLD.`apellido_materno`, 'correo', OLD.`correo`, 'direccion', OLD.`direccion`, 'telefono', OLD.`telefono`, 'password', OLD.`password`, 'rol', OLD.`rol`, 'estado', OLD.`estado`), JSON_OBJECT('id_usuario', NEW.`id_usuario`, 'nombre', NEW.`nombre`, 'apellido_paterno', NEW.`apellido_paterno`, 'apellido_materno', NEW.`apellido_materno`, 'correo', NEW.`correo`, 'direccion', NEW.`direccion`, 'telefono', NEW.`telefono`, 'password', NEW.`password`, 'rol', NEW.`rol`, 'estado', NEW.`estado`));
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `linea`
--
ALTER TABLE `linea`
  ADD PRIMARY KEY (`id_linea`);

--
-- Indices de la tabla `parada`
--
ALTER TABLE `parada`
  ADD PRIMARY KEY (`id_parada`);

--
-- Indices de la tabla `ruta`
--
ALTER TABLE `ruta`
  ADD PRIMARY KEY (`id_ruta`),
  ADD KEY `id_linea` (`id_linea`);

--
-- Indices de la tabla `sindicato`
--
ALTER TABLE `sindicato`
  ADD PRIMARY KEY (`id_sindicato`);

--
-- Indices de la tabla `tarifa`
--
ALTER TABLE `tarifa`
  ADD PRIMARY KEY (`id_tarifa`),
  ADD KEY `id_ruta` (`id_ruta`);

--
-- Indices de la tabla `trigger_history`
--
ALTER TABLE `trigger_history`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `linea`
--
ALTER TABLE `linea`
  MODIFY `id_linea` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `parada`
--
ALTER TABLE `parada`
  MODIFY `id_parada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `ruta`
--
ALTER TABLE `ruta`
  MODIFY `id_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `sindicato`
--
ALTER TABLE `sindicato`
  MODIFY `id_sindicato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tarifa`
--
ALTER TABLE `tarifa`
  MODIFY `id_tarifa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trigger_history`
--
ALTER TABLE `trigger_history`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ruta`
--
ALTER TABLE `ruta`
  ADD CONSTRAINT `ruta_ibfk_1` FOREIGN KEY (`id_linea`) REFERENCES `linea` (`id_linea`);

--
-- Filtros para la tabla `tarifa`
--
ALTER TABLE `tarifa`
  ADD CONSTRAINT `tarifa_ibfk_1` FOREIGN KEY (`id_ruta`) REFERENCES `ruta` (`id_ruta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
