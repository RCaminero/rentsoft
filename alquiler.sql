-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-09-2025 a las 16:58:47
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
-- Base de datos: `alquiler`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alquiler`
--

CREATE TABLE `alquiler` (
  `id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `tipo_precio` int(11) NOT NULL DEFAULT 0,
  `monto` decimal(10,2) NOT NULL,
  `abono` decimal(10,2) NOT NULL,
  `fecha_prestamo` datetime NOT NULL,
  `fecha_devolucion` datetime NOT NULL,
  `observacion` text DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `id_cliente` int(11) NOT NULL,
  `id_vehiculo` int(11) NOT NULL,
  `id_doc` int(11) NOT NULL,
  `id_caja` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alquiler`
--

INSERT INTO `alquiler` (`id`, `cantidad`, `tipo_precio`, `monto`, `abono`, `fecha_prestamo`, `fecha_devolucion`, `observacion`, `estado`, `id_cliente`, `id_vehiculo`, `id_doc`, `id_caja`, `fecha`) VALUES
(1, 2, 1, 2000.00, 2000.00, '2025-06-25 16:00:00', '2025-06-25 18:00:00', 'ninguna', 0, 3, 1, 1, NULL, '2025-06-28 21:21:38'),
(2, 1, 1, 10000.00, 50000.00, '2025-06-26 16:00:00', '2025-06-26 17:00:00', '', 0, 3, 2, 1, NULL, '2025-06-28 21:21:38'),
(3, 2, 1, 2000.00, 4000.00, '2025-06-27 15:30:00', '2025-06-27 17:30:00', 'kkkk', 0, 3, 1, 1, NULL, '2025-06-28 21:21:38'),
(4, 3, 1, 2000.00, 6000.00, '2025-06-21 12:42:00', '2025-06-21 15:42:00', '', 0, 3, 1, 1, NULL, '2025-06-28 21:21:38'),
(5, 2, 1, 2000.00, 4000.00, '2025-06-28 20:23:00', '2025-06-28 22:23:00', '', 0, 3, 1, 1, NULL, '2025-06-28 21:23:51'),
(6, 2, 1, 2000.00, 4000.00, '2025-06-29 18:23:00', '2025-06-29 20:23:00', 'nada', 0, 4, 1, 1, NULL, '2025-06-29 22:23:23'),
(7, 2, 1, 2000.00, 4000.00, '2025-07-02 23:51:00', '2025-07-03 01:51:00', '', 0, 4, 1, 1, NULL, '2025-07-03 03:51:23'),
(8, 2, 1, 2000.00, 4000.00, '2025-07-02 23:54:00', '2025-07-03 01:54:00', '', 0, 3, 1, 1, NULL, '2025-07-03 03:54:19'),
(9, 2, 1, 2000.00, 4000.00, '2025-07-02 23:58:00', '2025-07-03 01:58:00', '', 0, 3, 1, 1, NULL, '2025-07-03 03:58:58'),
(10, 2, 1, 2000.00, 4000.00, '2025-07-03 00:18:00', '2025-07-03 02:18:00', '', 0, 3, 1, 1, NULL, '2025-07-03 04:18:42'),
(11, 2, 1, 2000.00, 4000.00, '2025-07-03 00:23:00', '2025-07-03 02:23:00', '', 0, 3, 1, 1, NULL, '2025-07-03 04:23:31'),
(12, 2, 1, 2000.00, 4000.00, '2025-07-03 00:34:00', '2025-07-03 02:34:00', '', 0, 3, 1, 1, 19, '2025-07-03 04:34:44'),
(13, 4, 1, 2000.00, 8000.00, '2025-07-10 00:48:00', '2025-07-10 04:48:00', '', 0, 4, 1, 1, 20, '2025-07-03 04:48:57'),
(14, 2, 1, 2000.00, 4000.00, '2025-07-03 07:50:00', '2025-07-03 09:50:00', '', 0, 3, 1, 1, 21, '2025-07-03 11:51:03'),
(15, 2, 1, 2000.00, 4000.00, '2025-07-03 07:51:00', '2025-07-03 09:51:00', '', 0, 4, 1, 1, 21, '2025-07-03 11:51:38'),
(16, 4, 1, 2000.00, 8000.00, '2025-07-03 09:53:00', '2025-07-03 13:53:00', '', 0, 4, 1, 1, 22, '2025-07-03 13:53:13'),
(17, 2, 1, 2000.00, 4000.00, '2025-07-03 18:14:00', '2025-07-03 20:14:00', '', 0, 4, 1, 1, 23, '2025-07-03 22:14:12'),
(18, 2, 1, 2000.00, 4000.00, '2025-07-03 18:14:00', '2025-07-03 20:14:00', '', 0, 3, 1, 1, 23, '2025-07-03 22:14:45'),
(19, 2, 1, 2000.00, 4000.00, '2025-07-02 18:27:00', '2025-07-02 20:27:00', '', 0, 3, 1, 1, 24, '2025-07-03 22:27:06'),
(20, 2, 1, 2000.00, 4000.00, '2025-07-03 23:28:00', '2025-07-04 01:28:00', '', 0, 3, 1, 1, 25, '2025-07-04 03:28:30'),
(21, 2, 1, 2000.00, 4000.00, '2025-07-07 21:16:00', '2025-07-07 23:16:00', '', 0, 3, 1, 1, 26, '2025-07-08 01:16:42'),
(22, 2, 1, 2000.00, 4000.00, '2025-07-14 18:00:00', '2025-07-14 20:00:00', 'nada', 0, 3, 1, 1, 27, '2025-07-14 21:51:33'),
(23, 2, 1, 2000.00, 4000.00, '2025-07-14 19:38:00', '2025-07-14 21:38:00', '', 0, 4, 1, 1, 28, '2025-07-14 23:38:29'),
(24, 2, 1, 2000.00, 4000.00, '2025-07-21 20:30:00', '2025-07-21 22:30:00', '', 0, 3, 1, 1, 30, '2025-07-21 21:32:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `id` int(11) NOT NULL,
  `fecha_apertura` date NOT NULL,
  `hora_apertura` time NOT NULL,
  `efectivo_inicio` decimal(10,2) NOT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `hora_cierre` time DEFAULT NULL,
  `efectivo_cierre` decimal(10,2) DEFAULT NULL,
  `total_ventas` decimal(10,2) DEFAULT 0.00,
  `total_gastos` decimal(10,2) DEFAULT 0.00,
  `total_alquiler` decimal(10,2) DEFAULT 0.00,
  `observacion` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `caja`
--

INSERT INTO `caja` (`id`, `fecha_apertura`, `hora_apertura`, `efectivo_inicio`, `fecha_cierre`, `hora_cierre`, `efectivo_cierre`, `total_ventas`, `total_gastos`, `total_alquiler`, `observacion`, `id_usuario`, `estado`) VALUES
(1, '2025-06-28', '12:23:52', 1000.00, '2025-06-28', '12:27:39', 2000.00, 1000.00, 0.00, 0.00, '  [CANCELADA]', 1, 2),
(2, '2025-06-28', '12:38:02', 5000.00, '2025-06-28', '12:38:19', 2000.00, 0.00, 0.00, 0.00, '  [CANCELADA]', 1, 2),
(3, '2025-06-28', '12:39:15', 3000.00, '2025-06-28', '12:39:25', 4000.00, 0.00, 0.00, 0.00, '  [CANCELADA]', 1, 2),
(4, '2025-06-28', '12:41:32', 3000.00, '2025-06-28', '12:44:53', 7000.00, 1000.00, 0.00, 0.00, '', 1, 0),
(5, '2025-06-28', '13:01:08', 7000.00, '2025-06-28', '13:02:10', 9000.00, 1000.00, 0.00, 0.00, '', 1, 0),
(6, '2025-06-28', '17:04:34', 2000.00, '2025-06-28', '17:24:15', 6000.00, 0.00, 0.00, 0.00, '', 1, 0),
(7, '2025-06-28', '17:24:40', 1000.00, '2025-06-28', '17:25:11', 1000.00, 0.00, 0.00, 4000.00, '', 1, 0),
(8, '2025-06-29', '18:26:27', 70000.00, '2025-06-29', '18:26:50', 7000.00, 0.00, 0.00, 4000.00, '', 1, 0),
(9, '2025-07-02', '13:55:03', 20000.00, '2025-07-02', '15:13:01', 10000.00, 2000.00, 0.00, 0.00, '', 1, 0),
(10, '2025-07-02', '19:22:01', 20000.00, '2025-07-02', '23:09:05', 20000.00, 4030.00, 2250.00, 0.00, '', 1, 0),
(11, '2025-07-02', '23:20:57', 20000.00, '2025-07-02', '23:22:17', 20000.00, 0.00, 0.00, 0.00, '', 1, 0),
(12, '2025-07-02', '23:28:18', 100000.00, '2025-07-02', '23:31:34', 0.00, 0.00, 0.00, 0.00, '', 1, 0),
(13, '2025-07-02', '23:32:26', 20000.00, '2025-07-02', '23:34:52', 20000.00, 0.00, 0.00, 0.00, '', 1, 0),
(14, '2025-07-02', '23:38:14', 12000.00, '2025-07-02', '23:38:20', 12000.00, 0.00, 0.00, 0.00, '', 1, 0),
(15, '2025-07-02', '23:50:43', 2000.00, '2025-07-02', '23:59:45', 2000.00, 0.00, 2000.00, 12000.00, '', 1, 0),
(16, '2025-07-03', '00:18:18', 0.00, '2025-07-03', '00:19:40', 1745.00, 2000.00, 255.00, 0.00, '', 1, 0),
(17, '2025-07-03', '00:22:05', 0.00, '2025-07-03', '00:22:34', 0.00, 0.00, 0.00, 4000.00, '', 1, 0),
(18, '2025-07-03', '00:22:43', 0.00, '2025-07-03', '00:23:48', 0.00, 0.00, 0.00, 8000.00, '', 1, 0),
(19, '2025-07-03', '00:29:53', 0.00, '2025-07-03', '00:35:01', 4000.00, 0.00, 0.00, 12000.00, '  [CANCELADA]', 1, 2),
(20, '2025-07-03', '00:48:17', 0.00, '2025-07-03', '00:50:20', 8000.00, 2000.00, 2000.00, 12000.00, '', 1, 0),
(21, '2025-07-03', '07:49:53', 0.00, '2025-07-03', '07:52:58', 9750.00, 2000.00, 250.00, 20000.00, '', 1, 0),
(22, '2025-07-03', '09:52:28', 0.00, '2025-07-03', '09:54:22', 9400.00, 2000.00, 600.00, 8000.00, '', 1, 0),
(23, '2025-07-03', '18:13:42', 0.00, '2025-07-03', '18:19:15', 6800.00, 2000.00, 3200.00, 8000.00, '', 1, 0),
(24, '2025-07-03', '18:26:41', 0.00, '2025-07-03', '18:27:54', 4000.00, 0.00, 0.00, 4000.00, '', 1, 0),
(25, '2025-07-03', '23:28:02', 0.00, '2025-07-03', '23:29:59', 7600.00, 4000.00, 400.00, 4000.00, '', 1, 0),
(26, '2025-07-07', '21:16:12', 0.00, '2025-07-07', '21:17:06', 4000.00, 0.00, 0.00, 4000.00, '', 1, 0),
(27, '2025-07-14', '17:50:43', 0.00, '2025-07-14', '17:55:59', 7800.00, 4000.00, 200.00, 4000.00, '', 1, 0),
(28, '2025-07-14', '19:38:04', 0.00, '2025-07-14', '19:41:24', 6000.00, 4000.00, 2000.00, 4000.00, '', 1, 0),
(29, '2025-07-14', '19:50:03', 0.00, '2025-07-15', '20:15:30', 2000.00, 2000.00, 0.00, 0.00, '', 1, 0),
(30, '2025-07-21', '17:31:45', 0.00, '2025-07-21', '17:33:36', 3500.00, 2500.00, 3000.00, 4000.00, '', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_productos`
--

