-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-11-2025 a las 03:20:55
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
(1, 'plaza del estudiante', 19.4326, -99.1332, 'Activo'),
(4, 'plaza del estudiante', 19.4326, -99.1332, 'Activo'),
(5, 'plaza del estudiante', 19.4326, -99.1332, 'Inactivo');

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
  `id_linea` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ruta`
--

INSERT INTO `ruta` (`id_ruta`, `nombre_ruta`, `hora_inicio`, `hora_final`, `estado`, `id_linea`) VALUES
(1, 'ruta1', '01:00:00', '02:00:00', 'Activo', NULL),
(2, 'ruta2', '01:00:00', '02:00:00', 'Activo', NULL),
(3, 'ruta3', '01:00:00', '02:00:00', 'Activo', NULL);

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
(10, 'juan', 'perez', 'juanito@gmail.com', '6 de agosto', '742345345', NULL, 'usuario', 'Activo', 'guzman'),
(13, 'pedro', 'callizaya', 'pedro123@gmail.com', 'av. buch', '734243', NULL, 'usuario', 'Activo', 'perez'),
(42, 'ijbiñi', 'tik', 'ijbinipro@gmail.com', 'calle_de_la_seriedad', '666', NULL, 'usuario', 'Inactivo', 'tok'),
(43, 'pedro', 'vargas', 'pedrito@gmail.com', 'av. arce', '68544345', '$2y$10$FG03r.JQ4bZfpLRPEbOEY.6pdECtUV0GZeiE3k5L8Dy7jHR63mWIi', 'usuario', 'Activo', 'perez'),
(44, 'yovani', 'Andia', 'yovas@gmail.com', 'Santiago II', '69961678', '$2y$10$SKwZ0r.wfz4QX8ffzzZrGOmPwnXCw1Bu8u.u3.DKMs/RJNRAOblDW', 'usuario', 'Activo', 'Quispe');

--
-- Índices para tablas volcadas
--

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
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `linea`
--
ALTER TABLE `linea`
  MODIFY `id_linea` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `parada`
--
ALTER TABLE `parada`
  MODIFY `id_parada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ruta`
--
ALTER TABLE `ruta`
  MODIFY `id_ruta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
