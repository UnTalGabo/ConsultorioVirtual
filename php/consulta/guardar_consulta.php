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
$evaluacion_fisica = $_POST['evaluacion_fisica'] ?? '';

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
    evaluacion_fisica,
    botiquin, 
    destino
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "isssddddddsssssss",
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
    $evaluacion_fisica,
    $botiquin,
    $destino
);

if ($stmt->execute()) {
    $consulta_id = $conn->insert_id; // Obtiene el ID autoincremental de la consulta
    $stmt->close();
    $conn->close();
     ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Redirigiendo...</title>
    </head>
    <body>
        <script>
            window.open('../pdf_consulta.php?id=<?php echo $consulta_id; ?>', '_blank');
            window.location.href = '../../views/consulta/historial.php?id=<?php echo $id_empleado; ?>';
        </script>
        <noscript>
            <p>Consulta guardada. <a href="../pdf_consulta.php?id=<?php echo $consulta_id; ?>" target="_blank">Ver PDF</a> | <a href="../../views/consulta/historial.php?id=<?php echo $id_empleado; ?>">Ir al historial</a></p>
        </noscript>
    </body>
    </html>
    <?php
    exit;
} else {
    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    header("Location: ../../views/index.php?id=$id_empleado&error=" . urlencode($error));
    exit;
}
