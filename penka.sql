-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 16-06-2025 a las 00:57:17
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `penka`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunas`
--

DROP TABLE IF EXISTS `comunas`;
CREATE TABLE IF NOT EXISTS `comunas` (
  `idcomunas` int NOT NULL AUTO_INCREMENT,
  `nombre_comuna` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `estado` int NOT NULL,
  `idprovincias` int NOT NULL,
  PRIMARY KEY (`idcomunas`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `comunas`
--

INSERT INTO `comunas` (`idcomunas`, `nombre_comuna`, `estado`, `idprovincias`) VALUES
(1, 'La Serena', 1, 1),
(2, 'Coquimbo', 1, 1),
(3, 'Andacollo', 1, 1),
(4, 'La Higuera', 1, 1),
(5, 'Paiguano', 1, 1),
(6, 'Vicuña', 1, 1),
(7, 'Ovalle', 1, 2),
(8, 'Combarbalá', 1, 2),
(9, 'Monte Patria', 1, 2),
(10, 'Punitaqui', 1, 2),
(11, 'Río Hurtado', 1, 2),
(12, 'Illapel', 1, 3),
(13, 'Canela', 1, 3),
(14, 'Los Vilos', 1, 3),
(15, 'Salamanca', 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria`
--

DROP TABLE IF EXISTS `galeria`;
CREATE TABLE IF NOT EXISTS `galeria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `foto` varchar(255) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `estado` int NOT NULL DEFAULT '1',
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `idpropiedades` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idpropiedades` (`idpropiedades`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `galeria`
--

INSERT INTO `galeria` (`id`, `foto`, `estado`, `principal`, `idpropiedades`) VALUES
(1, 'img/propiedades/1750032630_38bc0b2c-e6f5-4780-a817-f081bd9c7734.webp', 1, 1, 3),
(2, 'img/propiedades/1750032665_a1b29771-0590-450b-a0a5-493873b9ff40.webp', 1, 1, 4),
(3, 'img/propiedades/1750032667_a1b29771-0590-450b-a0a5-493873b9ff40.webp', 1, 1, 5),
(4, 'img/propiedades/1750032856_a1b29771-0590-450b-a0a5-493873b9ff40.webp', 1, 1, 6),
(5, 'img/propiedades/1750033332_38bc0b2c-e6f5-4780-a817-f081bd9c7734.webp', 1, 1, 7),
(6, 'img/propiedades/1750034677_4cf4f171-bd37-4164-988a-d9f8b98e1838 (2).jpg', 1, 1, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestores`
--

DROP TABLE IF EXISTS `gestores`;
CREATE TABLE IF NOT EXISTS `gestores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rut` varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `nombre_completo` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `correo` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `password` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `sexo` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `telefono` varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `certificado_path` varchar(255) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `estado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rut` (`rut`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `gestores`
--

INSERT INTO `gestores` (`id`, `rut`, `nombre_completo`, `fecha_nacimiento`, `correo`, `password`, `sexo`, `telefono`, `certificado_path`, `estado`) VALUES
(1, '15575629-2', 'susana', '2025-05-08', 'susana@hotmail.es', 'aac7d3276fbe433467c3e4dcc6acb76c', 'F', '+56968712289', 'certificados/68266b665e836.pdf', 0),
(3, '19271399-4', 'Victor Manzano penka', '2025-05-09', 'victor@pnk.cl', 'edceaffe9972e6304832f692cf05245d', 'M', '+56966683463', 'certificados/68275c72c3983.png', 1),
(5, '20965953-0', 'Sergio Cubelli', '2025-06-03', 'sergio1@pnk.cl', '68b6c1eded499f85fb38876ff3b2df28', 'M', '+56966683463', 'certificados/684f55118983b.png', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propiedades`
--

DROP TABLE IF EXISTS `propiedades`;
CREATE TABLE IF NOT EXISTS `propiedades` (
  `num_propiedad` int NOT NULL AUTO_INCREMENT,
  `rut_propietario` varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `titulopropiedad` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `descripcion` text CHARACTER SET latin1 COLLATE latin1_spanish_ci,
  `cant_banos` int DEFAULT NULL,
  `cant_domitorios` int DEFAULT NULL,
  `area_total` int DEFAULT NULL,
  `area_construida` int DEFAULT NULL,
  `precio_pesos` int DEFAULT NULL,
  `precio_uf` int DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `idtipo_propiedad` int NOT NULL,
  `bodega` int DEFAULT NULL,
  `estacionamiento` int DEFAULT NULL,
  `logia` int DEFAULT NULL,
  `cocinaamoblada` int DEFAULT NULL,
  `antejardin` int DEFAULT NULL,
  `patiotrasero` int DEFAULT NULL,
  `piscina` int DEFAULT NULL,
  `idsectores` int NOT NULL,
  PRIMARY KEY (`num_propiedad`),
  KEY `fk_propiedades_tipo_propiedad1_idx` (`idtipo_propiedad`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `propiedades`
--

INSERT INTO `propiedades` (`num_propiedad`, `rut_propietario`, `titulopropiedad`, `descripcion`, `cant_banos`, `cant_domitorios`, `area_total`, `area_construida`, `precio_pesos`, `precio_uf`, `fecha_publicacion`, `estado`, `idtipo_propiedad`, `bodega`, `estacionamiento`, `logia`, `cocinaamoblada`, `antejardin`, `patiotrasero`, `piscina`, `idsectores`) VALUES
(7, '12345678-9', '22', '22', 22, 22, 22, 22, 22, 22, '2025-06-15', 1, 1, 0, 1, 1, 0, 1, 0, 0, 19),
(8, '98765432-1', 'sexo', '69696969', 3, 4, 23, 23, 234234, 233, '2025-06-15', 1, 2, 1, 1, 0, 1, 1, 1, 1, 24);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios`
--

DROP TABLE IF EXISTS `propietarios`;
CREATE TABLE IF NOT EXISTS `propietarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rut` varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `nombre_completo` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `correo` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `sexo` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `telefono` varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rut` (`rut`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincias`
--

DROP TABLE IF EXISTS `provincias`;
CREATE TABLE IF NOT EXISTS `provincias` (
  `idprovincias` int NOT NULL AUTO_INCREMENT,
  `nombre_provincia` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `estado` int NOT NULL,
  `idregion` int NOT NULL,
  PRIMARY KEY (`idprovincias`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `provincias`
--

INSERT INTO `provincias` (`idprovincias`, `nombre_provincia`, `estado`, `idregion`) VALUES
(1, 'Elqui', 1, 4),
(2, 'Limari', 1, 4),
(3, 'Choapa', 1, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regiones`
--

DROP TABLE IF EXISTS `regiones`;
CREATE TABLE IF NOT EXISTS `regiones` (
  `idregion` int NOT NULL AUTO_INCREMENT,
  `nombre_region` varchar(30) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `estado` int NOT NULL,
  PRIMARY KEY (`idregion`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `regiones`
--

INSERT INTO `regiones` (`idregion`, `nombre_region`, `estado`) VALUES
(4, 'Región de Coquimbo', 1),
(3, 'Region de Atacama', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sectores`
--

DROP TABLE IF EXISTS `sectores`;
CREATE TABLE IF NOT EXISTS `sectores` (
  `idsectores` int NOT NULL AUTO_INCREMENT,
  `nombre_sector` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `estado` int NOT NULL,
  `idcomunas` int NOT NULL,
  PRIMARY KEY (`idsectores`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `sectores`
--

INSERT INTO `sectores` (`idsectores`, `nombre_sector`, `estado`, `idcomunas`) VALUES
(1, 'Bosque San Carlos', 1, 2),
(2, 'Tierras Blancas', 1, 2),
(3, 'Sindempart', 1, 2),
(4, 'La Cantera', 1, 2),
(5, 'La Florida', 1, 1),
(6, 'Las Compañias', 1, 1),
(7, 'Centro de La Serena', 1, 1),
(8, 'La Pampa', 1, 1),
(9, 'Centro de Ovalle', 1, 7),
(10, 'Media Hacienda', 1, 7),
(11, 'El Molino', 1, 7),
(12, 'Centro de Combarbalá', 1, 8),
(13, 'La Isla', 1, 8),
(14, 'Monte Patria Centro', 1, 9),
(15, 'El Palqui', 1, 9),
(16, 'Punitaqui Centro', 1, 10),
(17, 'El Tome Alto', 1, 10),
(18, 'Población Hurtado', 1, 11),
(19, 'Centro de Illapel', 1, 12),
(20, 'Villa San Rafael', 1, 12),
(21, 'Canela Alta', 1, 13),
(22, 'Canela Baja', 1, 13),
(23, 'Centro de Los Vilos', 1, 14),
(24, 'Pichidangui', 1, 14),
(25, 'Salamanca Centro', 1, 15),
(26, 'Chalinga', 1, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_propiedad`
--

DROP TABLE IF EXISTS `tipo_propiedad`;
CREATE TABLE IF NOT EXISTS `tipo_propiedad` (
  `idtipo_propiedad` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `estado` int DEFAULT NULL,
  PRIMARY KEY (`idtipo_propiedad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `tipo_propiedad`
--

INSERT INTO `tipo_propiedad` (`idtipo_propiedad`, `tipo`, `estado`) VALUES
(1, 'Casas', 1),
(2, 'Departamentos', 1),
(3, 'Terrenos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rut` varchar(12) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `nombres` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ap_paterno` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ap_materno` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `usuario` varchar(50) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `clave` varchar(250) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `estado` int NOT NULL DEFAULT '1',
  `tipo_usuario` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT 'usuario',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rut` (`rut`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `rut`, `nombres`, `ap_paterno`, `ap_materno`, `usuario`, `clave`, `estado`, `tipo_usuario`) VALUES
(12, '11111111-9', 'Admin', 'Admin', 'Admin', 'admin@pnk.cl', '$2a$12$G4nbFZGk0XBGRDbvuVY53u1zSdNkz76XCexOsU0hMkJFIwDb33b5W', 1, 'admin');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `propiedades`
--
ALTER TABLE `propiedades`
  ADD CONSTRAINT `fk_propiedades_tipo_propiedad1` FOREIGN KEY (`idtipo_propiedad`) REFERENCES `tipo_propiedad` (`idtipo_propiedad`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
