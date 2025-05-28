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
function obtenerFecha($fechas){
    if ($fechas) {
        $fecha = new DateTime($fechas);
        return $fecha->format('d/m/Y');
    } else {
        return '';
    }
}
function obtenerHora($fechas){
    if ($fechas) {
        $fecha = new DateTime($fechas);
        return $fecha->format('H:i');
    } else {
        return '';
    }
}
function obtenerEdad($fecha_nacimiento){
    if ($fecha_nacimiento) {
        $fecha = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha);
        return $edad->y; // Devuelve la edad en años
    } else {
        return '';
    }
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
$pdf->SetFontSize(10);


$pdf->AddPage();
$pdf->setSourceFile('../media/HC laboral.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx);

// Insertar texto por coordenadas (X, Y)
$pdf->SetXY(33, 72);
$pdf->Write(0, utf8_decode($paciente['nombre_completo']));

$pdf->SetXY(126, 47);
$pdf->Write(0, obtenerFecha($examenMedico['fecha_actualizacion']));

$pdf->SetXY(126, 52);
$pdf->Write(0, obtenerHora($examenMedico['fecha_actualizacion']));

$pdf->AddPage();
$pdf->setSourceFile('../media/HC laboral.pdf');
$tplIdx = $pdf->importPage(2);
$pdf->useTemplate($tplIdx);







// Continúa con más campos según el diseño del PDF...

// Salida
$pdf->Output('I', 'historia_clinica.pdf');

$conn->close(); // Cerrar la conexión a la base de datos