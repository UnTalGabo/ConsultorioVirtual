<?php
require_once "../conexion.php";

// Validar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paso8.php?id=" . $_POST['id_empleado']);
    exit();
}

// Obtener datos del formulario
$id_empleado = $_POST['id_empleado'];
$accion = $_POST['accion'] ?? '';

// Somatometría y signos vitales
$talla = isset($_POST['talla']) ? floatval($_POST['talla']) : null;
$peso = isset($_POST['peso']) ? floatval($_POST['peso']) : null;
$imc = isset($_POST['imc']) ? floatval($_POST['imc']) : null;
$fc = isset($_POST['fc']) ? intval($_POST['fc']) : null;
$fr = isset($_POST['fr']) ? intval($_POST['fr']) : null;
$temp = isset($_POST['temp']) ? floatval($_POST['temp']) : null;
$perimetro_abdominal = isset($_POST['perimetro_abdominal']) ? intval($_POST['perimetro_abdominal']) : null;
$presion_arterial = isset($_POST['presion_arterial']) ? $_POST['presion_arterial'] : null;
$spo2 = isset($_POST['spo2']) ? intval($_POST['spo2']) : null;

// Evaluación física
$cabeza = isset($_POST['cabeza']) ? $_POST['cabeza'] : null;
$columna = isset($_POST['columna']) ? $_POST['columna'] : null;
$oido = isset($_POST['oido']) ? $_POST['oido'] : null;
$extremidades_superiores = isset($_POST['extremidades_superiores']) ? $_POST['extremidades_superiores'] : null;
$cavidad_oral = isset($_POST['cavidad_oral']) ? $_POST['cavidad_oral'] : null;
$extremidades_inferiores = isset($_POST['extremidades_inferiores']) ? $_POST['extremidades_inferiores'] : null;
$cuello = isset($_POST['cuello']) ? $_POST['cuello'] : null;
$torax = isset($_POST['torax']) ? $_POST['torax'] : null;
$abdomen = isset($_POST['abdomen']) ? $_POST['abdomen'] : null;

// Resultados
$resultado = isset($_POST['resultado']) ? $_POST['resultado'] : null;
$recomendaciones = isset($_POST['recomendaciones']) ? $_POST['recomendaciones'] : null;
$confirmacion_paciente = isset($_POST['confirmacion_paciente']) ? 1 : 0;

// Iniciar transacción
$conn->begin_transaction();

try {
    // Verificar si ya existe un registro
    $sql_check = "SELECT id FROM examenes_medicos WHERE id_empleado = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_empleado);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Actualizar registro existente
        $sql = "UPDATE examenes_medicos SET 
                talla = ?,
                peso = ?,
                imc = ?,
                fc = ?,
                fr = ?,
                temp = ?,
                perimetro_abdominal = ?,
                presion_arterial = ?,
                spo2 = ?,
                cabeza = ?,
                columna_vertebral = ?,
                oido = ?,
                extremidades_superiores = ?,
                cavidad_oral = ?,
                extremidades_inferiores = ?,
                cuello = ?,
                torax = ?,
                abdomen = ?,
                resultado = ?,
                recomendaciones = ?,
                fecha_actualizacion = NOW()
                WHERE id_empleado = ?";
    } else {
        // Insertar nuevo registro
        $sql = "INSERT INTO examenes_medicos (
                id_empleado,
                talla,
                peso,
                imc,
                fc,
                fr,
                temp,
                perimetro_abdominal,
                presion_arterial,
                spo2,
                cabeza,
                columna_vertebral,
                oido,
                extremidades_superiores,
                cavidad_oral,
                extremidades_inferiores,
                cuello,
                torax,
                abdomen,
                resultado,
                recomendaciones,
                fecha_creacion,
                fecha_actualizacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    }

    $stmt = $conn->prepare($sql);

    if ($result->num_rows > 0) {
        $stmt->bind_param(
            "dddiidisisssssssssssi",
            $talla,
            $peso,
            $imc,
            $fc,
            $fr,
            $temp,
            $perimetro_abdominal,
            $presion_arterial,
            $spo2,
            $cabeza,
            $columna,
            $oido,
            $extremidades_superiores,
            $cavidad_oral,
            $extremidades_inferiores,
            $cuello,
            $torax,
            $abdomen,
            $resultado,
            $recomendaciones,
            $id_empleado
        );
    } else {
        $stmt->bind_param(
            "idddiidisisssssssssss",
            $id_empleado,
            $talla,
            $peso,
            $imc,
            $fc,
            $fr,
            $temp,
            $perimetro_abdominal,
            $presion_arterial,
            $spo2,
            $cabeza,
            $columna,
            $oido,
            $extremidades_superiores,
            $cavidad_oral,
            $extremidades_inferiores,
            $cuello,
            $torax,
            $abdomen,
            $resultado,
            $recomendaciones
        );
    }

    $stmt->execute();
    $stmt->close();

    $conn->commit();
    header("Location: ../crear_pdf.php?id=$id_empleado&redir=1");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    die("Error al guardar los datos del examen médico: " . $e->getMessage());
}

