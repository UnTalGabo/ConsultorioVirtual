<?php
require_once '../vendor/autoload.php';
require 'conexion.php';

use setasign\Fpdi\Fpdi;

$id_empleado = $_GET['id'] ?? null;
if (!$id_empleado) die("ID de empleado no proporcionado.");

// Utilidades
function obtenerDatos($tabla, $id_empleado)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM $tabla WHERE id_empleado = ?");
    $stmt->bind_param("i", $id_empleado);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    $stmt->close();
    return $data;
}
function obtenerFecha($fecha)
{
    return $fecha ? (new DateTime($fecha))->format('d/m/Y') : '';
}
function obtenerHora($fecha)
{
    return $fecha ? (new DateTime($fecha))->format('H:i') : '';
}
function obtenerEdad($fecha_nacimiento)
{
    return $fecha_nacimiento ? (new DateTime())->diff(new DateTime($fecha_nacimiento))->y : '';
}
function getChecked($nombre)
{
    global $enfermedadesH;
    foreach ($enfermedadesH as $e) if ($e['enfermedad'] == $nombre) return 'X';
    return '';
}
function obtenerQuien($nombre)
{
    global $enfermedadesH;
    foreach ($enfermedadesH as $e) if ($e['enfermedad'] == $nombre) return $e['parentesco'];
    return '';
}

// Datos principales
$paciente = obtenerDatos('pacientes', $id_empleado);
if (!$paciente) die("Paciente no encontrado.");

$antecedentesNoPatologicos = obtenerDatos('antecedentes_no_patologicos', $id_empleado);
if (!$antecedentesNoPatologicos) die("Antecedentes no patológicos no encontrados.");

$antecedentesGineco = ($paciente['genero'] === 'Femenino') ? obtenerDatos('antecedentes_gineco_obstetricos', $id_empleado) : null;
if ($paciente['genero'] === 'Femenino' && !$antecedentesGineco) die("Antecedentes gineco-obstétricos no encontrados.");

$antecedentesPatologicos = obtenerDatos('antecedentes_patologicos', $id_empleado);
if (!$antecedentesPatologicos) die("Antecedentes patológicos no encontrados.");

$antecedentesLaborales = obtenerDatos('antecedentes_laborales', $id_empleado);
if (!$antecedentesLaborales) die("Antecedentes laborales no encontrados.");

$examenMedico = obtenerDatos('examenes_medicos', $id_empleado);
if (!$examenMedico) die("Examen médico no encontrado.");

// Enfermedades heredo familiares
$enfermedadesH = [];
$stmt = $conn->prepare("SELECT enfermedad, parentesco FROM enfermedades_heredo WHERE id_empleado = ?");
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $enfermedadesH[] = $row;
$stmt->close();

// Enfermedades patológicas
$enfermedadesP = [];
$stmt = $conn->prepare("SELECT enfermedad FROM enfermedades_patologicas WHERE id_empleado = ?");
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $enfermedadesP[] = $row;
$stmt->close();

// Crear PDF
$pdf = new Fpdi();
$pdf->SetFont('Helvetica');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFontSize(9);

