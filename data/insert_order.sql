INSERT INTO `ciudades` (`idCiudad`, `nombre`) VALUES (1, 'CAAGUAZU');

INSERT INTO `cursos` (`idCurso`, `nombre`, `importe`) VALUES
(1, 'FRONTEND HTML5 BOOSTRAP 5', 350000),
(2, 'BACKEND PHP 8 MYSQL 8', 550000),
(3, 'MODELADO DE SOFTWARE POO CON UML', 250000);

INSERT INTO `roles` (`idRol`, `nombre`) VALUES (1, 'Administrador');

INSERT INTO `formapagos` (`idFormaPago`, `descripcion`) VALUES
(1, 'EFECTIVO'),
(3, 'TARJETA'),
(2, 'TRANSFERENCIA');

INSERT INTO `estudiantes` (`idEstudiante`, `nombre`, `apellido`, `idCiudad`, `cin`) VALUES
(1, 'JUAN', 'GONZALEZ', 1, 5554447),
(2, 'JOSE', 'AYALA', 1, 8887773);

INSERT INTO `usuarios` (`idUsuario`, `alias`, `clave`, `idRol`) VALUES
(1, 'admin', '$2y$10$KhjbF5ve6XXWmY1ZAL.Vu.AsrVt6jvP8WMWSkPWwDHN9UIDkVHeCi', 1);

INSERT INTO `facturas` (`numero`, `fecha`, `idEstudiante`, `idFormaPago`, `anulada`, `idUsuario`) VALUES
(100, '2024-11-06', 1, 1, 0, 1),
(101, '2024-11-06', 2, 2, 0, 1),
(102, '2024-11-06', 1, 1, 0, 1);

INSERT INTO `matriculas` (`idMatricula`, `fecha`, `idEstudiante`, `idUsuario`, `idCurso`) VALUES
(1, '2024-11-05', 1, 1, 1),
(2, '2024-11-03', 2, 1, 2);

INSERT INTO `detallefacturas` (`numero`, `idCurso`, `cantidad`, `importe`) VALUES
(100, 3, 1, 250000),
(101, 2, 1, 550000),
(102, 1, 1, 350000),
(102, 3, 1, 250000);
