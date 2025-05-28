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
$pdf->Write(0, utf8_decode($paciente['parentesco'] . '   ' . $paciente['telefono_emergencia']));

$pdf->SetXY(19, 77.5);
$pdf->Write(0, utf8_decode($paciente['puesto']));

$pdf->SetXY(127, 77.5);
$pdf->Write(0, utf8_decode($paciente['area']));

//enfermedades heredo familiares

$pdf->SetXY(42, 155.3);
$pdf->Write(0, getChecked('Presión alta/baja'));
$pdf->SetXY(62, 155.3);
$pdf->Write(0, obtenerQuien('Presión alta/baja'));

$pdf->SetXY(42, 160.3);
$pdf->Write(0, getChecked('Vértigos'));
$pdf->SetXY(62, 160.3);
$pdf->Write(0, obtenerQuien('Vértigos'));

$pdf->SetXY(42, 165.3);
$pdf->Write(0, getChecked('Diabetes'));
$pdf->SetXY(62, 165.3);
$pdf->Write(0, obtenerQuien('Diabetes'));

$pdf->SetXY(42, 169.8);
$pdf->Write(0, getChecked('Enfermedades del Corazón'));
$pdf->SetXY(62, 169.8);
$pdf->Write(0, obtenerQuien('Enfermedades del Corazón'));

$pdf->SetXY(42, 174.6);
$pdf->Write(0, getChecked('Enfermedades Pulmonares'));
$pdf->SetXY(62, 174.6);
$pdf->Write(0, obtenerQuien('Enfermedades Pulmonares'));

$pdf->SetXY(42, 181.3);
$pdf->Write(0, getChecked('Enfermedades del Riñon'));
$pdf->SetXY(62, 181.3);
$pdf->Write(0, obtenerQuien('Enfermedades del Riñon'));

$pdf->SetXY(42, 187.8);
$pdf->Write(0, getChecked('Enfermedades del Higado'));
$pdf->SetXY(62, 187.8);
$pdf->Write(0, obtenerQuien('Enfermedades del Higado'));

$pdf->SetXY(42, 192.5);
$pdf->Write(0, getChecked('Alergias'));
$pdf->SetXY(62, 192.5);
$pdf->Write(0, obtenerQuien('Alergias'));


$pdf->SetXY(107.6, 155.3);
$pdf->Write(0, getChecked('Tumores o cáncer'));
$pdf->SetXY(123, 155.3);
$pdf->Write(0, obtenerQuien('Tumores o cáncer'));

$pdf->SetXY(107.6, 160);
$pdf->Write(0, getChecked('Asma bronquial'));
$pdf->SetXY(123, 160);
$pdf->Write(0, obtenerQuien('Asma bronquial'));

$pdf->SetXY(107.6, 165);
$pdf->Write(0, getChecked('Gastritis/Ulcera'));
$pdf->SetXY(123, 165);
$pdf->Write(0, obtenerQuien('Gastritis/Ulcera'));

$pdf->SetXY(107.6, 170);
$pdf->Write(0, getChecked('Flebitis/Várices'));
$pdf->SetXY(123, 170);
$pdf->Write(0, obtenerQuien('Flebitis/Várices'));

$pdf->SetXY(107.6, 174.6);
$pdf->Write(0, getChecked('Artritis'));
$pdf->SetXY(123, 174.6);
$pdf->Write(0, obtenerQuien('Artritis'));

$pdf->SetXY(107.6, 181);
$pdf->Write(0, getChecked('Alteraciones del sueño'));
$pdf->SetXY(123, 183);
$pdf->Write(0, obtenerQuien('Alteraciones del sueño'));

$pdf->SetXY(107.6, 187.5);
$pdf->Write(0, getChecked('Acufeno/Tinitus'));
$pdf->SetXY(123, 188);
$pdf->Write(0, obtenerQuien('Acufeno/Tinitus'));


$pdf->SetXY(180, 155.3);
$pdf->Write(0, getChecked('Problemas de espalda'));
$pdf->SetXY(195, 155.3);
$pdf->Write(0, obtenerQuien('Problemas de espalda'));

$pdf->SetXY(180, 160);
$pdf->Write(0, getChecked('Sensación de hormigueo'));
$pdf->SetXY(195, 160);
$pdf->Write(0, obtenerQuien('Sensación de hormigueo'));

$pdf->SetXY(180, 165);
$pdf->Write(0, getChecked('Convulsiones'));
$pdf->SetXY(195, 165);
$pdf->Write(0, obtenerQuien('Convulsiones'));

$pdf->SetXY(180, 170);
$pdf->Write(0, getChecked('Debilidad Muscular'));
$pdf->SetXY(195, 170);
$pdf->Write(0, obtenerQuien('Debilidad Muscular'));

$pdf->SetXY(180, 174.6);
$pdf->Write(0, getChecked('Osteoporosis'));
$pdf->SetXY(195, 174.6);
$pdf->Write(0, obtenerQuien('Osteoporosis'));

$pdf->SetXY(180, 181);
$pdf->Write(0, getChecked('Hernias'));
$pdf->SetXY(195, 183);
$pdf->Write(0, obtenerQuien('Hernias'));

$pdf->SetXY(180, 187.5);
$pdf->Write(0, getChecked('COVID 19'));
$pdf->SetXY(195, 188);
$pdf->Write(0, obtenerQuien('COVID 19'));



// Antecedentes no patológicos



$pdf->AddPage();
$pdf->setSourceFile('../media/formato.pdf');
$tplIdx = $pdf->importPage(2);
$pdf->useTemplate($tplIdx);







// Continúa con más campos según el diseño del PDF...

// Salida
$pdf->Output('I', 'historia_clinica.pdf');

$conn->close(); // Cerrar la conexión a la base de datos