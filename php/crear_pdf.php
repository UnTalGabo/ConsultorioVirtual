<?php
require_once '../vendor/autoload.php';
require 'conexion.php'; // tu archivo de conexión

use setasign\Fpdi\Fpdi;

$id_empleado = $_GET['id'] ?? null;

if (!$id_empleado) {
    die("ID de empleado no proporcionado.");
}

// Consulta de datos
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
$antecedentesGineco = obtenerDatos('antecedentes_gineco_obstetricos', $id_empleado);
$antecedentesPatologicos = obtenerDatos('antecedentes_patologicos', $id_empleado);

$sql = "SELECT enfermedad, parentesco FROM enfermedades_heredo WHERE id_empleado = ?";
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
$pdf->Write(0, $paciente['nombre_completo']);

$pdf->AddPage();
$pdf->setSourceFile('../media/HC laboral.pdf');
$tplIdx = $pdf->importPage(2);
$pdf->useTemplate($tplIdx);







// Continúa con más campos según el diseño del PDF...

// Salida
$pdf->Output('I', 'historia_clinica.pdf');
