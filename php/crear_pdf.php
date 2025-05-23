<?php
require_once('conexion.php');
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

// Obtener ID del empleado desde URL (ej: generar_pdf.php?id=77)
$id_empleado = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cargar datos principales del paciente
$sql = "SELECT * FROM pacientes WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();
$paciente = $resultado->fetch_assoc();
$stmt->close();

if (!$paciente) {
    die("Empleado no encontrado.");
}

// Cargar datos adicionales
function fetchOne($conn, $table, $id_empleado) {
    $stmt = $conn->prepare("SELECT * FROM $table WHERE id_empleado = ? LIMIT 1");
    $stmt->bind_param('i', $id_empleado);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
    $stmt->close();
}

$no_patologicos = fetchOne($conn, 'antecedentes_no_patologicos', $id_empleado);
$patologicos = fetchOne($conn, 'antecedentes_patologicos', $id_empleado);
$laborales = fetchOne($conn, 'antecedentes_laborales', $id_empleado);
$examen = fetchOne($conn, 'examenes_medicos', $id_empleado);
$gineco = fetchOne($conn, 'antecedentes_gineco_obstetricos', $id_empleado);

// Crear PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Historia Clínica Laboral');
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

function addLine($pdf, $label, $value) {
    $pdf->Cell(60, 8, "$label:", 0, 0);
    $pdf->Cell(120, 8, $value, 0, 1);
}

$pdf->Cell(0, 10, 'HISTORIA CLÍNICA LABORAL', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('', 'B');
$pdf->Cell(0, 10, 'I. FICHA DE IDENTIFICACIÓN', 0, 1);
$pdf->SetFont('', '');
addLine($pdf, 'Nombre completo', $paciente['nombre_completo']);
addLine($pdf, 'Fecha de nacimiento', $paciente['fecha_nacimiento']);
addLine($pdf, 'Género', $paciente['genero']);
addLine($pdf, 'Estado civil', $paciente['estado_civil']);
addLine($pdf, 'Teléfono', $paciente['telefono']);
addLine($pdf, 'Escolaridad', $paciente['escolaridad']);
addLine($pdf, 'Puesto', $paciente['puesto']);

$pdf->Ln(5);
$pdf->SetFont('', 'B');
$pdf->Cell(0, 10, 'IV. ANTECEDENTES PERSONALES NO PATOLÓGICOS', 0, 1);
$pdf->SetFont('', '');
if ($no_patologicos) {
    addLine($pdf, 'Fuma', $no_patologicos['fuma'] ? 'Sí' : 'No');
    addLine($pdf, 'Cigarros por día', $no_patologicos['cigarros_dia']);
    addLine($pdf, 'Años fumando', $no_patologicos['anos_fumando']);
    addLine($pdf, 'Bebe', $no_patologicos['bebe'] ? 'Sí' : 'No');
    addLine($pdf, 'Años bebiendo', $no_patologicos['anos_bebiendo']);
    addLine($pdf, 'Frecuencia', $no_patologicos['frecuencia_alcohol']);
    addLine($pdf, 'Usa drogas', $no_patologicos['usa_drogas'] ? 'Sí' : 'No');
    addLine($pdf, 'Tipo de droga', $no_patologicos['tipo_droga']);
    addLine($pdf, 'Deporte', $no_patologicos['tipo_deporte']);
}

$pdf->Ln(5);
$pdf->SetFont('', 'B');
$pdf->Cell(0, 10, 'VII. ANTECEDENTES MÉDICO - LABORALES', 0, 1);
$pdf->SetFont('', '');
if ($laborales) {
    addLine($pdf, 'Empresa', $laborales['empresa']);
    addLine($pdf, 'Puesto', $laborales['puesto']);
    addLine($pdf, 'Antigüedad', $laborales['antiguedad']);
    addLine($pdf, 'Edad inicio trabajo', $laborales['edad_inicio_trabajo']);
}

$pdf->Ln(5);
$pdf->SetFont('', 'B');
$pdf->Cell(0, 10, 'IX. EVALUACIÓN FÍSICA', 0, 1);
$pdf->SetFont('', '');
if ($examen) {
    addLine($pdf, 'Talla (cm)', $examen['talla']);
    addLine($pdf, 'Peso (kg)', $examen['peso']);
    addLine($pdf, 'IMC', $examen['imc']);
    addLine($pdf, 'Presión arterial', $examen['presion_arterial']);
    addLine($pdf, 'Resultado', $examen['resultado']);
    addLine($pdf, 'Recomendaciones', $examen['recomendaciones']);
}

$pdf->Output('historia_clinica.pdf', 'I');
?>
