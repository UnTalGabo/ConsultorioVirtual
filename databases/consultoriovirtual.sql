-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-06-2025 a las 20:18:33
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
-- Base de datos: `consultoriovirtual`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `antecedentes_gineco_obstetricos`
--

CREATE TABLE `antecedentes_gineco_obstetricos` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `edad_inicio_regla` int(11) DEFAULT NULL,
  `ritmo_ciclo_menstrual` int(11) DEFAULT NULL,
  `fecha_ultima_menstruacion` date DEFAULT NULL,
  `numero_gestas` int(11) DEFAULT 0,
  `numero_partos` int(11) DEFAULT 0,
  `numero_abortos` int(11) DEFAULT 0,
  `numero_cesareas` int(11) DEFAULT 0,
  `fecha_ultimo_embarazo` date DEFAULT NULL,
  `complicaciones_menstruacion` text DEFAULT NULL,
  `fecha_ultima_citologia` date DEFAULT NULL,
  `mastografia` tinyint(1) DEFAULT 0,
  `fecha_ultima_mastografia` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `antecedentes_laborales`
--

CREATE TABLE `antecedentes_laborales` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `edad_inicio_trabajo` int(11) DEFAULT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `antiguedad` varchar(50) DEFAULT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `polvo` tinyint(1) DEFAULT 0,
  `ruido` tinyint(1) DEFAULT 0,
  `humo` tinyint(1) DEFAULT 0,
  `radiacion` tinyint(1) DEFAULT 0,
  `quimicos_solventes` tinyint(1) DEFAULT 0,
  `calor_frio` tinyint(1) DEFAULT 0,
  `vibracion` tinyint(1) DEFAULT 0,
  `movimiento_repetitivo` tinyint(1) DEFAULT 0,
  `cargas` tinyint(1) DEFAULT 0,
  `riesgos_psicosociales` tinyint(1) DEFAULT 0,
  `equipo_proteccion` text DEFAULT NULL,
  `accidentes` tinyint(1) DEFAULT 0,
  `fecha_accidente` date DEFAULT NULL,
  `lesion` text DEFAULT NULL,
  `pagos_accidente` tinyint(1) DEFAULT 0,
  `pagado_por` varchar(10) DEFAULT NULL,
  `secuelas` tinyint(1) DEFAULT 0,
  `fecha_secuela` date DEFAULT NULL,
  `secuela` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `antecedentes_no_patologicos`
--

CREATE TABLE `antecedentes_no_patologicos` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `fuma` tinyint(1) DEFAULT 0,
  `cigarros_dia` int(11) DEFAULT NULL,
  `anos_fumando` int(11) DEFAULT NULL,
  `bebe` tinyint(1) DEFAULT 0,
  `anos_bebiendo` int(11) DEFAULT NULL,
  `frecuencia_alcohol` varchar(20) DEFAULT NULL,
  `medicamentos_controlados` tinyint(1) DEFAULT 0,
  `nombre_medicamento_controlado` text DEFAULT NULL,
  `desde_cuando_medicamento_controlado` date DEFAULT NULL,
  `usa_drogas` tinyint(1) DEFAULT 0,
  `tipo_droga` varchar(100) DEFAULT NULL,
  `practica_deporte` tinyint(1) DEFAULT 0,
  `tipo_deporte` varchar(100) DEFAULT NULL,
  `frecuencia_deporte` varchar(20) DEFAULT NULL,
  `tatuajes` tinyint(1) DEFAULT 0,
  `cantidad_tatuajes` int(11) DEFAULT NULL,
  `ubicacion_tatuajes` text DEFAULT NULL,
  `transfusiones` tinyint(1) DEFAULT 0,
  `transfusiones_recibidas` tinyint(1) DEFAULT 0,
  `fobias` tinyint(1) NOT NULL,
  `cual_fobia` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `antecedentes_patologicos`
--

