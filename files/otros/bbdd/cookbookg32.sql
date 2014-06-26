-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 23-06-2014 a las 18:12:27
-- Versión del servidor: 5.5.27
-- Versión de PHP: 5.4.7

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=112 ;

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
(106, 'Federico', 'Pacheco', '0000-00-00', '', 1),
(107, 'sdasd', 'ad', '0000-00-00', '', 1),
(108, 'Federico', 'Pacheco', '0000-00-00', '', 1),
(109, 'Federico', 'Pacheco', '1991-05-06', 'Berisso, BA', 1),
(110, 'Federico', 'Pacheco', '0000-00-00', '', 1),
(111, 'Kristencita', 'Feola', '0000-00-00', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE IF NOT EXISTS `compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `estado` varchar(20) NOT NULL,
  `username` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`id`, `fecha`, `estado`, `username`) VALUES
(1, '2014-06-02', 'pendiente', 'fede0d'),
(7, '2014-06-22', 'cancelado', 'fede0d'),
(8, '2014-06-22', 'efectuado', 'uno');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `escribe`
--

CREATE TABLE IF NOT EXISTS `escribe` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `isbn` int(40) NOT NULL,
  `id_autor` int(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=281 ;

--
-- Volcado de datos para la tabla `escribe`
--

INSERT INTO `escribe` (`id`, `isbn`, `id_autor`) VALUES
(206, 12345, 105),
(211, 1234, 102),
(212, 1234, 101),
(213, 1234, 103),
(251, 882894293, 100),
(256, 1478523698, 105),
(257, 2147483647, 105),
(258, 2147483647, 104),
(259, 878987655, 104),
(260, 879548481, 102),
(261, 888444777, 103),
(276, 123456789, 105),
(277, 123456789, 101),
(278, 123456789, 103),
(279, 123456789, 104),
(280, 123456789, 100);

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
('882894293', 'Cocina criolla', 'Español', 87, 58.99, '1983-03-31', 'criolla', 'Coincidiendo con la llegada al Palacio de La Moncloa del actual presidente del Gobierno, Mariano Rajoy, y su familia, Julio González de Buitrago, el cocinero de la casa durante treinta y tres años, se jubila. Es el momento de hacer balance, de compartir con los lectores recuerdos y vivencias, momentos estelares y pequeños sinsabores. Descubrir curiosidades inéditas que nos hablan de la cara más humana de nuestros presidentes y sus familias, pero también de ídolos del deporte, representantes de la cultura o grandes estadistas internacionales. Salpican estas páginas diversas recetas especialmente queridas por el autor, que ahora comparte con los lectores.', 'cocina_criolla.jpg', 0, 0),
('123456789', 'La guía optima para el ayuno de Daniel', 'Español', 68, 69, '2001-08-25', 'guía,desayuno,comida', 'Coincidiendo con la llegada al Palacio de La Moncloa del actual presidente del Gobierno, Mariano Rajoy, y su familia, Julio González de Buitrago, el cocinero de la casa durante treinta y tres años, se jubila. Es el momento de hacer balance, de compartir con los lectores recuerdos y vivencias, momentos estelares y pequeños sinsabores. Descubrir curiosidades inéditas que nos hablan de la cara más humana de nuestros presidentes y sus familias, pero también de ídolos del deporte, representantes de la cultura o grandes estadistas internacionales. Salpican estas páginas diversas recetas especialmente queridas por el autor, que ahora comparte con los lectores.', 'libro1.jpg', 0, 1),
('879548481', 'LAS MEJORES RECETAS DE RICO Y ABUNDANTE', 'Español', 70, 87.45, '2012-07-24', 'recetas', 'Coincidiendo con la llegada al Palacio de La Moncloa del actual presidente del Gobierno, Mariano Rajoy, y su familia, Julio González de Buitrago, el cocinero de la casa durante treinta y tres años, se jubila. Es el momento de hacer balance, de compartir con los lectores recuerdos y vivencias, momentos estelares y pequeños sinsabores. Descubrir curiosidades inéditas que nos hablan de la cara más humana de nuestros presidentes y sus familias, pero también de ídolos del deporte, representantes de la cultura o grandes estadistas internacionales. Salpican estas páginas diversas recetas especialmente queridas por el autor, que ahora comparte con los lectores.', 'libro8.jpg', 0, 0),
('888444777', 'COCINA CON CALOR DE HOGAR - RUSTICA', 'Español', 154, 152.21, '2006-06-06', 'rustica', 'Coincidiendo con la llegada al Palacio de La Moncloa del actual presidente del Gobierno, Mariano Rajoy, y su familia, Julio González de Buitrago, el cocinero de la casa durante treinta y tres años, se jubila. Es el momento de hacer balance, de compartir con los lectores recuerdos y vivencias, momentos estelares y pequeños sinsabores. Descubrir curiosidades inéditas que nos hablan de la cara más humana de nuestros presidentes y sus familias, pero también de ídolos del deporte, representantes de la cultura o grandes estadistas internacionales. Salpican estas páginas diversas recetas especialmente queridas por el autor, que ahora comparte con los lectores.', 'wilber.png', 0, 0),
('878987655', 'LA DIETA DE LOS ZUMOS', 'Español', 54, 99.99, '1999-03-15', 'zumos, jugos', 'Coincidiendo con la llegada al Palacio de La Moncloa del actual presidente del Gobierno, Mariano Rajoy, y su familia, Julio González de Buitrago, el cocinero de la casa durante treinta y tres años, se jubila. Es el momento de hacer balance, de compartir con los lectores recuerdos y vivencias, momentos estelares y pequeños sinsabores. Descubrir curiosidades inéditas que nos hablan de la cara más humana de nuestros presidentes y sus familias, pero también de ídolos del deporte, representantes de la cultura o grandes estadistas internacionales. Salpican estas páginas diversas recetas especialmente queridas por el autor, que ahora comparte con los lectores.', 'libro7.jpg', 0, 0),
('1478523698', 'CUPCAKES VEGANOS', 'Español', 55, 47.8, '2011-02-01', 'cupcakes', 'Coincidiendo con la llegada al Palacio de La Moncloa del actual presidente del Gobierno, Mariano Rajoy, y su familia, Julio González de Buitrago, el cocinero de la casa durante treinta y tres años, se jubila. Es el momento de hacer balance, de compartir con los lectores recuerdos y vivencias, momentos estelares y pequeños sinsabores. Descubrir curiosidades inéditas que nos hablan de la cara más humana de nuestros presidentes y sus familias, pero también de ídolos del deporte, representantes de la cultura o grandes estadistas internacionales. Salpican estas páginas diversas recetas especialmente queridas por el autor, que ahora comparte con los lectores.', 'libro2.jpg', 0, 0),
('2147483647', 'EL LIBRO DE LAS VIANDAS PARA PEQUENOS', 'Español', 87, 79.84, '2012-01-01', 'viandas', 'Coincidiendo con la llegada al Palacio de La Moncloa del actual presidente del Gobierno, Mariano Rajoy, y su familia, Julio González de Buitrago, el cocinero de la casa durante treinta y tres años, se jubila. Es el momento de hacer balance, de compartir con los lectores recuerdos y vivencias, momentos estelares y pequeños sinsabores. Descubrir curiosidades inéditas que nos hablan de la cara más humana de nuestros presidentes y sus familias, pero también de ídolos del deporte, representantes de la cultura o grandes estadistas internacionales. Salpican estas páginas diversas recetas especialmente queridas por el autor, que ahora comparte con los lectores.', 'libro3.jpg', 0, 0),
('1234', '1234', 'Español', 123, 124.99, '2004-03-12', 'test,pruebas', '', '', 1, 1),
('12345', 'Federico', 'Español', 123, 123, '2014-01-01', '', '', '', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ISBN` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` double NOT NULL,
  `id_compra` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `ISBN`, `cantidad`, `precio_unitario`, `id_compra`) VALUES
(2, 882894293, 2, 60, 1),
(3, 123456789, 1, 50, 1),
(16, 1478523698, 1, 47.8, 8),
(15, 2147483647, 2, 79.84, 8),
(14, 878987655, 3, 99.99, 8),
(13, 2147483647, 2, 79.84, 7),
(12, 1478523698, 1, 47.8, 7);

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
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`username`, `password`, `nombre`, `apellido`, `direccion`, `mail`, `telefono`, `admin`, `fecha_alta`, `fecha_nac`, `enabled`) VALUES
('fede0d', 'federico', 'Federico', 'Pacheco', '10 nº 1748', 'federicogpacheco@gmail.com', '2214417432', 0, '2014-04-12', '1991-05-06', 1),
('usuario', 'fede', '', '', '', 'caca@caca.com', '', 0, '2014-04-21', '0000-00-00', 0),
('uno', 'federico', '', '', '', 'federico@federico', '', 0, '2014-05-13', '0000-00-00', 1),
('alguien', 'federico', '', '', '', 'alguien@algo.com', '', 0, '2014-05-12', '0000-00-00', 0),
('asd', 'asd', '', '', '', 'asd@asd', '', 0, '2014-05-09', '0000-00-00', 0),
('tres', '123', '', '', '', 't@a', '', 0, '2014-05-09', '0000-00-00', 0),
('alguien2', 'federico', '', '', '', 'alguien@asd', '', 0, '2014-05-26', '0000-00-00', 0),
('admin', 'admin', NULL, NULL, NULL, NULL, NULL, 1, '2014-05-26', '0000-00-00', 1),
('otro2', 'federico', '', '', '', 'federico@gmail.com', '', 0, '2014-06-01', '0000-00-00', 0),
('rami', '123', '', '', '', 'ramiii.92@hotmail.com', '', 0, '2014-06-02', '0000-00-00', 1),
('uno1', 'uno', '', '', '', 'uno@uno', '', 0, '2014-06-20', '0000-00-00', 1),
('dos', 'dos', '', '', '', 'dos@dos', '', 0, '2014-06-20', '0000-00-00', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
