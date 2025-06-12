<?php 
require_once '../vendor/autoload.php';
require 'conexion.php';

use setasign\Fpdi\Fpdi;

$id_consulta = $_GET['id'] ?? null;
if (!$id_consulta) die("ID de empleado no proporcionado.");
$carpeta_destino = __DIR__ . '../../media/consultas_pdf/';

// Utilidades
function obtenerDatos($tabla, $id_consulta)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM $tabla WHERE id_consulta = ?");
    $stmt->bind_param("i", $id_consulta);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    $stmt->close();
    return $data ?: [];
}
function obtenerDatosPac($tabla, $id_empleado)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM $tabla WHERE id_empleado = ?");
    $stmt->bind_param("i", $id_empleado);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    $stmt->close();
    return $data ?: [];
}
function obtenerFecha($fecha)
{
    return ($fecha && $fecha !== '0000-00-00') ? (new DateTime($fecha))->format('d/m/Y') : '';
}
function obtenerHora($fecha)
{
    return ($fecha && $fecha !== '0000-00-00') ? (new DateTime($fecha))->format('H:i') : '';
}

$consulta = obtenerDatos('consultas', $id_consulta);
$paciente = obtenerDatosPac('pacientes', $consulta['id_empleado']);

// Crear PDF
$pdf = new Fpdi();
$pdf->SetFont('Helvetica');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFontSize(9);

$pdf->AddPage();
$pdf->setSourceFile('../media/formato2.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx);


$pdf->SetXY(43, 31);
$pdf->Write(0, (isset($consulta['talla']) ? $consulta['talla'] . ' cm' : ''));
$pdf->SetXY(68, 31);
$pdf->Write(0, (isset($consulta['peso']) ? $consulta['peso'] . ' kg' : ''));
$pdf->SetXY(94, 31);
$pdf->Write(0, $consulta['imc'] ?? '');
$pdf->SetXY(115, 31);
$pdf->Write(0, (isset($consulta['fc']) ? $consulta['fc'] . ' lpm' : ''));
$pdf->SetXY(138, 31);
$pdf->Write(0, (isset($consulta['fr']) ? $consulta['fr'] . ' rpm' : ''));
$pdf->SetXY(161, 31);
$pdf->Write(0, (isset($consulta['temp']) ? $consulta['temp'] . ' C' : ''));

$pdf->SetXY(64, 40.5);
$pdf->Write(0, (isset($consulta['perimetro_abdominal']) ? $consulta['perimetro_abdominal'] . ' cm' : ''));
$pdf->SetXY(105, 40.5);
$pdf->Write(0, $consulta['presion_arterial'] ?? '');
$pdf->SetXY(155, 40.5);
$pdf->Write(0, $consulta['spo2'] ?? '');

$pdf->SetXY(15, 47.5);
$pdf->MultiCell(170, 3.5, ($consulta['motivo'] ?? ''), 0, 'L');

$pdf->SetXY(15, 66.5);
$pdf->MultiCell(170, 3.5, ($consulta['evaluacion_fisica'] ?? ''), 0, 'L');

$pdf->SetXY(8, 238);
$pdf->MultiCell(87, 3.5, ($consulta['botiquin'] ?? ''), 0, 'L');

$pdf->SetXY(121, 238);
$pdf->MultiCell(87, 3.5, ($consulta['destino'] ?? ''), 0, 'L');


$nombre_archivo = 'consulta_' . $paciente['id_empleado'] . '_' . date('dmY') . '.pdf';
$ruta_relativa = 'consultoriovirtual/media/consultas_pdf/' . $nombre_archivo;
$ruta_completa = $carpeta_destino . $nombre_archivo;

// Guardar el PDF en el servidor
$pdf->Output($ruta_completa, 'F'); // 'F' para guardar en archivo


$tipo_pdf = 'consulta'; // O el tipo que corresponda

// Eliminar registro anterior con la misma ruta
$stmt = $conn->prepare("DELETE FROM pdf WHERE ruta_pdf = ?");
$stmt->bind_param("s", $ruta_relativa);
$stmt->execute();
$stmt->close();


$stmt = $conn->prepare("INSERT INTO pdf (id_empleado, tipo_pdf, ruta_pdf, fecha_creacion) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iss", $consulta['id_empleado'], $tipo_pdf, $ruta_relativa);
$stmt->execute();
$stmt->close();

// Obtener el id del PDF recién insertado
$id_pdf = $conn->insert_id;

// Guardar el id del PDF en la consulta correspondiente
$stmt = $conn->prepare("UPDATE consultas SET pdf = ? WHERE id_consulta = ?");
$stmt->bind_param("ii", $id_pdf, $id_consulta);
$stmt->execute();
$stmt->close();

header('Location: ../../' . $ruta_relativa);
$conn->close();

?>