CREATE TABLE `antecedentes_patologicos` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `fracturas_esguinces` text DEFAULT NULL,
  `cirugias` text DEFAULT NULL,
  `enfermedad_actual_desc` text DEFAULT NULL,
  `medicamentos` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `id_consulta` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `imc` decimal(5,2) DEFAULT NULL,
  `fc` varchar(10) DEFAULT NULL,
  `fr` varchar(10) DEFAULT NULL,
  `temp` decimal(4,2) DEFAULT NULL,
  `perimetro_abdominal` decimal(5,2) DEFAULT NULL,
  `presion_arterial` varchar(15) DEFAULT NULL,
  `spo2` varchar(10) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `evaluacion_fisica` text NOT NULL,
  `botiquin` varchar(100) DEFAULT NULL,
  `destino` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `pdf` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enfermedades_heredo`
--

CREATE TABLE `enfermedades_heredo` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `enfermedad` varchar(50) NOT NULL,
  `parentesco` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enfermedades_patologicas`
--

CREATE TABLE `enfermedades_patologicas` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `enfermedad` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `examenes_medicos`
--

CREATE TABLE `examenes_medicos` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `imc` decimal(5,2) DEFAULT NULL,
  `fc` int(11) DEFAULT NULL,
  `fr` int(11) DEFAULT NULL,
  `temp` decimal(3,1) DEFAULT NULL,
  `perimetro_abdominal` int(11) DEFAULT NULL,
  `presion_arterial` varchar(10) DEFAULT NULL,
  `spo2` int(11) DEFAULT NULL,
  `cabeza` text DEFAULT NULL,
  `columna_vertebral` text DEFAULT NULL,
  `oido` text DEFAULT NULL,
  `extremidades_superiores` text DEFAULT NULL,
  `cavidad_oral` text DEFAULT NULL,
  `extremidades_inferiores` text DEFAULT NULL,
  `cuello` text DEFAULT NULL,
  `torax` text DEFAULT NULL,
  `abdomen` text DEFAULT NULL,
  `resultado` varchar(50) DEFAULT NULL,
  `recomendaciones` text DEFAULT NULL,
  `confirmacion_paciente` tinyint(1) DEFAULT 0,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id_empleado` int(11) NOT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` varchar(20) DEFAULT NULL,
  `estado_civil` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `escolaridad` varchar(50) DEFAULT NULL,
  `puesto` varchar(50) DEFAULT NULL,
  `contacto_emergencia` varchar(100) DEFAULT NULL,
  `telefono_emergencia` varchar(10) DEFAULT NULL,
  `parentesco` varchar(50) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `departamento` varchar(50) DEFAULT NULL,
  `acepta_terminos` tinyint(1) DEFAULT 0,
  `calle` varchar(50) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `colonia` varchar(50) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `tipo_sangre` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pdf`
--

CREATE TABLE `pdf` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `tipo_pdf` varchar(50) NOT NULL,
  `ruta_pdf` varchar(255) NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('doctor','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `rol`) VALUES
(1, 'admin', '$2y$10$d7lBOsOIYKlNwZNdA0Hye.SO8dc0AJKfCwaMGpSYY2BTUw.WguEPa', 'admin'),
(2, 'doctor', '$2y$10$6gtXX7Wh4Q8cv0HSllCH0OtLvqzAK.fBipFwvV6v6pIVhKplvXLry', 'doctor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacunas`
--

CREATE TABLE `vacunas` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `covid` tinyint(1) DEFAULT 0,
  `covid_penultima` date DEFAULT NULL,
  `covid_ultima` date DEFAULT NULL,
  `influenza` tinyint(1) DEFAULT 0,
  `influenza_penultima` date DEFAULT NULL,
  `influenza_ultima` date DEFAULT NULL,
  `sarampion` tinyint(1) DEFAULT 0,
  `sarampion_1` date DEFAULT NULL,
  `sarampion_2` date DEFAULT NULL,
  `tetanos` tinyint(1) DEFAULT 0,
  `tetanos_1` date DEFAULT NULL,
  `tetanos_2` date DEFAULT NULL,
  `tetanos_3` date DEFAULT NULL,
  `tetanos_refuerzo` date DEFAULT NULL,
  `varicela` tinyint(1) DEFAULT 0,
  `varicela_1` date DEFAULT NULL,
  `varicela_2` date DEFAULT NULL,
  `herpes` tinyint(1) DEFAULT 0,
  `herpes_1` date DEFAULT NULL,
  `herpes_2` date DEFAULT NULL,
  `vph` tinyint(1) DEFAULT 0,
  `vph_1` date DEFAULT NULL,
  `vph_2` date DEFAULT NULL,
  `vph_3` date DEFAULT NULL,
  `hepatitis_a` tinyint(1) DEFAULT 0,
  `hepatitis_a_1` date DEFAULT NULL,
  `hepatitis_a_2` date DEFAULT NULL,
  `hepatitis_b` tinyint(1) DEFAULT 0,
  `hepatitis_b_1` date DEFAULT NULL,
  `hepatitis_b_2` date DEFAULT NULL,
  `hepatitis_b_3` date DEFAULT NULL,
  `neumococo` tinyint(1) DEFAULT 0,
  `neumococo_penultima` date DEFAULT NULL,
  `neumococo_ultima` date DEFAULT NULL,
  `meningococo` tinyint(1) DEFAULT 0,
  `meningococo_1` date DEFAULT NULL,
  `rabia` tinyint(1) DEFAULT 0,
  `rabia_1` date DEFAULT NULL,
  `fiebre_amarilla` tinyint(1) DEFAULT 0,
  `fiebre_amarilla_1` date DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `antecedentes_gineco_obstetricos`
