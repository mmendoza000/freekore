-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 30-08-2011 a las 04:52:54
-- Versión del servidor: 5.5.8
-- Versión de PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `freekore_v2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fk_controllers`
--

CREATE TABLE IF NOT EXISTS `fk_controllers` (
  `id_controller` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(100) NOT NULL,
  PRIMARY KEY (`id_controller`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `fk_controllers`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fk_controllers_action`
--

CREATE TABLE IF NOT EXISTS `fk_controllers_action` (
  `id_action` int(11) NOT NULL AUTO_INCREMENT,
  `id_controller` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  PRIMARY KEY (`id_action`),
  KEY `fk_action_controller` (`id_controller`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `fk_controllers_action`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fk_perfiles`
--

CREATE TABLE IF NOT EXISTS `fk_perfiles` (
  `id_perfil` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_perfil` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id_perfil`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `fk_perfiles`
--

INSERT INTO `fk_perfiles` (`id_perfil`, `nombre_perfil`, `descripcion`) VALUES
(1, 'SUPER-ADMIN', 'SUPER ADMINISRADOR FREEKORE ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fk_perfiles_privs`
--

CREATE TABLE IF NOT EXISTS `fk_perfiles_privs` (
  `id_perfil_priv` int(11) NOT NULL AUTO_INCREMENT,
  `id_perfil` int(11) NOT NULL,
  `id_priv` int(11) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `read_only` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_perfil_priv`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `fk_perfiles_privs`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fk_privileges`
--

CREATE TABLE IF NOT EXISTS `fk_privileges` (
  `id_priv` int(11) NOT NULL AUTO_INCREMENT,
  `privilege_desc` varchar(200) NOT NULL,
  `privilege_help` text NOT NULL,
  `mode` int(11) NOT NULL,
  `id_controller` int(11) DEFAULT NULL,
  `id_action` int(11) DEFAULT NULL,
  `table_name` varchar(70) NOT NULL,
  `field_name` varchar(60) NOT NULL,
  PRIMARY KEY (`id_priv`),
  KEY `fk_privileges_controller` (`id_controller`),
  KEY `fk_privileges_controller_act` (`id_action`),
  KEY `fk_privileges_modepriv` (`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `fk_privileges`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fk_privileges_usuarios`
--

CREATE TABLE IF NOT EXISTS `fk_privileges_usuarios` (
  `id_priv_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_priv` int(11) NOT NULL,
  `permitir_acceso` tinyint(1) NOT NULL,
  `solo_lectura` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_priv_usuario`),
  UNIQUE KEY `uk_usr_priv` (`id_usuario`,`id_priv`),
  KEY `fk_privusr_priv` (`id_priv`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `fk_privileges_usuarios`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(10) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(45) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(80) NOT NULL,
  `password` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `fecha_reg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `nombre`, `apellidos`, `password`, `email`, `id_perfil`, `fecha_reg`) VALUES
(1, 'admin', 'Administrador de', 'Freekore', '21232f297a57a5a743894a0e4a801fc3', '', 1, '2011-08-29 21:52:18');

--
-- Filtros para las tablas descargadas (dump)
--

--
-- Filtros para la tabla `fk_controllers_action`
--
ALTER TABLE `fk_controllers_action`
  ADD CONSTRAINT `fk_action_controller` FOREIGN KEY (`id_controller`) REFERENCES `fk_controllers` (`id_controller`);

--
-- Filtros para la tabla `fk_privileges`
--
ALTER TABLE `fk_privileges`
  ADD CONSTRAINT `fk_privileges_controller` FOREIGN KEY (`id_controller`) REFERENCES `fk_controllers` (`id_controller`),
  ADD CONSTRAINT `fk_privileges_controller_act` FOREIGN KEY (`id_action`) REFERENCES `fk_controllers_action` (`id_action`);

--
-- Filtros para la tabla `fk_privileges_usuarios`
--
ALTER TABLE `fk_privileges_usuarios`
  ADD CONSTRAINT `fk_privusr_priv` FOREIGN KEY (`id_priv`) REFERENCES `fk_privileges` (`id_priv`);
