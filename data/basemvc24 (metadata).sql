--
-- Base de datos: `basemvc24`
--
CREATE DATABASE IF NOT EXISTS `basemvc24` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `basemvc24`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
    `idCiudad` int(11) NOT NULL,
    `nombre` varchar(50) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
    `idCurso` int(11) NOT NULL,
    `nombre` varchar(50) NOT NULL,
    `importe` double NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefacturas`
--

CREATE TABLE `detallefacturas` (
    `numero` int(11) NOT NULL,
    `idCurso` int(11) NOT NULL,
    `cantidad` int(11) NOT NULL,
    `importe` double NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
    `idEstudiante` int(11) NOT NULL,
    `nombre` varchar(35) NOT NULL,
    `apellido` varchar(45) NOT NULL,
    `idCiudad` int(11) NOT NULL,
    `cin` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
    `numero` int(11) NOT NULL,
    `fecha` date NOT NULL,
    `idEstudiante` int(11) NOT NULL,
    `idFormaPago` int(11) NOT NULL,
    `anulada` smallint(6) NOT NULL,
    `idUsuario` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formapagos`
--

CREATE TABLE `formapagos` (
    `idFormaPago` int(11) NOT NULL,
    `descripcion` varchar(45) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

CREATE TABLE `matriculas` (
    `idMatricula` int(11) NOT NULL,
    `fecha` date NOT NULL,
    `idEstudiante` int(11) NOT NULL,
    `idUsuario` int(11) NOT NULL,
    `idCurso` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
    `idRol` int(11) NOT NULL,
    `nombre` varchar(45) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
    `idUsuario` int(11) NOT NULL,
    `alias` varchar(65) NOT NULL,
    `clave` varchar(255) NOT NULL,
    `idRol` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
ADD PRIMARY KEY (`idCiudad`),
ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
ADD PRIMARY KEY (`idCurso`),
ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `detallefacturas`
--
ALTER TABLE `detallefacturas`
ADD PRIMARY KEY (`numero`, `idCurso`),
ADD KEY `detallefacturas_ibfk_2` (`idCurso`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
ADD UNIQUE KEY `idEstudiante` (`idEstudiante`),
ADD UNIQUE KEY `cin` (`cin`),
ADD KEY `idCiudad` (`idCiudad`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
ADD PRIMARY KEY (`numero`),
ADD KEY `idUsuario` (`idUsuario`),
ADD KEY `facturas_ibfk_1` (`idEstudiante`),
ADD KEY `facturas_ibfk_2` (`idFormaPago`);

--
-- Indices de la tabla `formapagos`
--
ALTER TABLE `formapagos`
ADD PRIMARY KEY (`idFormaPago`),
ADD UNIQUE KEY `descripcion` (`descripcion`);

--
-- Indices de la tabla `matriculas`
--
ALTER TABLE `matriculas`
ADD PRIMARY KEY (`idMatricula`),
ADD KEY `idEstudiante` (`idEstudiante`),
ADD KEY `idUsuario` (`idUsuario`),
ADD KEY `idCurso` (`idCurso`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
ADD PRIMARY KEY (`idRol`),
ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
ADD PRIMARY KEY (`idUsuario`),
ADD UNIQUE KEY `alias` (`alias`),
ADD KEY `idRol` (`idRol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
MODIFY `idCiudad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
MODIFY `idCurso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
MODIFY `idEstudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
MODIFY `numero` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `formapagos`
--
ALTER TABLE `formapagos`
MODIFY `idFormaPago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `matriculas`
--
ALTER TABLE `matriculas`
MODIFY `idMatricula` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles` MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detallefacturas`
--
ALTER TABLE `detallefacturas`
ADD CONSTRAINT `detallefacturas_ibfk_1` FOREIGN KEY (`numero`) REFERENCES `facturas` (`numero`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `detallefacturas_ibfk_2` FOREIGN KEY (`idCurso`) REFERENCES `cursos` (`idCurso`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
ADD CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`idCiudad`) REFERENCES `ciudades` (`idCiudad`);

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`) ON UPDATE CASCADE,
ADD CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`idFormaPago`) REFERENCES `formapagos` (`idFormaPago`) ON UPDATE CASCADE,
ADD CONSTRAINT `facturas_ibfk_3` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `matriculas`
--
ALTER TABLE `matriculas`
ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`idEstudiante`) REFERENCES `estudiantes` (`idEstudiante`),
ADD CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`),
ADD CONSTRAINT `matriculas_ibfk_3` FOREIGN KEY (`idCurso`) REFERENCES `cursos` (`idCurso`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`);

COMMIT;