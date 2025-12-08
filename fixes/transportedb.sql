-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-10-2024 a las 22:24:24
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
CREATE DATABASE IF NOT EXISTS `transportedb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `transportedb`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(255) NOT NULL,
  `tabla_afectada` varchar(255) NOT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `datos_anteriores` text DEFAULT NULL,
  `datos_nuevos` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `audit_log`
--

INSERT INTO `audit_log` (`id`, `usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `datos_anteriores`, `datos_nuevos`, `fecha`) VALUES
(1, 1, 'INSERT', 'usuario', 1, NULL, '{\"id\":1,\"nombre\":\"Admin\",\"email\":\"admin@transporte.com\",\"password\":\"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi\",\"rol\":\"admin\",\"estado\":\"activo\",\"fecha_creacion\":\"2024-10-29 22:24:24\"}', '2024-10-29 22:24:24'),
(2, 1, 'INSERT', 'usuario', 2, NULL, '{\"id\":2,\"nombre\":\"Usuario\",\"email\":\"usuario@transporte.com\",\"password\":\"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi\",\"rol\":\"usuario\",\"estado\":\"activo\",\"fecha_creacion\":\"2024-10-29 22:24:24\"}', '2024-10-29 22:24:24'),
(3, 1, 'INSERT', 'linea', 1, NULL, '{\"id\":1,\"nombre\":\"Línea 1\",\"descripcion\":\"Línea principal\",\"estado\":\"activo\",\"fecha_creacion\":\"2024-10-29 22:24:24\"}', '2024-10-29 22:24:24'),
(4, 1, 'INSERT', 'parada', 1, NULL, '{\"id\":1,\"nombre\":\"Parada Central\",\"direccion\":\"Centro de la ciudad\",\"latitud\":-12.0464,\"longitud\":-77.0428,\"estado\":\"activo\",\"fecha_creacion\":\"2024-10-29 22:24:24\"}', '2024-10-29 22:24:24'),
(5, 1, 'INSERT', 'parada', 2, NULL, '{\"id\":2,\"nombre\":\"Parada Norte\",\"direccion\":\"Norte de la ciudad\",\"latitud\":-12.0264,\"longitud\":-77.0328,\"estado\":\"activo\",\"fecha_creacion\":\"2024-10-29 22:24:24\"}', '2024-10-29 22:24:24'),
(6, 1, 'INSERT', 'ruta', 1, NULL, '{\"id\":1,\"linea_id\":1,\"nombre\":\"Ruta Principal\",\"descripcion\":\"Ruta de ida\",\"estado\":\"activo\",\"fecha_creacion\":\"2024-10-29 22:24:24\"}', '2024-10-29 22:24:24'),
(7, 1, 'INSERT', 'ruta', 2, NULL, '{\"id\":2,\"linea_id\":1,\"nombre\":\"Ruta de Regreso\",\"descripcion\":\"Ruta de vuelta\",\"estado\":\"activo\",\"fecha_creacion\":\"2024-10-29 22:24:24\"}', '2024-10-29 22:24:24'),
(8, 1, 'INSERT', 'tarifa', 1, NULL, '{\"id\":1,\"ruta_id\":1,\"precio\":2.5,\"descripcion\":\"Tarifa normal\",\"estado\":\"activo\",\"fecha_creacion\":\"2024-10-29 22:24:24\"}', '2024-10-29 22:24:24'),
(9, 1, 'INSERT', 'tarifa', 2, NULL, '{\"id\":2,\"ruta_id\":2,\"precio\":2.5,\"descripcion\":\"Tarifa normal\",\"estado\":\"activo\",\"fecha_creacion\":\"2024-10-29 22:24:24\"}', '2024-10-29 22:24:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea`
--

CREATE TABLE `linea` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `linea`
--

INSERT INTO `linea` (`id`, `nombre`, `descripcion`, `estado`, `fecha_creacion`) VALUES
(1, 'Línea 1', 'Línea principal', 'activo', '2024-10-29 22:24:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parada`
--

CREATE TABLE `parada` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `direccion` text NOT NULL,
  `latitud` decimal(10,8) NOT NULL,
  `longitud` decimal(11,8) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parada`
--

INSERT INTO `parada` (`id`, `nombre`, `direccion`, `latitud`, `longitud`, `estado`, `fecha_creacion`) VALUES
(1, 'Parada Central', 'Centro de la ciudad', -12.04637400, -77.04279300, 'activo', '2024-10-29 22:24:24'),
(2, 'Parada Norte', 'Norte de la ciudad', -12.02637400, -77.03279300, 'activo', '2024-10-29 22:24:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ruta`
--

CREATE TABLE `ruta` (
  `id` int(11) NOT NULL,
  `linea_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ruta`
--

INSERT INTO `ruta` (`id`, `linea_id`, `nombre`, `descripcion`, `estado`, `fecha_creacion`) VALUES
(1, 1, 'Ruta Principal', 'Ruta de ida', 'activo', '2024-10-29 22:24:24'),
(2, 1, 'Ruta de Regreso', 'Ruta de vuelta', 'activo', '2024-10-29 22:24:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifa`
--

CREATE TABLE `tarifa` (
  `id` int(11) NOT NULL,
  `ruta_id` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifa`
--

INSERT INTO `tarifa` (`id`, `ruta_id`, `precio`, `descripcion`, `estado`, `fecha_creacion`) VALUES
(1, 1, 2.50, 'Tarifa normal', 'activo', '2024-10-29 22:24:24'),
(2, 2, 2.50, 'Tarifa normal', 'activo', '2024-10-29 22:24:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trigger_history`
--

CREATE TABLE `trigger_history` (
  `id` int(11) NOT NULL,
  `trigger_name` varchar(255) NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `email`, `password`, `rol`, `estado`, `fecha_creacion`) VALUES
(1, 'Admin', 'admin@transporte.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'activo', '2024-10-29 22:24:24'),
(2, 'Usuario', 'usuario@transporte.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario', 'activo', '2024-10-29 22:24:24');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `linea`
--
ALTER TABLE `linea`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `parada`
--
ALTER TABLE `parada`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ruta`
--
ALTER TABLE `ruta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `linea_id` (`linea_id`);

--
-- Indices de la tabla `tarifa`
--
ALTER TABLE `tarifa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ruta_id` (`ruta_id`);

--
-- Indices de la tabla `trigger_history`
--
ALTER TABLE `trigger_history`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `linea`
--
ALTER TABLE `linea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `parada`
--
ALTER TABLE `parada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ruta`
--
ALTER TABLE `ruta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tarifa`
--
ALTER TABLE `tarifa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `trigger_history`
--
ALTER TABLE `trigger_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `ruta`
--
ALTER TABLE `ruta`
  ADD CONSTRAINT `ruta_ibfk_1` FOREIGN KEY (`linea_id`) REFERENCES `linea` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarifa`
--
ALTER TABLE `tarifa`
  ADD CONSTRAINT `tarifa_ibfk_1` FOREIGN KEY (`ruta_id`) REFERENCES `ruta` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
