<?php 
require_once '../vendor/autoload.php';
require 'conexion.php';

use setasign\Fpdi\Fpdi;

$id_consulta = $_GET['id'] ?? null;
if (!$id_consulta) die("ID de empleado no proporcionado.");

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
function obtenerFecha($fecha)
{
    return ($fecha && $fecha !== '0000-00-00') ? (new DateTime($fecha))->format('d/m/Y') : '';
}
function obtenerHora($fecha)
{
    return ($fecha && $fecha !== '0000-00-00') ? (new DateTime($fecha))->format('H:i') : '';
}

$consulta = obtenerDatos('consultas', $id_consulta);

// Crear PDF
$pdf = new Fpdi();
$pdf->SetFont('Helvetica');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFontSize(9);

$pdf->AddPage();
$pdf->setSourceFile('../media/formato.pdf');
$tplIdx = $pdf->importPage(3);
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

$pdf->SetXY(20, 54.5);
$pdf->Write(0, utf8_decode($consulta['cabeza'] ?? ''));
$pdf->SetXY(15, 63.7);
$pdf->Write(0, utf8_decode($consulta['oido'] ?? ''));
$pdf->SetXY(30, 72.9);
$pdf->Write(0, utf8_decode($consulta['cavidad_oral'] ?? ''));
$pdf->SetXY(20, 82.1);
$pdf->Write(0, utf8_decode($consulta['cuello'] ?? ''));
$pdf->SetXY(20, 91.3);
$pdf->Write(0, utf8_decode($consulta['torax'] ?? ''));

$pdf->SetXY(42, 101);
$pdf->Write(0, utf8_decode($consulta['columna_vertebral'] ?? ''));
$pdf->SetXY(53, 110.5);
$pdf->Write(0, utf8_decode($consulta['extremidades_superiores'] ?? ''));
$pdf->SetXY(51, 120);
$pdf->Write(0, utf8_decode($consulta['extremidades_inferiores'] ?? ''));

$pdf->SetXY(23, 134.2);
$pdf->Write(0, utf8_decode($consulta['abdomen'] ?? ''));

$pdf->Output('I', 'historia_clinica.pdf');
$conn->close();

?>