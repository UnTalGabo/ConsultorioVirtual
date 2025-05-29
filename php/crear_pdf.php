<?php
require_once '../vendor/autoload.php';
require 'conexion.php'; // tu archivo de conexión

use setasign\Fpdi\Fpdi;

$id_empleado = $_GET['id'] ?? null;

if (!$id_empleado) {
    die("ID de empleado no proporcionado.");
}
function obtenerDatos($tabla, $id_empleado)
{
    global $conn; // Asegúrate de que la conexión esté disponible
    $query = "SELECT * FROM $tabla WHERE id_empleado = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_empleado);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
    $stmt->close();
}
function obtenerFecha($fechas)
{
    if ($fechas) {
        $fecha = new DateTime($fechas);
        return $fecha->format('d/m/Y');
    } else {
        return '';
    }
}
function obtenerHora($fechas)
{
    if ($fechas) {
        $fecha = new DateTime($fechas);
        return $fecha->format('H:i');
    } else {
        return '';
    }
}
function obtenerEdad($fecha_nacimiento)
{
    if ($fecha_nacimiento) {
        $fecha = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha);
        return $edad->y; // Devuelve la edad en años
    } else {
        return '';
    }
}
function getChecked($efnfermedad)
{
    global $enfermedadesH;
    foreach ($enfermedadesH as $enfermedad) {
        if ($enfermedad['enfermedad'] == $efnfermedad) {
            return 'X';
        }
    }
    return '';
}
function obtenerQuien($enfermedad)
{
    global $enfermedadesH;
    foreach ($enfermedadesH as $enfermedadH) {
        if ($enfermedadH['enfermedad'] == $enfermedad) {
            return $enfermedadH['parentesco'];
        }
    }
    return '';
}

// Consulta de datos

$paciente = obtenerDatos('pacientes', $id_empleado);

$sql = "SELECT enfermedad, parentesco FROM enfermedades_heredo WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();
$enfermedadesH = [];
while ($row = $resultado->fetch_assoc()) {
    $enfermedadesH[] = $row;
}
$stmt->close();

$antecedentesNoPatologicos = obtenerDatos('antecedentes_no_patologicos', $id_empleado);
if ($paciente['genero'] === 'Femenino') {
    $antecedentesGineco = obtenerDatos('antecedentes_gineco_obstetricos', $id_empleado);
} else {
    $antecedentesGineco = null; // No aplica para hombres
}
$antecedentesPatologicos = obtenerDatos('antecedentes_patologicos', $id_empleado);

$sql = "SELECT enfermedad FROM enfermedades_patologicas WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();
$enfermedadesP = [];
while ($row = $resultado->fetch_assoc()) {
    $enfermedadesP[] = $row;
}
$stmt->close();

$antecedentesLaborales = obtenerDatos('antecedentes_laborales', $id_empleado);
$examenMedico = obtenerDatos('examenes_medicos', $id_empleado);

//-------------------------------------------------------------------------------------------------------------------------------------------------------------
if (!$paciente) {
    die("Paciente no encontrado.");
} elseif (!$antecedentesNoPatologicos) {
    die("Antecedentes no patológicos no encontrados.");
} elseif ($paciente['genero'] === 'Femenino' && !$antecedentesGineco) {
    die("Antecedentes gineco-obstétricos no encontrados.");
} elseif (!$antecedentesPatologicos) {
    die("Antecedentes patológicos no encontrados.");
} elseif (!$antecedentesLaborales) {
    die("Antecedentes laborales no encontrados.");
} elseif (!$examenMedico) {
    die("Examen médico no encontrado.");
}

// Crear PDF
$pdf = new Fpdi();

// Estilo del texto
$pdf->SetFont('Helvetica');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFontSize(9);


