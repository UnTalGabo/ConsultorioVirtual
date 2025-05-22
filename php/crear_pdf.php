<?php
require_once('../vendor/autoload.php');
require_once('../php/conexion.php');

use TCPDF as TCPDF;

$id_empleado = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_empleado === 0) die("ID inválido");

// Obtener todos los datos
$datos = [];
$tablas = [
    'pacientes',
    'antecedentes_laborales',
    'antecedentes_no_patologicos',
    'antecedentes_gineco_obstetricos',
    'antecedentes_patologicos',
    'enfermedades_heredo',
    'examenes_medicos'
];

foreach ($tablas as $tabla) {
    $stmt = $conn->prepare("SELECT * FROM $tabla WHERE id_empleado = ?");
    $stmt->bind_param("i", $id_empleado);
    $stmt->execute();
    $datos[$tabla] = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

class HC_PDF extends TCPDF {
    
    private $id_empleado;
    private $datos;
    
    public function __construct($datos, $orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->datos = $datos;
    }
    
    public function Header() {
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, 'Hospital Angeles Morelia', 0, 1, 'C');
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'HISTORIA CLÍNICA LABORAL', 0, 1, 'C');
        
        // Datos del paciente
        $this->SetFont('helvetica', '', 10);
        $paciente = $this->datos['pacientes'];
        $info = "Nombre: ".$paciente['nombre_completo']." | ID: ".$paciente['id_empleado']." | Fecha: ".date('d/m/Y');
        $this->Cell(0, 6, $info, 0, 1, 'L');
        $this->Line(10, 30, 200, 30);
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->PageNo().'/{nb}', 0, 0, 'C');
    }
    
    public function crearSeccion($titulo, $contenido) {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 8, $titulo, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $this->SetFont('helvetica', '', 10);
        $this->writeHTMLCell(0, 0, '', '', $contenido, 0, 1);
        $this->Ln(5);
    }
}

$pdf = new HC_PDF($datos);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->AddPage();

// Sección I: Ficha de Identificación
$html = '<table border="1" cellpadding="4">
    <tr>
        <td width="25%"><strong>Fecha de nacimiento:</strong><br>'.$datos['pacientes']['fecha_nacimiento'].'</td>
        <td width="25%"><strong>Género:</strong><br>'.$datos['pacientes']['genero'].'</td>
        <td width="25%"><strong>Estado civil:</strong><br>'.$datos['pacientes']['estado_civil'].'</td>
        <td width="25%"><strong>Teléfono:</strong><br>'.$datos['pacientes']['telefono'].'</td>
    </tr>
    <tr>
        <td colspan="4"><strong>Domicilio:</strong><br>'.$datos['pacientes']['direccion'].'</td>
    </tr>
</table>';
$pdf->crearSeccion('I. FICHA DE IDENTIFICACIÓN', $html);

// Sección II: Antecedentes Heredo-Familiares
$html = '<table border="1" cellpadding="4">';
foreach($datos['enfermedades_heredo'] as $enf) {
    $html .= '<tr><td width="70%">'.$enf['enfermedad'].'</td><td>'.$enf['parentesco'].'</td></tr>';
}
$html .= '</table>';
$pdf->crearSeccion('II. ANTECEDENTES HEREDO-FAMILIARES', $html);

// Sección III: Antecedentes No Patológicos
$ant = $datos['antecedentes_no_patologicos'];
$html = '<table border="1" cellpadding="4">
    <tr>
        <td width="30%"><strong>Fuma:</strong> '.($ant['fuma'] ? 'Sí' : 'No').'</td>
        <td width="30%"><strong>Bebe:</strong> '.($ant['bebe'] ? 'Sí' : 'No').'</td>
        <td width="40%"><strong>Deportes:</strong> '.$ant['tipo_deporte'].'</td>
    </tr>
    <!-- Agregar más campos según estructura -->
</table>';
$pdf->crearSeccion('III. ANTECEDENTES NO PATOLÓGICOS', $html);

// Sección IV-VI: Exámenes y Resultados
$html = '<table border="1" cellpadding="4">
    <tr>
        <td><strong>Talla:</strong> '.$datos['examenes_medicos']['talla'].' cm</td>
        <td><strong>Peso:</strong> '.$datos['examenes_medicos']['peso'].' kg</td>
        <td><strong>IMC:</strong> '.$datos['examenes_medicos']['imc'].'</td>
    </tr>
    <!-- Agregar más campos médicos -->
</table>';
$pdf->crearSeccion('IV. EXAMEN MÉDICO', $html);

// Firmas
$pdf->Ln(15);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(95, 5, '_________________________________', 0, 0, 'C');
$pdf->Cell(95, 5, '_________________________________', 0, 1, 'C');
$pdf->Cell(95, 5, 'Firma del Paciente', 0, 0, 'C');
$pdf->Cell(95, 5, 'Firma del Médico', 0, 1, 'C');

$pdf->Output('hc_'.$id_empleado.'.pdf', 'I');

$conn->close();
?>