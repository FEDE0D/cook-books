
-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 11-06-2014 a las 20:02:10
-- Versión del servidor: 5.1.61
-- Versión de PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `u847065820_cb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autor`
--

CREATE TABLE IF NOT EXISTS `autor` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `apellido` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT '0000-00-00',
  `lugar_nacimiento` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=137 ;

--
-- Volcado de datos para la tabla `autor`
--

INSERT INTO `autor` (`ID`, `nombre`, `apellido`, `fecha_nacimiento`, `lugar_nacimiento`, `eliminado`) VALUES
(100, 'Carmen', 'Valldejuli', '1991-05-06', '', 0),
(101, 'Kristen', 'Feola', '1961-12-20', 'Sprinfield', 0),
(102, 'Mirta G.', 'Carabajal', '1951-10-17', 'Buenos Aires', 0),
(103, 'Doña', 'Gandulfo', '1896-06-29', 'Santiago del Estero, Argentina', 0),
(104, 'Tonio', 'Rodriguez', '0000-00-00', '', 0),
(105, 'Cecilia', 'Fassardini', '2014-00-00', '', 0),
(134, 'Federico', 'Pacheco', '0000-00-00', '', 1),
(135, 'Federico', 'Pacheco', '0000-00-00', '', 1),
(136, 'Federico', 'Pacheco', '0000-00-00', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `escribe`
--

CREATE TABLE IF NOT EXISTS `escribe` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `isbn` int(40) NOT NULL,
  `id_autor` int(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=131 ;

--
-- Volcado de datos para la tabla `escribe`
--

INSERT INTO `escribe` (`id`, `isbn`, `id_autor`) VALUES
(105, 882894293, 100),
(106, 123456789, 101),
(107, 879548481, 102),
(108, 888444777, 103),
(109, 878987655, 104),
(110, 1478523698, 105),
(111, 2147483647, 104),
(112, 2147483647, 105),
(128, 1234, 102),
(129, 1234, 101),
(130, 1234, 103);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE IF NOT EXISTS `libros` (
  `ISBN` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `titulo` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `IDIOMA` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `paginas` int(11) NOT NULL DEFAULT '0',
  `precio` float NOT NULL DEFAULT '0',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `etiquetas` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `texto` text COLLATE utf8_unicode_ci,
  `tapa` text COLLATE utf8_unicode_ci,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL,
  PRIMARY KEY (`ISBN`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`ISBN`, `titulo`, `IDIOMA`, `paginas`, `precio`, `fecha`, `etiquetas`, `texto`, `tapa`, `eliminado`, `hidden`) VALUES
('882894293', 'Cocina criolla', 'Español', 87, 58.99, '1983-03-31', 'criolla', 'Cocina criolla', 'libro1.jpg', 0, 0),
('123456789', 'La guía optima para el ayuno de Daniel', 'Español', 68, 69, '2001-08-25', 'guía', '', 'libro2.jpg', 0, 0),
('879548481', 'LAS MEJORES RECETAS DE RICO Y ABUNDANTE', 'Español', 70, 87.45, '2012-07-24', 'recetas', '', 'libro3.jpg', 0, 0),
('888444777', 'COCINA CON CALOR DE HOGAR - RUSTICA', 'Español', 154, 152.21, '2006-06-06', 'rustica', '', 'libro4.jpg', 0, 0),
('878987655', 'LA DIETA DE LOS ZUMOS', 'Español', 54, 99.99, '1999-03-15', 'zumos, jugos', 'Descripcion', 'libro5.jpg', 0, 0),
('1478523698', 'CUPCAKES VEGANOS', 'Español', 55, 47.8, '2011-02-01', 'cupcakes', '', 'libro6.jpg', 0, 0),
('2147483647', 'EL LIBRO DE LAS VIANDAS PARA PEQUENOS', 'Español', 87, 79.84, '2012-01-01', 'viandas', '', 'libro7.jpg', 0, 0),
('1234', '12', 'Español', 12, 12, '0004-03-12', '', '', '', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ISBN` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `usuario` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `estado` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `username` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `apellido` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `direccion` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mail` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` tinyint(1) NOT NULL,
  `fecha_alta` date NOT NULL,
  `fecha_nac` date DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`username`, `password`, `nombre`, `apellido`, `direccion`, `mail`, `telefono`, `admin`, `fecha_alta`, `fecha_nac`) VALUES
('fede0d', 'federico', 'Federico', 'Pacheco', '10 nº 1748', 'federicogpacheco@gmail.com', '2214417432', 0, '2014-04-12', '1991-05-06'),
('usuario', 'fede', '', '', '', 'caca@caca.com', '', 0, '2014-04-21', '0000-00-00'),
('uno', 'federico', '', '', '', 'federico@federico', '', 0, '2014-05-13', '0000-00-00'),
('alguien', 'federico', '', '', '', 'alguien@algo.com', '', 0, '2014-05-12', '0000-00-00'),
('asd', 'asd', '', '', '', 'asd@asd', '', 0, '2014-05-09', '0000-00-00'),
('tres', '123', '', '', '', 't@a', '', 0, '2014-05-09', '0000-00-00'),
('alguien2', 'federico', '', '', '', 'alguien@asd', '', 0, '2014-05-26', '0000-00-00'),
('admin', 'admin', NULL, NULL, NULL, NULL, NULL, 1, '2014-05-26', '0000-00-00'),
('otro', 'federico', '', '', '', 'federico@gmail.com', '', 0, '2014-06-01', '0000-00-00'),
('rami', '123', '', '', '', 'ramiii.92@hotmail.com', '', 0, '2014-06-02', '0000-00-00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