$pdf->AddPage();
$pdf->setSourceFile('../media/formato.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx);

// Insertar texto por coordenadas (X, Y)
$pdf->SetXY(35, 49.5);
$pdf->Write(0, utf8_decode($paciente['nombre_completo']));

$pdf->SetXY(140, 26.5);
$pdf->Write(0, obtenerFecha($examenMedico['fecha_actualizacion']));

$pdf->SetXY(140, 31);
$pdf->Write(0, obtenerHora($examenMedico['fecha_actualizacion']));

$pdf->SetXY(35, 59);
$pdf->Write(0, utf8_decode(obtenerEdad($paciente['fecha_nacimiento']) . ' años'));

if ($paciente['genero'] === 'Masculino') {
    $pdf->SetXY(107.5, 59);
    $pdf->Write(0, ('X'));
} else {
    $pdf->SetXY(154.6, 59);
    $pdf->Write(0, ('X'));
}

$pdf->SetXY(50, 63.5);
$pdf->Write(0, obtenerFecha($paciente['fecha_nacimiento']));

$pdf->SetXY(116, 63.5);
$pdf->Write(0, $paciente['telefono']);

$pdf->SetXY(160, 63);
$pdf->Write(0, $paciente['estado_civil']);

$pdf->SetXY(32, 68);
$pdf->Write(0, utf8_decode($paciente['calle'] . ' ' . $paciente['numero'] . ',    ' . $paciente['colonia']));

$pdf->SetFontSize(7.5);
$pdf->SetXY(54, 73);
$pdf->Write(0, utf8_decode($paciente['contacto_emergencia']));
$pdf->SetFontSize(9);

$pdf->SetXY(138, 73);
$pdf->Write(0, utf8_decode($paciente['parentesco'] . '        ' . $paciente['telefono_emergencia']));

$pdf->SetXY(35, 77.5);
$pdf->Write(0, utf8_decode($paciente['puesto']));

$pdf->SetXY(127, 77.5);
$pdf->Write(0, utf8_decode($paciente['area']));

//enfermedades heredo familiares

// Función para escribir check y parentesco en el PDF
function escribirEnfermedad($pdf, $xCheck, $yCheck, $xPar, $yPar, $nombre)
{
    $pdf->SetXY($xCheck, $yCheck);
    $pdf->Write(0, getChecked($nombre));
    $pdf->SetXY($xPar, $yPar);
    $pdf->Write(0, obtenerQuien($nombre));
}

// Array de enfermedades con coordenadas
$enfermedades = [
    // Columna 1
    [42, 155.3, 62, 155.3, 'Presión alta/baja'],
    [42, 160.3, 62, 160.3, 'Vértigos'],
    [42, 165.3, 62, 165.3, 'Diabetes'],
    [42, 169.8, 62, 169.8, 'Enfermedades del Corazón'],
    [42, 174.6, 62, 174.6, 'Enfermedades Pulmonares'],
    [42, 181.3, 62, 181.3, 'Enfermedades del Riñon'],
    [42, 187.8, 62, 187.8, 'Enfermedades del Higado'],
    [42, 192.5, 62, 192.5, 'Alergias'],
    // Columna 2
    [107.6, 155.3, 123, 155.3, 'Tumores o cáncer'],
    [107.6, 160, 123, 160, 'Asma bronquial'],
    [107.6, 165, 123, 165, 'Gastritis/Ulcera'],
    [107.6, 170, 123, 170, 'Flebitis/Várices'],
    [107.6, 174.6, 123, 174.6, 'Artritis'],
    [107.6, 181, 123, 183, 'Alteraciones del sueño'],
    [107.6, 187.5, 123, 188, 'Acufeno/Tinitus'],
    // Columna 3
    [180, 155.3, 195, 155.3, 'Problemas de espalda'],
    [180, 160, 195, 160, 'Sensación de hormigueo'],
    [180, 165, 195, 165, 'Convulsiones'],
    [180, 170, 195, 170, 'Debilidad Muscular'],
    [180, 174.6, 195, 174.6, 'Osteoporosis'],
    [180, 181, 195, 183, 'Hernias'],
    [180, 187.5, 195, 188, 'COVID 19'],
];

// Escribir todas las enfermedades con un solo ciclo
foreach ($enfermedades as $e) {
    escribirEnfermedad($pdf, $e[0], $e[1], $e[2], $e[3], $e[4]);
}


// Antecedentes no patológicos

$pdf->SetXY(25, 201.5);
$pdf->Write(0, utf8_decode($paciente['escolaridad']));

$pdf->SetXY(62.5, 206.5);
$pdf->Write(0, $antecedentesNoPatologicos['fuma'] ? 'X' : '');
if ($antecedentesNoPatologicos['fuma']) {
    $pdf->SetXY(136.5, 206.5);
    $pdf->Write(0, $antecedentesNoPatologicos['cigarros_dia'] . ' cigarros');
    $pdf->SetXY(180, 206.5);
    $pdf->Write(0, utf8_decode($antecedentesNoPatologicos['anos_fumando'] . ' años'));
} else {
    $pdf->SetXY(107.5, 206.5);
    $pdf->Write(0, '');
}
$pdf->SetXY(107.5, 206.5);
$pdf->Write(0, $antecedentesNoPatologicos['fuma'] ? '' : 'X');

$pdf->SetXY(62.5, 211.2);
$pdf->Write(0, $antecedentesNoPatologicos['bebe'] ? 'X' : '');
if ($antecedentesNoPatologicos['bebe']) {
    $pdf->SetXY(133.5, 211.2);
    $pdf->Write(0, utf8_decode($antecedentesNoPatologicos['frecuencia_alcohol']));
    $pdf->SetXY(180, 211.2);
    $pdf->Write(0, utf8_decode($antecedentesNoPatologicos['anos_bebiendo'] . ' años'));
} else {
    $pdf->SetXY(107.5, 211.2);
    $pdf->Write(0, '');
}
$pdf->SetXY(107.5, 211.2);
$pdf->Write(0, $antecedentesNoPatologicos['bebe'] ? '' : 'X');


$pdf->SetXY(124.5, 216);
$pdf->Write(0, $antecedentesNoPatologicos['medicamentos_controlados'] ? 'X' : '');
$pdf->SetXY(180, 216);
$pdf->Write(0, $antecedentesNoPatologicos['medicamentos_controlados'] ? '' : 'X');

$pdf->SetXY(87.5, 221);
$pdf->Write(0, $antecedentesNoPatologicos['usa_drogas'] ? 'X' : '');
$pdf->SetXY(124.5, 221);
$pdf->Write(0, $antecedentesNoPatologicos['usa_drogas'] ? '' : 'X');

$pdf->SetXY(160, 221);
if ($antecedentesNoPatologicos['usa_drogas']) {
    $pdf->Write(0, utf8_decode($antecedentesNoPatologicos['tipo_droga']));
} else {
    $pdf->Write(0, '');
}


$pdf->SetXY(87.5, 225.8);
$pdf->Write(0, $antecedentesNoPatologicos['practica_deporte'] ? 'X' : '');
$pdf->SetXY(124.5, 225.8);
$pdf->Write(0, $antecedentesNoPatologicos['practica_deporte'] ? '' : 'X');

$pdf->SetXY(150, 225.8);
if ($antecedentesNoPatologicos['practica_deporte']) {
    $pdf->Write(0, utf8_decode($antecedentesNoPatologicos['tipo_deporte']));
} else {
    $pdf->Write(0, '');
}


$pdf->SetXY(87.5, 230.5);
$pdf->Write(0, $antecedentesNoPatologicos['tatuajes'] ? 'X' : '');
$pdf->SetXY(124.5, 230.5);
$pdf->Write(0, $antecedentesNoPatologicos['tatuajes'] ? '' : 'X');


$pdf->SetXY(87.5, 235.3);
$pdf->Write(0, $antecedentesNoPatologicos['transfusiones'] ? 'X' : '');
$pdf->SetXY(124.5, 235.3);
$pdf->Write(0, $antecedentesNoPatologicos['transfusiones'] ? '' : 'X');

$pdf->SetXY(180, 235.3);
$pdf->Write(0, $antecedentesNoPatologicos['transfusiones_recibidas'] ? 'Si' : 'No');


$pdf->SetXY(87.5, 240);
$pdf->Write(0, $antecedentesNoPatologicos['fobias'] ? 'X' : '');
$pdf->SetXY(124.5, 240);
$pdf->Write(0, $antecedentesNoPatologicos['fobias'] ? '' : 'X');


$pdf->AddPage();
$tplIdx = $pdf->importPage(2);
$pdf->useTemplate($tplIdx);

if ($paciente['genero'] == 'Femenino') {
    $pdf->SetXY(60, 26);
    $pdf->write(0, $antecedentesGineco['edad_inicio_regla']);

    $pdf->SetXY(115, 26);
    $pdf->Write(0, utf8_decode($antecedentesGineco['ritmo_ciclo_menstrual'] . ' días'));

    $pdf->SetXY(150, 26);
    $pdf->Write(0, obtenerFecha($antecedentesGineco['fecha_ultima_menstruacion']));
}

// Salida
$pdf->Output('I', 'historia_clinica.pdf');

$conn->close(); // Cerrar la conexión a la base de datos