$pdf->AddPage();
$pdf->setSourceFile('../media/formato.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx);

// Datos personales
$pdf->SetXY(35, 49.5);
$pdf->Write(0, utf8_decode($paciente['nombre_completo']));
$pdf->SetXY(140, 26.5);
$pdf->Write(0, obtenerFecha($examenMedico['fecha_actualizacion']));
$pdf->SetXY(140, 31);
$pdf->Write(0, obtenerHora($examenMedico['fecha_actualizacion']));
$pdf->SetXY(35, 59);
$pdf->Write(0, utf8_decode(obtenerEdad($paciente['fecha_nacimiento']) . ' años'));
$pdf->SetXY($paciente['genero'] === 'Masculino' ? 107.5 : 154.6, 59);
$pdf->Write(0, 'X');
$pdf->SetXY(50, 63.5);
$pdf->Write(0, obtenerFecha($paciente['fecha_nacimiento']));
$pdf->SetXY(116, 63.5);
$pdf->Write(0, $paciente['telefono']);
$pdf->SetXY(160, 63);
$pdf->Write(0, $paciente['estado_civil']);
$pdf->SetXY(32, 68);
$pdf->Write(0, utf8_decode($paciente['calle'] . ' ' . $paciente['numero'] . ',    ' . $paciente['colonia'] . ',    ' . $paciente['ciudad'] . ',     Michoacan'));
$pdf->SetFontSize(7.5);
$pdf->SetXY(54, 73);
$pdf->Write(0, utf8_decode($paciente['contacto_emergencia']));
$pdf->SetFontSize(9);
$pdf->SetXY(123, 73);
$pdf->Write(0, utf8_decode($paciente['parentesco'] . '        ' . $paciente['telefono_emergencia']));
$pdf->SetXY(35, 77.5);
$pdf->Write(0, utf8_decode($paciente['puesto']));
$pdf->SetXY(127, 77.5);
$pdf->Write(0, utf8_decode($paciente['departamento']));

// Enfermedades heredo familiares
function escribirEnfermedad($pdf, $xCheck, $yCheck, $xPar, $yPar, $nombre)
{
    $pdf->SetXY($xCheck, $yCheck);
    $pdf->Write(0, getChecked($nombre));
    $pdf->SetXY($xPar, $yPar);
    $pdf->Write(0, obtenerQuien($nombre));
}
$enfermedades = [
    [42, 155.3, 58, 155.3, 'Presión alta/baja'],
    [42, 160.3, 58, 160.3, 'Vértigos'],
    [42, 165.3, 58, 165.3, 'Diabetes'],
    [42, 169.8, 58, 169.8, 'Enfermedades del Corazón'],
    [42, 174.6, 58, 174.6, 'Enfermedades Pulmonares'],
    [42, 179.5, 58, 179.5, 'Enfermedades del Riñon'],
    [42, 184, 58, 184, 'Enfermedades del Higado'],
    [42, 189.5, 58, 189.5, 'Alergias'],
    [107.6, 155.3, 119, 155.3, 'Tumores o cáncer'],
    [107.6, 160, 119, 160, 'Asma bronquial'],
    [107.6, 165, 119, 165, 'Gastritis/Ulcera'],
    [107.6, 170, 119, 170, 'Flebitis/Várices'],
    [107.6, 174.6, 119, 174.6, 'Artritis'],
    [107.6, 179.5, 119, 179.5, 'Alteraciones del sueño'],
    [107.6, 184, 119, 184, 'Acufeno/Tinitus'],
    [180, 155.3, 188, 155.3, 'Problemas de espalda'],
    [180, 160, 188, 160, 'Sensación de hormigueo'],
    [180, 165, 188, 165, 'Convulsiones'],
    [180, 170, 188, 170, 'Debilidad Muscular'],
    [180, 174.6, 188, 174.6, 'Osteoporosis'],
    [180, 179.5, 188, 179.5, 'Hernias'],
    [180, 184, 188, 184, 'COVID 19'],
];
foreach ($enfermedades as $e) escribirEnfermedad($pdf, ...$e);

// Antecedentes no patológicos
$pdf->SetXY(25, 198);
$pdf->Write(0, utf8_decode($paciente['escolaridad'] ?? ''));
$pdf->SetXY(62.5, 203);
$pdf->Write(0, $antecedentesNoPatologicos['fuma'] ? 'X' : '');
$pdf->SetXY(136.5, 203);
$pdf->Write(0, $antecedentesNoPatologicos['fuma'] ? $antecedentesNoPatologicos['cigarros_dia'] . ' cigarros' : '');
$pdf->SetXY(180, 203);
$pdf->Write(0, $antecedentesNoPatologicos['fuma'] ? utf8_decode($antecedentesNoPatologicos['anos_fumando'] . ' años') : '');
$pdf->SetXY(107.5, 203);
$pdf->Write(0, !$antecedentesNoPatologicos['fuma'] ? 'X' : '');

$pdf->SetXY(62.5, 207.7);
$pdf->Write(0, $antecedentesNoPatologicos['bebe'] ? 'X' : '');
$pdf->SetXY(137, 207.7);
$pdf->Write(0, $antecedentesNoPatologicos['bebe'] ? utf8_decode($antecedentesNoPatologicos['frecuencia_alcohol']) : '');
$pdf->SetXY(180, 207.7);
$pdf->Write(0, $antecedentesNoPatologicos['bebe'] ? utf8_decode($antecedentesNoPatologicos['anos_bebiendo'] . ' años') : '');
$pdf->SetXY(107.5, 211.2);
$pdf->Write(0, !$antecedentesNoPatologicos['bebe'] ? 'X' : '');

$pdf->SetXY(107.6, 212.5);
$pdf->Write(0, $antecedentesNoPatologicos['medicamentos_controlados'] ? 'X' : '');
$pdf->SetXY(138, 212.5);
$pdf->Write(0, !$antecedentesNoPatologicos['medicamentos_controlados'] ? 'X' : '');

$pdf->SetXY(87.5, 222);
$pdf->Write(0, $antecedentesNoPatologicos['usa_drogas'] ? 'X' : '');
$pdf->SetXY(124.5, 222);
$pdf->Write(0, !$antecedentesNoPatologicos['usa_drogas'] ? 'X' : '');
$pdf->SetXY(160, 222);
$pdf->Write(0, utf8_decode($antecedentesNoPatologicos['tipo_droga'] ?? ''));

$pdf->SetXY(87.5, 226.7);
$pdf->Write(0, $antecedentesNoPatologicos['practica_deporte'] ? 'X' : '');
$pdf->SetXY(124.5, 226.7);
$pdf->Write(0, !$antecedentesNoPatologicos['practica_deporte'] ? 'X' : '');
$pdf->SetXY(30, 231);
$pdf->Write(0, utf8_decode($antecedentesNoPatologicos['tipo_deporte'] ?? ''));
$pdf->SetXY(120, 231);
$pdf->Write(0, utf8_decode($antecedentesNoPatologicos['frecuencia_deporte'] ?? ''));

$pdf->SetXY(87.5, 236);
$pdf->Write(0, $antecedentesNoPatologicos['tatuajes'] ? 'X' : '');
$pdf->SetXY(124.5, 236);
$pdf->Write(0, !$antecedentesNoPatologicos['tatuajes'] ? 'X' : '');
$pdf->SetXY(25, 240.5);
$pdf->Write(0, utf8_decode($antecedentesNoPatologicos['cantidad_tatuajes'] ?? ''));
$pdf->SetXY(70, 240.5);
$pdf->Write(0, utf8_decode($antecedentesNoPatologicos['ubicacion_tatuajes'] ?? ''));

$pdf->SetXY(87.5, 245.5);
$pdf->Write(0, $antecedentesNoPatologicos['transfusiones'] ? 'X' : '');
$pdf->SetXY(124.5, 245.5);
$pdf->Write(0, !$antecedentesNoPatologicos['transfusiones'] ? 'X' : '');
$pdf->SetXY(188, 245.5);
$pdf->Write(0, $antecedentesNoPatologicos['transfusiones_recibidas'] ? 'X' : '');

$pdf->SetXY(87.5, 250.5);
$pdf->Write(0, $antecedentesNoPatologicos['fobias'] ? 'X' : '');
$pdf->SetXY(124.5, 250.5);
$pdf->Write(0, !$antecedentesNoPatologicos['fobias'] ? 'X' : '');
$pdf->SetXY(40, 255);
$pdf->Write(0, utf8_decode($antecedentesNoPatologicos['cual_fobia'] ?? ''));



$pdf->AddPage();
$pdf->setSourceFile('../media/formato.pdf');
$tplIdx = $pdf->importPage(2);
$pdf->useTemplate($tplIdx);

// Antecedentes gineco-obstétricos
if ($paciente['genero'] === 'Femenino') {
    // Antecedentes gineco-obstétricos
    $pdf->SetXY(58, 26);
    $pdf->Write(0, ($antecedentesGineco['edad_inicio_regla']));
    $pdf->SetXY(115, 26);
    $pdf->Write(0, utf8_decode($antecedentesGineco['ritmo_ciclo_menstrual'] . ' días'));
    $pdf->SetXY(150, 26);
    $pdf->Write(0, (obtenerFecha($antecedentesGineco['fecha_ultima_menstruacion'])));

    $pdf->SetXY(15, 30);
    $pdf->Write(0, ($antecedentesGineco['numero_gestas']));
    $pdf->SetXY(28, 30);
    $pdf->Write(0, ($antecedentesGineco['numero_partos']));
    $pdf->SetXY(41, 30);
    $pdf->Write(0, ($antecedentesGineco['numero_abortos']));
    $pdf->SetXY(54, 30);
    $pdf->Write(0, ($antecedentesGineco['numero_cesareas']));
    $pdf->SetXY(120, 30.5);
    $pdf->Write(0, obtenerFecha($antecedentesGineco['fecha_ultimo_embarazo']));

    $pdf->SetXY(62, 35);
    $pdf->Write(0, utf8_decode($antecedentesGineco['complicaciones_menstruacion']));

    $pdf->SetXY(72, 39.5);
    $pdf->Write(0, obtenerFecha($antecedentesGineco['fecha_ultima_citologia']));

    $pdf->SetXY(62.5, 48.2);
    $pdf->Write(0, ($antecedentesGineco['mastografia'] ? 'X' : ''));
    $pdf->SetXY(108, 48.2);
    $pdf->Write(0, ($antecedentesGineco['mastografia'] ? '' : 'X'));
    $pdf->SetXY(135, 48.2);
    $pdf->Write(0, obtenerFecha($antecedentesGineco['fecha_ultima_mastografia']));
}



function escribirPatologica($pdf, $x, $y, $nombre)
{
    global $enfermedadesP;
    foreach ($enfermedadesP as $e) {
        if (strcasecmp($e['enfermedad'], $nombre) === 0) {
            $pdf->SetXY($x, $y);
            $pdf->Write(0, 'X');
            return;
        }
    }
}


$enfermedades_patologicas = [
    // x, y, nombre
    [54.5, 61.5,  'Varicela/Rubeola/Sarampión'],
    [54.5, 67.5,  'Enfermedades respiratorias'],
    [54.5, 73,  'Enfermedades pulmonares'],
    [54.5, 78.5,  'Asma bronquial'],
    [54.5, 84,  'Enfermedades del Corazón'],
    [54.5, 89.5,  'Presión alta o baja'],
    [54.5, 94,  'Vértigos'],
    [54.5, 99,  'Anemia/Sangrado anormal'],
    [54.5, 104, 'Tuberculosis'],
    [101.7, 61.5,  'Varices/Hemorroides'],
    [101.7, 67.5,  'Cefalea'],
    [101.7, 73,  'Hernias'],
    [101.7, 78.5,  'Problemas en la espalda'],
    [101.7, 84,  'Golpes en la columna'],
    [101.7, 89.5,  'Golpes en la cabeza'],
    [101.7, 94,  'Artritis o Reumatismo'],
    [101.7, 99,  'Depresión/Ansiedad'],
    [101.7, 104, 'Paludismo'],
    [153, 61.5,  'Sensación de hormigueo'],
    [153, 67.5,  'Enfermedades Gastrointestinales'],
    [153, 73,  'Gastritis/Ulcera/Colitis'],
    [153, 78.5,  'Enfermedades del higado'],
    [153, 84,  'Diabetes'],
    [153, 89.5,  'Enfermedades del riñon'],
    [153, 94,  'Enfermedades de Genitales'],
    [153, 99,  'Convulsiones (Epilepsia)'],
    [153, 104, 'Parotiditis'],
    [191.5, 61.7, 'Transtornos de la piel'],
    [191.5, 67.5, 'Heridas/quemaduras'],
    [191.5, 73, 'Enfermedades oculares'],
    [191.5, 78.5, 'Enfermedades dentales'],
    [191.5, 84, 'Problemas de audicion'],
    [191.5, 89.5, 'Acufeno/Tinitus'],
    [191.5, 94, 'Usa prótesis'],
    [191.5, 99, 'Tumores o cáncer'],
    [191.5, 104, 'COVID 19'],
];
foreach ($enfermedades_patologicas as [$x, $y, $nombre]) {
    escribirPatologica($pdf, $x, $y, $nombre);
}


$pdf->SetXY(38, 113);
$pdf->Write(0, utf8_decode($antecedentesPatologicos['fracturas_esguinces']));
$pdf->SetXY(38, 118);
$pdf->Write(0, utf8_decode($antecedentesPatologicos['cirugias']));
$pdf->SetXY(165, 118);
$pdf->Write(0, utf8_decode($paciente['tipo_sangre']));

$pdf->SetXY(68, 122.5);
$pdf->Write(0, utf8_decode($antecedentesPatologicos['enfermedad_actual_desc']));

$pdf->SetXY(42, 127);
$pdf->Write(0, utf8_decode($antecedentesPatologicos['medicamentos']));

$pdf->SetXY(30, 131.5);
$pdf->Write(0, utf8_decode($antecedentesPatologicos['observaciones']));


// Antecedentes laborales
$pdf->SetXY(75, 145.7);
$pdf->Write(0, ($antecedentesLaborales['edad_inicio_trabajo']));

$pdf->SetXY(25, 150);
$pdf->Write(0, utf8_decode($antecedentesLaborales['empresa']));
$pdf->SetXY(75, 150);
$pdf->Write(0, utf8_decode($antecedentesLaborales['antiguedad'] . ' años'));
$pdf->SetXY(120, 150);
$pdf->Write(0, utf8_decode($antecedentesLaborales['puesto']));

$pdf->SetXY(39.5 - 4.5, 159.5 + 1);
$pdf->Write(0, ($antecedentesLaborales['polvo'] ? 'X' : ''));
$pdf->SetXY(39.5 - 4.5, 164 + 1);
$pdf->Write(0, ($antecedentesLaborales['movimiento_repetitivo'] ? 'X' : ''));
$pdf->SetXY(56 - 4, 159.5 + 1);
$pdf->Write(0, ($antecedentesLaborales['ruido'] ? 'X' : ''));
$pdf->SetXY(56 - 4, 164 + 1);
$pdf->Write(0, ($antecedentesLaborales['cargas'] ? 'X' : ''));
$pdf->SetXY(92.5 - 4, 159.5 + 1);
$pdf->Write(0, ($antecedentesLaborales['humo'] ? 'X' : ''));
$pdf->SetXY(92.5 - 4, 164 + 1);
$pdf->Write(0, ($antecedentesLaborales['riesgos_psicosociales'] ? 'X' : ''));
$pdf->SetXY(115.3 - 4, 159.5 + 1);
$pdf->Write(0, ($antecedentesLaborales['radiacion'] ? 'X' : ''));
$pdf->SetXY(151.8 - 4, 159.5 + 1);
$pdf->Write(0, ($antecedentesLaborales['quimicos_solventes'] ? 'X' : ''));
$pdf->SetXY(172.5 - 4, 159.5 + 1);
$pdf->Write(0, ($antecedentesLaborales['vibracion'] ? 'X' : ''));
$pdf->SetXY(195 - 5.8, 159.5 + 1);
$pdf->Write(0, ($antecedentesLaborales['calor_frio'] ? 'X' : ''));

$pdf->SetXY(60, 169.5);
$pdf->Write(0, utf8_decode($antecedentesLaborales['equipo_proteccion']));

$pdf->SetXY(75.7, 182);
$pdf->Write(0, ($antecedentesLaborales['accidentes'] ? 'X' : ''));
$pdf->SetXY(115.7, 182);
$pdf->Write(0, ($antecedentesLaborales['accidentes'] ? '' : 'X'));
$pdf->SetXY(150, 182);
$pdf->Write(0, obtenerFecha($antecedentesLaborales['fecha_accidente'] ?? ''));
$pdf->SetXY(20, 187);
$pdf->Write(0, utf8_decode($antecedentesLaborales['lesion'] ?? ''));

$pdf->SetXY(107.7, 191.5);
$pdf->Write(0, ($antecedentesLaborales['pagos_accidente'] ? 'X' : ''));
$pdf->SetXY(138, 191.5);
$pdf->Write(0, ($antecedentesLaborales['pagos_accidente'] ? '' : 'X'));
$pdf->SetXY(20, 196);
$pdf->Write(0, utf8_decode($antecedentesLaborales['secuela'] ?? ''));
$pdf->SetXY(124.5, 196);
$pdf->Write(0, ($antecedentesLaborales['pagado_por'] == 'imss' ? 'X' : ''));
$pdf->SetXY(176.5, 196);
$pdf->Write(0, ($antecedentesLaborales['pagado_por'] == 'imss' && $antecedentesLaborales['pagado_por'] != null ? '' : 'X'));
$pdf->SetXY(107.7, 201);
$pdf->Write(0, ($antecedentesLaborales['secuelas'] ? 'X' : ''));
$pdf->SetXY(138, 201);
$pdf->Write(0, ($antecedentesLaborales['secuelas'] ? '' : 'X'));
$pdf->SetXY(20, 205.5);
$pdf->Write(0, obtenerFecha($antecedentesLaborales['fecha_secuela'] ?? ''));
$pdf->SetXY(70, 205.5);
$pdf->Write(0, utf8_decode($antecedentesLaborales['secuela'] ?? ''));

$pdf->AddPage();
$pdf->setSourceFile('../media/formato.pdf');
$tplIdx = $pdf->importPage(3);
$pdf->useTemplate($tplIdx);

$pdf->SetXY(43, 31);
$pdf->Write(0, ($examenMedico['talla']) . ' cm');
$pdf->SetXY(68, 31);
$pdf->Write(0, ($examenMedico['peso']) . ' kg');
$pdf->SetXY(94, 31);
$pdf->Write(0, ($examenMedico['imc']));
$pdf->SetXY(115, 31);
$pdf->Write(0, ($examenMedico['fc']) . ' lpm');
$pdf->SetXY(138, 31);
$pdf->Write(0, ($examenMedico['fr']) . ' rpm');
$pdf->SetXY(161, 31);
$pdf->Write(0, ($examenMedico['temp']) . ' C');

$pdf->SetXY(64, 40.5);
$pdf->Write(0, ($examenMedico['perimetro_abdominal'] . ' cm'));
$pdf->SetXY(105, 40.5);
$pdf->Write(0, ($examenMedico['presion_arterial']));
$pdf->SetXY(155, 40.5);
$pdf->Write(0, ($examenMedico['spo2']));


$pdf->SetXY(20, 54.5);
$pdf->Write(0, utf8_decode($examenMedico['cabeza']));
$pdf->SetXY(15, 63.7);
$pdf->Write(0, utf8_decode($examenMedico['oido']));
$pdf->SetXY(30, 72.9);
$pdf->Write(0, utf8_decode($examenMedico['cavidad_oral']));
$pdf->SetXY(20, 82.1);
$pdf->Write(0, utf8_decode($examenMedico['cuello']));
$pdf->SetXY(20, 91.3);
$pdf->Write(0, utf8_decode($examenMedico['torax']));

$pdf->SetXY(42, 101);
$pdf->Write(0, utf8_decode($examenMedico['columna_vertebral']));
$pdf->SetXY(53, 110.5);
$pdf->Write(0, utf8_decode($examenMedico['extremidades_superiores']));
$pdf->SetXY(51, 120);
$pdf->Write(0, utf8_decode($examenMedico['extremidades_inferiores']));

$pdf->SetXY(23, 134.2);
$pdf->Write(0, utf8_decode($examenMedico['abdomen']));

$pdf->Output('I', 'historia_clinica.pdf');
$conn->close();
