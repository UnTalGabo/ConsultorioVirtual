<?php
require_once "../conexion.php";

// Validar que venga el id del paciente
$id_empleado = $_POST['id_empleado'];
if ($id_empleado <= 0) {
    header("Location: ../../views/index.php?error=Paciente no válido");
    exit;
}

// Recibir datos del formulario
$fecha             = $_POST['fecha'] ?? date('Y-m-d');
$hora_entrada      = $_POST['hora_entrada'] ?? '';
$hora_salida       = $_POST['hora_salida'] ?? '';
$talla             = $_POST['talla'] ?? null;
$peso              = $_POST['peso'] ?? null;
$imc               = $_POST['imc'] ?? null;
$fc                = $_POST['fc'] ?? null;
$fr                = $_POST['fr'] ?? null;
$temp              = $_POST['temp'] ?? null;
$perimetro_abdominal = $_POST['perimetro_abdominal'] ?? null;
$presion_arterial  = $_POST['presion_arterial'] ?? null;
$spo2              = $_POST['spo2'] ?? null;

$motivo            = $_POST['motivo'] ?? '';
$cabeza            = $_POST['cabeza'] ?? '';
$oido              = $_POST['oido'] ?? '';
$cavidad_oral      = $_POST['cavidad_oral'] ?? '';
$cuello            = $_POST['cuello'] ?? '';
$torax             = $_POST['torax'] ?? '';
$abdomen           = $_POST['abdomen'] ?? '';
$columna           = $_POST['columna'] ?? '';
$extremidades_superiores = $_POST['extremidades_superiores'] ?? '';
$extremidades_inferiores = $_POST['extremidades_inferiores'] ?? '';

// Puedes agregar aquí los campos de botiquin y destino si los renombras en el formulario
$botiquin          = $_POST['botiquin'] ?? '';
$destino           = $_POST['destino'] ?? '';

// Insertar la consulta (cada consulta es un registro nuevo)
$sql = "INSERT INTO consultas (
    id_empleado,
    fecha,
    hora_entrada, 
    hora_salida,
    talla,
    peso,
    imc,
    fc,
    fr,
    temp,
    perimetro_abdominal,
    presion_arterial, 
    spo2,
    motivo, 
    cabeza, 
    oido, 
    cavidad_oral, 
    cuello, 
    torax, 
    abdomen, 
    columna, 
    extremidades_superiores, 
    extremidades_inferiores, 
    botiquin, 
    destino
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "isssddddddsssssssssssssss",
    $id_empleado,
    $fecha,
    $hora_entrada,
    $hora_salida,
    $talla,
    $peso,
    $imc,
    $fc,
    $fr,
    $temp,
    $perimetro_abdominal,
    $presion_arterial,
    $spo2,
    $motivo,
    $cabeza,
    $oido,
    $cavidad_oral,
    $cuello,
    $torax,
    $abdomen,
    $columna,
    $extremidades_superiores,
    $extremidades_inferiores,
    $botiquin,
    $destino
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: ../../views/index.php?id=$id_empleado&success=1");
    exit;
} else {
    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    header("Location: ../../views/index.php?id=$id_empleado&error=" . urlencode($error));
    exit;
}
