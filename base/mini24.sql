--
-- Base de datos: `mini24`
--
CREATE DATABASE IF NOT EXISTS `mini24` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `mini24`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
    `codigo` int(11) NOT NULL,
    `descripcion` varchar(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO
    `categorias` (`codigo`, `descripcion`)
VALUES (1, 'Microprocesadores'),
    (2, 'Discos Sólidos SSD'),
    (3, 'Tarjetas gráficas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
    `codigo` int(11) NOT NULL,
    `nombre` varchar(50) NOT NULL,
    `telefono` varchar(20) NOT NULL,
    `email` varchar(70) DEFAULT NULL,
    `direccion` varchar(70) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO
    `clientes` (
        `codigo`,
        `nombre`,
        `telefono`,
        `email`,
        `direccion`
    )
VALUES (
        1,
        'Diego Martinez',
        '2356789',
        'diegomartinez@gmail.com',
        'Colon 3455'
    ),
    (
        2,
        'Ana Paula',
        '6434566',
        'anapaula@gmail.com',
        'Bernardino Caballero 345'
    ),
    (
        3,
        'Marcos Rodriguez',
        '5984384',
        'marcosrodriguez@gmail.com',
        '23 de septiembre casi Nanawa'
    );

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
    `codigo` int(11) NOT NULL,
    `codigofactura` int(11) NOT NULL,
    `codigoproducto` int(11) NOT NULL,
    `precio` float NOT NULL,
    `cantidad` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
    `codigo` int(11) NOT NULL,
    `fecha` date DEFAULT NULL,
    `codigocliente` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
    `codigo` int(11) NOT NULL,
    `descripcion` varchar(50) NOT NULL,
    `precio` float NOT NULL,
    `codigocategoria` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO
    `productos` (
        `codigo`,
        `descripcion`,
        `precio`,
        `codigocategoria`
    )
VALUES (
        1,
        'Intel Core i5-10400F 2.9 GHz LGA 1200',
        979000,
        1
    ),
    (
        2,
        'Intel Core I7-10700F 2.9 GHz LGA 1200',
        1819870,
        1
    ),
    (
        3,
        'SSD 2.5 Crucial 540/500 MB/s 240 GB',
        150000,
        2
    ),
    (
        4,
        'SSD 2.5 Kingston A400 SATA 500/450 MB/s 480GB',
        283000,
        2
    ),
    (
        5,
        'MSI GTX 1650 4GB GDDR5 Ventus XS OC',
        1500000,
        3
    ),
    (
        6,
        'Geforce RTX 3060 GDDR6 XDS-X12 3584SP',
        2766820,
        3
    );

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias` ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes` ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
ADD PRIMARY KEY (`codigo`),
ADD KEY `codigoproducto` (`codigoproducto`),
ADD KEY `detallefactura_ibfk_1` (`codigofactura`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
ADD PRIMARY KEY (`codigo`),
ADD KEY `codigocliente` (`codigocliente`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
ADD PRIMARY KEY (`codigo`),
ADD KEY `codigocategoria` (`codigocategoria`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
ADD CONSTRAINT `detallefactura_ibfk_1` FOREIGN KEY (`codigofactura`) REFERENCES `facturas` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `detallefactura_ibfk_2` FOREIGN KEY (`codigoproducto`) REFERENCES `productos` (`codigo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`codigocliente`) REFERENCES `clientes` (`codigo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`codigocategoria`) REFERENCES `categorias` (`codigo`) ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;