--
ALTER TABLE `antecedentes_gineco_obstetricos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paciente` (`id_empleado`);

--
-- Indices de la tabla `antecedentes_laborales`
--
ALTER TABLE `antecedentes_laborales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `antecedentes_no_patologicos`
--
ALTER TABLE `antecedentes_no_patologicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paciente` (`id_empleado`);

--
-- Indices de la tabla `antecedentes_patologicos`
--
ALTER TABLE `antecedentes_patologicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paciente` (`id_empleado`);

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id_consulta`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `fk_consultas_pdf` (`pdf`);

--
-- Indices de la tabla `enfermedades_heredo`
--
ALTER TABLE `enfermedades_heredo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enfermedades_heredo_ibfk_1` (`id_empleado`);

--
-- Indices de la tabla `enfermedades_patologicas`
--
ALTER TABLE `enfermedades_patologicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paciente` (`id_empleado`);

--
-- Indices de la tabla `examenes_medicos`
--
ALTER TABLE `examenes_medicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id_empleado`);

--
-- Indices de la tabla `pdf`
--
ALTER TABLE `pdf`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ruta_pdf` (`ruta_pdf`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vacunas_ibfk_1` (`id_empleado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `antecedentes_gineco_obstetricos`
--
ALTER TABLE `antecedentes_gineco_obstetricos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `antecedentes_laborales`
--
ALTER TABLE `antecedentes_laborales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `antecedentes_no_patologicos`
--
ALTER TABLE `antecedentes_no_patologicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `antecedentes_patologicos`
--
ALTER TABLE `antecedentes_patologicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id_consulta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `enfermedades_heredo`
--
ALTER TABLE `enfermedades_heredo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `enfermedades_patologicas`
--
ALTER TABLE `enfermedades_patologicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `examenes_medicos`
--
ALTER TABLE `examenes_medicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pdf`
--
ALTER TABLE `pdf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `antecedentes_gineco_obstetricos`
--
ALTER TABLE `antecedentes_gineco_obstetricos`
  ADD CONSTRAINT `antecedentes_gineco_obstetricos_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `antecedentes_laborales`
--
ALTER TABLE `antecedentes_laborales`
  ADD CONSTRAINT `antecedentes_laborales_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `antecedentes_no_patologicos`
--
ALTER TABLE `antecedentes_no_patologicos`
  ADD CONSTRAINT `antecedentes_no_patologicos_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `antecedentes_patologicos`
--
ALTER TABLE `antecedentes_patologicos`
  ADD CONSTRAINT `antecedentes_patologicos_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `consultas_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`),
  ADD CONSTRAINT `fk_consultas_pdf` FOREIGN KEY (`pdf`) REFERENCES `pdf` (`id`);

--
-- Filtros para la tabla `enfermedades_heredo`
--
ALTER TABLE `enfermedades_heredo`
  ADD CONSTRAINT `enfermedades_heredo_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `enfermedades_patologicas`
--
ALTER TABLE `enfermedades_patologicas`
  ADD CONSTRAINT `enfermedades_patologicas_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `examenes_medicos`
--
ALTER TABLE `examenes_medicos`
  ADD CONSTRAINT `examenes_medicos_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pdf`
--
ALTER TABLE `pdf`
  ADD CONSTRAINT `pdf_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`);

--
-- Filtros para la tabla `vacunas`
--
ALTER TABLE `vacunas`
  ADD CONSTRAINT `vacunas_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `pacientes` (`id_empleado`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
