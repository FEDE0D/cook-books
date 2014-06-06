-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Servidor: db4free.net:3306
-- Tiempo de generación: 07-06-2014 a las 00:08:22
-- Versión del servidor: 5.6.19
-- Versión de PHP: 5.3.28

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `cookbookg32`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=121 ;

--
-- Volcado de datos para la tabla `autor`
--

INSERT INTO `autor` (`ID`, `nombre`, `apellido`, `fecha_nacimiento`, `lugar_nacimiento`, `eliminado`) VALUES
(100, 'Carmen', 'Valldejuli', '1991-05-06', '', 0),
(101, 'Kristen', 'Feola', '1961-12-20', 'Sprinfield', 0),
(102, 'Mirta G.', 'Carabajal', '1951-10-17', 'Buenos Aires', 0),
(103, 'Doña', 'Gandulfo', '1896-06-29', 'Santiago del Estero, Argentina', 0),
(104, 'Christine', 'Bailey', '2014-05-06', 'Madrid', 0),
(105, 'Tonio', 'Rodriguez', '0000-00-00', '', 0),
(106, 'Cecilia', 'Fassardini', '2014-00-00', '', 0),
(117, 'Federico', 'Pacheco', '0000-00-00', '', 1),
(118, 'Federico', 'Pacheco', '2015-01-01', 'asdasd', 1),
(119, 'Federico', 'Pacheco', '2014-01-01', '', 1),
(120, 'Federico', 'Pacheco', '0000-00-00', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `escribe`
--

CREATE TABLE IF NOT EXISTS `escribe` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_libro` int(40) NOT NULL,
  `id_autor` int(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93 ;

--
-- Volcado de datos para la tabla `escribe`
--

INSERT INTO `escribe` (`id`, `id_libro`, `id_autor`) VALUES
(42, 2, 101),
(43, 3, 102),
(45, 5, 104),
(46, 6, 105),
(47, 7, 106),
(48, 3, 103),
(64, 10, 117),
(67, 11, 117),
(69, 12, 117),
(82, 4, 103),
(87, 14, 117),
(88, 13, 117),
(89, 1, 100),
(92, 15, 117);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE IF NOT EXISTS `libros` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`ID`, `ISBN`, `titulo`, `IDIOMA`, `paginas`, `precio`, `fecha`, `etiquetas`, `texto`, `tapa`, `eliminado`) VALUES
(1, '882894293', 'Cocina criolla', 'Español', 87, 58.99, '1983-03-31', 'criolla', 'Cocina criolla', '', 0),
(2, '123456789', 'La guía optima para el ayuno de Daniel', 'Español', 68, 69, '2001-08-25', 'guía', '', 'libro2.jpg', 0),
(3, '879548481', 'LAS MEJORES RECETAS DE RICO Y ABUNDANTE', 'Español', 70, 87.45, '2012-07-24', 'recetas', '', 'libro3.jpg', 0),
(4, '888444777', 'COCINA CON CALOR DE HOGAR - RUSTICA', 'Español', 154, 152.21, '2006-06-06', 'rustica', '', 'libro4.jpg', 0),
(5, '878987655', 'LA DIETA DE LOS ZUMOS', 'Español', 54, 99.99, '1999-03-15', 'zumos, jugos', '', 'libro5.jpg', 0),
(6, '1478523698', 'CUPCAKES VEGANOS', 'Español', 55, 47.8, '2011-02-01', 'cupcakes', '', 'libro6.jpg', 0),
(7, '2147483647', 'EL LIBRO DE LAS VIANDAS PARA PEQUENOS', 'Español', 87, 79.84, '2012-01-01', 'viandas', '', 'libro7.jpg', 0),
(15, '123', 'Titulo del libro', 'Español', 123, 199.99, '2014-02-01', '', '', '', 1);

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
