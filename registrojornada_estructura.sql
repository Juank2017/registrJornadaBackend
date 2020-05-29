-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-05-2020 a las 12:41:48
-- Versión del servidor: 10.4.8-MariaDB
-- Versión de PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `registrojornada` DEFAULT CHARACTER SET utf8 ;
USE `registrojornada` ;
--
-- Base de datos: `registrojornada`
--

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idROL` int(11) NOT NULL,
  `ROL` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `idEMPRESA` int(11) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `cif` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tipo_marcaje`
--

CREATE TABLE `tipo_marcaje` (
  `idTIPO_MARCAJE` int(11) NOT NULL,
  `tipo` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turno`
--

CREATE TABLE `turno` (
  `idTURNO` int(11) NOT NULL,
  `turno` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUSUARIO` int(11) NOT NULL,
  `login` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `idEMPLEADO` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `dni` varchar(10) NOT NULL,
  `idTURNO` int(11) NOT NULL,
  `idSEDE` int(11) NOT NULL,
  `idUSUARIO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Estructura de tabla para la tabla `horario`
--

CREATE TABLE `horario` (
  `idHORARIO` int(11) NOT NULL,
  `hora_entrada` time NOT NULL,
  `hora_salida` time NOT NULL,
  `idEMPLEADO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcado`
--

CREATE TABLE `marcado` (
  `fecha` date NOT NULL,
  `idMarcado` int(11) NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_final` time DEFAULT NULL,
  `longitud` float(16,14) DEFAULT NULL,
  `latitud` float(16,14) DEFAULT NULL,
  `idTIPO_MARCAJE` int(11) NOT NULL,
  `idEMPLEADO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `idNOTIFICACION` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `texto_notificacion` varchar(255) NOT NULL,
  `texto_respuesta` varchar(255) NOT NULL,
  `leida` int(11) NOT NULL,
  `idEMPLEADO` int(11) NOT NULL,
  `loginEmisor` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------



--
-- Estructura de tabla para la tabla `sede`
--

CREATE TABLE `sede` (
  `idSEDE` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `longitud` float NOT NULL,
  `latitud` float NOT NULL,
  `direccion` varchar(60) NOT NULL,
  `idEMPRESA` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------



--
-- Estructura de tabla para la tabla `usuario_empresa`
--

CREATE TABLE `usuario_empresa` (
  `idUSUARIO` int(11) NOT NULL,
  `idEmpresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_rol`
--

CREATE TABLE `usuario_rol` (
  `idUSUARIO` int(11) NOT NULL,
  `idROL` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--



--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`idEMPRESA`),
  ADD UNIQUE KEY `cif_UNIQUE` (`cif`);

--
-- Indices de la tabla `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`idHORARIO`),
  ADD KEY `fk_horario_empleado1_idx` (`idEMPLEADO`);

--
-- Indices de la tabla `marcado`
--
ALTER TABLE `marcado`
  ADD PRIMARY KEY (`idMarcado`),
  ADD KEY `fk_marcado_tipo_marcaje1_idx` (`idTIPO_MARCAJE`),
  ADD KEY `fk_marcado_empleado1_idx` (`idEMPLEADO`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`idNOTIFICACION`),
  ADD KEY `fk_notificacion_empleado1_idx` (`idEMPLEADO`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idROL`);

--
-- Indices de la tabla `sede`
--
ALTER TABLE `sede`
  ADD PRIMARY KEY (`idSEDE`),
  ADD KEY `fk_sede_empresa1_idx` (`idEMPRESA`);

--
-- Indices de la tabla `tipo_marcaje`
--
ALTER TABLE `tipo_marcaje`
  ADD PRIMARY KEY (`idTIPO_MARCAJE`);

--
-- Indices de la tabla `turno`
--
ALTER TABLE `turno`
  ADD PRIMARY KEY (`idTURNO`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUSUARIO`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indices de la tabla `usuario_empresa`
--
ALTER TABLE `usuario_empresa`
  ADD PRIMARY KEY (`idUSUARIO`,`idEmpresa`),
  ADD KEY `fk_empresa` (`idEmpresa`);

--
-- Indices de la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD PRIMARY KEY (`idUSUARIO`,`idROL`),
  ADD KEY `fk_usuario_rol_rol1_idx` (`idROL`),
  ADD KEY `fk_usuario_rol_usuario_idx` (`idUSUARIO`);
  
  --
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`idEMPLEADO`),
  ADD KEY `fk_empleado_turno1_idx` (`idTURNO`),
  ADD KEY `fk_empleado_sede1_idx` (`idSEDE`),
  ADD KEY `fk_empleado_usuario1_idx` (`idUSUARIO`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `idEMPLEADO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `idEMPRESA` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horario`
--
ALTER TABLE `horario`
  MODIFY `idHORARIO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcado`
--
ALTER TABLE `marcado`
  MODIFY `idMarcado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `idNOTIFICACION` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idROL` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sede`
--
ALTER TABLE `sede`
  MODIFY `idSEDE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_marcaje`
--
ALTER TABLE `tipo_marcaje`
  MODIFY `idTIPO_MARCAJE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `idTURNO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUSUARIO` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `fk_empleado_sede1` FOREIGN KEY (`idSEDE`) REFERENCES `sede` (`idSEDE`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_empleado_turno1` FOREIGN KEY (`idTURNO`) REFERENCES `turno` (`idTURNO`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_empleado_usuario1` FOREIGN KEY (`idUSUARIO`) REFERENCES `usuario` (`idUSUARIO`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `horario`
--
ALTER TABLE `horario`
  ADD CONSTRAINT `fk_horario_empleado1` FOREIGN KEY (`idEMPLEADO`) REFERENCES `empleado` (`idEMPLEADO`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `marcado`
--
ALTER TABLE `marcado`
  ADD CONSTRAINT `fk_marcado_empleado1` FOREIGN KEY (`idEMPLEADO`) REFERENCES `empleado` (`idEMPLEADO`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_marcado_tipo_marcaje1` FOREIGN KEY (`idTIPO_MARCAJE`) REFERENCES `tipo_marcaje` (`idTIPO_MARCAJE`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `fk_notificacion_empleado1` FOREIGN KEY (`idEMPLEADO`) REFERENCES `empleado` (`idEMPLEADO`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sede`
--
ALTER TABLE `sede`
  ADD CONSTRAINT `fk_sede_empresa1` FOREIGN KEY (`idEMPRESA`) REFERENCES `empresa` (`idEMPRESA`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuario_empresa`
--
ALTER TABLE `usuario_empresa`
  ADD CONSTRAINT `fk_empresa` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`idEMPRESA`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`idUSUARIO`) REFERENCES `usuario` (`idUSUARIO`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD CONSTRAINT `fk_usuario_rol_rol1` FOREIGN KEY (`idROL`) REFERENCES `rol` (`idROL`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario_rol_usuario` FOREIGN KEY (`idUSUARIO`) REFERENCES `usuario` (`idUSUARIO`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