CREATE TABLE `categorias_productos` (
  `id` int(11) NOT NULL,
  `categoria` varchar(80) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_productos`
--

INSERT INTO `categorias_productos` (`id`, `categoria`, `estado`, `fecha`) VALUES
(1, 'Accesorio', 1, '2025-06-28 01:15:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `dni` varchar(10) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `codphone` varchar(10) DEFAULT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` text NOT NULL,
  `clave` varchar(150) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `dni`, `nombre`, `correo`, `codphone`, `telefono`, `direccion`, `clave`, `fecha`, `estado`) VALUES
(3, NULL, 'Roberlina Caminero', 'nayejudo@gmail.com', '+1', '8093924052', 'Jarabacoa', '72a1b923512d929a2d40eb3305ed4722bec7d04a26adfc0ba18aa5b020604a59', '2025-06-25 18:31:18', 1),
(4, '000000', 'Miguel Angel', NULL, NULL, '8294247992', 'Jarabacoa', NULL, '2025-06-29 22:17:30', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `ruc` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `logo` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `ruc`, `nombre`, `telefono`, `correo`, `direccion`, `mensaje`, `logo`) VALUES
(1, '40230832327', 'TEAM BLANQUITO', '8090000000', 'nayejudo@gmail.com', 'JARABACOA', 'GRACIAS POR SU PREFERENCIA', 'logo.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unit` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `cantidad`, `precio_unit`, `subtotal`, `id_producto`, `id_venta`) VALUES
(1, 1, 2000.00, 2000.00, 3, 22),
(2, 1, 2000.00, 2000.00, 3, 23),
(3, 1, 15.00, 15.00, 2, 23),
(4, 1, 15.00, 15.00, 2, 24),
(5, 1, 2000.00, 2000.00, 3, 25),
(6, 1, 2000.00, 2000.00, 3, 26),
(7, 1, 2000.00, 2000.00, 3, 27),
(8, 1, 2000.00, 2000.00, 3, 28),
(9, 1, 2000.00, 2000.00, 3, 29),
(10, 1, 2000.00, 2000.00, 3, 30),
(11, 2, 2000.00, 4000.00, 3, 31),
(12, 2, 2000.00, 4000.00, 3, 32),
(13, 2, 2000.00, 4000.00, 3, 33),
(14, 1, 2000.00, 2000.00, 3, 34),
(15, 1, 2500.00, 2500.00, 4, 35);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id` int(11) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id`, `documento`, `estado`, `fecha`) VALUES
(1, '40230832327', 1, '2025-06-25 18:58:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_caja` int(11) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id`, `descripcion`, `monto`, `fecha`, `id_usuario`, `id_caja`, `estado`) VALUES
(2, 'Compra de tornillos', 2000.00, '2025-07-02 19:27:00', 1, 10, 1),
(3, 'compra cable', 250.00, '2025-07-09 19:36:00', 1, 10, 1),
(4, 'Compra de tornillos', 2000.00, '2025-07-02 23:59:00', 1, 15, 1),
(5, 'compra cable', 255.00, '2025-07-03 00:19:00', 1, 16, 1),
(6, 'Compra de pieza', 2000.00, '2025-07-03 00:49:00', 1, 20, 1),
(7, 'Compra de tornillos', 250.00, '2025-07-03 07:52:00', 1, 21, 1),
(8, 'compra cable', 600.00, '2025-07-03 09:54:00', 1, 22, 1),
(9, 'Compra Tubos', 2250.00, '2025-07-03 18:18:00', 1, 23, 1),
(10, 'Compra de spray cadena', 950.00, '2025-07-03 18:18:00', 1, 23, 1),
(11, 'Compra de tornillos', 400.00, '2025-07-03 23:29:00', 1, 25, 1),
(12, 'Compra de tornillos', 200.00, '2025-07-14 17:55:00', 1, 27, 1),
(13, 'Compra de tornillos', 2000.00, '2025-07-14 19:40:00', 1, 28, 1),
(14, 'Compra de tornillos', 3000.00, '2025-07-21 17:33:00', 1, 30, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `marca`, `estado`, `fecha`) VALUES
(1, 'FERRARI', 1, '2021-11-11 18:45:57'),
(2, 'HONDA', 1, '2021-11-11 18:46:06'),
(3, 'NISAN', 1, '2021-11-11 18:46:19'),
(4, 'TOYOTA', 1, '2021-11-11 18:46:28'),
(5, 'VOLVO', 1, '2021-11-11 18:46:38'),
(6, 'SUZUKI', 1, '2021-11-11 18:46:53'),
(7, 'MORGAN', 1, '2021-11-11 18:47:22'),
(8, 'TESLA', 1, '2021-11-11 18:47:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(30) DEFAULT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo`, `nombre`, `descripcion`, `precio_compra`, `precio_venta`, `stock_actual`, `estado`, `fecha`, `id_categoria`) VALUES
(2, '0001', 'Gafasa', 'a', 12.00, 15.00, 0, 1, '2025-07-03 03:03:03', 1),
(3, '002', 'Bujia', 'Articulo', 1000.00, 2000.00, 3, 1, '2025-07-14 23:51:01', 1),
(4, 'LENTES', 'lentes', 'lentes', 2000.00, 2500.00, 11, 1, '2025-07-21 21:33:04', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `f_recogida` datetime NOT NULL,
  `f_entrega` datetime NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `tipo_precio` int(11) NOT NULL DEFAULT 1,
  `monto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `f_reserva` datetime NOT NULL,
  `observacion` text DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 0,
  `id_vehiculo` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `f_recogida`, `f_entrega`, `cantidad`, `tipo_precio`, `monto`, `f_reserva`, `observacion`, `estado`, `id_vehiculo`, `id_cliente`) VALUES
(1, '2025-06-03 21:00:00', '2025-06-03 22:00:00', 1, 1, 10000.00, '2025-06-25 23:41:08', '', 1, 2, 3),
(2, '2025-06-26 17:00:00', '2025-06-26 21:00:00', 4, 1, 2000.00, '2025-06-25 23:45:21', '', 1, 1, 3),
(3, '2025-06-28 17:00:00', '2025-06-28 21:00:00', 4, 1, 2000.00, '2025-06-25 23:49:08', '', 1, 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos`
--

CREATE TABLE `tipos` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos`
--

INSERT INTO `tipos` (`id`, `tipo`, `estado`, `fecha`) VALUES
(1, 'CAMIONETA', 1, '2021-11-11 14:44:50'),
(2, 'MINI BAN', 1, '2021-11-11 14:46:20'),
(3, 'MOTOCICLETA', 1, '2021-11-11 18:48:32'),
(4, 'MOTO CARRO', 1, '2021-11-11 18:48:55'),
(5, 'TURISMO', 1, '2021-11-11 18:49:13'),
(6, 'CAMION', 1, '2021-11-11 18:49:21'),
(7, 'Furgón', 1, '2021-11-11 18:49:54'),
(8, 'Mercedes Grand Sedan', 1, '2023-11-09 18:40:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `correo` varchar(80) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `perfil` varchar(50) NOT NULL DEFAULT 'avatar.svg',
  `clave` varchar(150) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `nombre`, `apellido`, `correo`, `telefono`, `direccion`, `perfil`, `clave`, `fecha`, `estado`) VALUES
(1, 'ADMIN', 'DAURY', 'GRULLON', 'nayejudo@gmail.com', '8097654323', 'JARABACOA', '20250625210046.jpg', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', '2025-06-25 19:00:46', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id` int(11) NOT NULL,
  `placa` varchar(50) NOT NULL,
  `precio_hora` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_dia` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_mes` decimal(10,2) NOT NULL DEFAULT 0.00,
  `modelo` varchar(50) NOT NULL,
  `kilometraje` varchar(50) NOT NULL,
  `transmision` varchar(50) NOT NULL,
  `asientos` int(11) NOT NULL DEFAULT 0,
  `equipaje` varchar(50) NOT NULL,
  `combustible` varchar(50) NOT NULL,
  `foto` varchar(50) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_tipo` int(11) NOT NULL,
  `id_marca` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `placa`, `precio_hora`, `precio_dia`, `precio_mes`, `modelo`, `kilometraje`, `transmision`, `asientos`, `equipaje`, `combustible`, `foto`, `estado`, `fecha`, `id_tipo`, `id_marca`) VALUES
(1, '00000', 2000.00, 20000.00, 800000.00, 'Maverick X3', '9000000', '11', 4, 'II', '800', '20250625205301.jpg', 1, '2025-06-25 18:53:01', 3, 2),
(2, '1111', 10000.00, 1000000.00, 1000000.00, 'PRUEBA', '2222', '2222', 2, '2', '2', '20250625205521.jpg', 1, '2025-06-25 18:55:21', 3, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `total_bruto` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `impuesto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_neto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(30) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_caja` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `total_bruto`, `descuento`, `impuesto`, `total_neto`, `metodo_pago`, `estado`, `id_cliente`, `id_usuario`, `id_caja`) VALUES
(19, '2025-06-28 18:24:00', 1000.00, 0.00, 0.00, 1000.00, 'Efectivo', 0, 3, 1, 1),
(20, '2025-06-28 18:44:00', 1000.00, 0.00, 0.00, 1000.00, 'Efectivo', 1, 3, 1, 4),
(21, '2025-06-28 19:01:00', 1000.00, 0.00, 0.00, 1000.00, 'Efectivo', 1, 3, 1, 5),
(22, '2025-07-02 20:04:00', 2000.00, 0.00, 0.00, 2000.00, 'Efectivo', 1, 4, 1, 9),
(23, '2025-07-03 05:01:00', 2015.00, 0.00, 0.00, 2015.00, 'Efectivo', 1, 4, 1, 10),
(24, '2025-07-03 05:01:00', 15.00, 0.00, 0.00, 15.00, 'Efectivo', 1, 3, 1, 10),
(25, '2025-07-03 05:08:00', 2000.00, 0.00, 0.00, 2000.00, 'Efectivo', 1, 3, 1, 10),
(26, '2025-07-03 06:18:00', 2000.00, 0.00, 0.00, 2000.00, 'Efectivo', 1, 3, 1, 16),
(27, '2025-07-03 06:49:00', 2000.00, 0.00, 0.00, 2000.00, 'Efectivo', 1, 3, 1, 20),
(28, '2025-07-03 13:52:00', 2000.00, 0.00, 0.00, 2000.00, 'Efectivo', 1, 4, 1, 21),
(29, '2025-07-03 15:53:00', 2000.00, 0.00, 0.00, 2000.00, 'Efectivo', 1, 4, 1, 22),
(30, '2025-07-04 00:15:00', 2000.00, 0.00, 0.00, 2000.00, 'Efectivo', 1, 3, 1, 23),
(31, '2025-07-04 05:29:00', 4000.00, 0.00, 0.00, 4000.00, 'Efectivo', 1, 4, 1, 25),
(32, '2025-07-14 23:55:00', 4000.00, 0.00, 0.00, 4000.00, 'Efectivo', 1, 3, 1, 27),
(33, '2025-07-15 01:39:00', 4000.00, 0.00, 0.00, 4000.00, 'Efectivo', 1, 4, 1, 28),
(34, '2025-07-15 01:50:00', 2000.00, 0.00, 0.00, 2000.00, 'Efectivo', 1, 4, 1, 29),
(35, '2025-07-21 23:32:00', 2500.00, 0.00, 0.00, 2500.00, 'Efectivo', 1, 3, 1, 30);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vehiculo` (`id_vehiculo`),
  ADD KEY `id_doc` (`id_doc`),
  ADD KEY `ventas_fk_caja` (`id_caja`),
  ADD KEY `alquiler_fk_caja` (`id_caja`);

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detventas_fk_producto` (`id_producto`),
  ADD KEY `detventas_fk_venta` (`id_venta`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gastos_fk_usuario` (`id_usuario`),
  ADD KEY `gastos_fk_caja` (`id_caja`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `productos_fk_categoria` (`id_categoria`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_vehiculo` (`id_vehiculo`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_marca` (`id_marca`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ventas_fk_cliente` (`id_cliente`),
  ADD KEY `ventas_fk_usuario` (`id_usuario`),
  ADD KEY `ventas_fk_caja` (`id_caja`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alquiler`
--
ALTER TABLE `alquiler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `categorias_productos`
--
ALTER TABLE `categorias_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alquiler`
--
ALTER TABLE `alquiler`
  ADD CONSTRAINT `alquiler_fk_caja` FOREIGN KEY (`id_caja`) REFERENCES `caja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `alquiler_ibfk_1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `alquiler_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `alquiler_ibfk_3` FOREIGN KEY (`id_doc`) REFERENCES `documentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detventas_fk_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `detventas_fk_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_fk_caja` FOREIGN KEY (`id_caja`) REFERENCES `caja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `gastos_fk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_productos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vehiculos_ibfk_2` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_fk_caja` FOREIGN KEY (`id_caja`) REFERENCES `caja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_fk_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_fk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
