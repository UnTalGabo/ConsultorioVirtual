<?php
require_once "conexion.php";

// Validar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paso7.php?id=" . $_POST['id_empleado']);
    exit();
}

// Obtener datos del formulario
$id_empleado = $_POST['id_empleado'];
$edad_inicio_trabajo = $_POST['edad_inicio_trabajo'] ?? null;

// Datos de empresa
$empresa = $_POST['empresa'] ?? null;
$antiguedad = $_POST['antiguedad'] ?? null;
$puesto = $_POST['puesto'] ?? null;
$equipo_proteccion = $_POST['equipo_proteccion'] ?? null;

// Exposiciones (convertir array de checkboxes a columnas individuales)
$exposiciones = $_POST['exposicion'] ?? [];
$polvo = in_array('polvo', $exposiciones) ? 1 : 0;
$ruido = in_array('ruido', $exposiciones) ? 1 : 0;
$humo = in_array('humo', $exposiciones) ? 1 : 0;
$radiacion = in_array('radiacion', $exposiciones) ? 1 : 0;
$quimicos = in_array('quimicos', $exposiciones) ? 1 : 0;
$calor_frio = in_array('calor_frio', $exposiciones) ? 1 : 0;
$vibracion = in_array('vibracion', $exposiciones) ? 1 : 0;
$movimiento_repetitivo = in_array('movimiento_repetitivo', $exposiciones) ? 1 : 0;
$cargas = in_array('cargas', $exposiciones) ? 1 : 0;
$riesgos_psicosociales = in_array('riesgos_psicosociales', $exposiciones) ? 1 : 0;

// Datos de accidentes
$accidentes = $_POST['accidentes'] ?? 0;
$fecha_accidente = !empty($_POST['fecha_accidente']) ? $_POST['fecha_accidente'] : null;
$lesion = $_POST['lesion'] ?? null;
$pagos_accidente = $_POST['pagos_accidente'] ?? 0;
$pagado_por = $_POST['pagado_por'] ?? null;
$secuelas = $_POST['secuelas'] ?? 0;
$fecha_secuela = !empty($_POST['fecha_secuela']) ? $_POST['fecha_secuela'] : null;
$secuela = $_POST['secuela'] ?? null;

// Iniciar transacción
$conn->begin_transaction();

try {
    // Verificar si ya existe un registro
    $sql_check = "SELECT id FROM antecedentes_laborales WHERE id_empleado = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_empleado);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if ($result->num_rows > 0) {
        // Actualizar registro existente
        $sql = "UPDATE antecedentes_laborales SET 
                edad_inicio_trabajo = ?,
                empresa = ?,
                antiguedad = ?,
                puesto = ?,
                polvo = ?,
                ruido = ?,
                humo = ?,
                radiacion = ?,
                quimicos_solventes = ?,
                calor_frio = ?,
                vibracion = ?,
                movimiento_repetitivo = ?,
                cargas = ?,
                riesgos_psicosociales = ?,
                equipo_proteccion = ?,
                accidentes = ?,
                fecha_accidente = ?,
                lesion = ?,
                pagos_accidente = ?,
                pagado_por = ?,
                secuelas = ?,
                fecha_secuela = ?,
                secuela = ?,
                fecha_actualizacion = NOW()
                WHERE id_empleado = ?";
    } else {
        // Insertar nuevo registro
        $sql = "INSERT INTO antecedentes_laborales (
                id_empleado,
                edad_inicio_trabajo,
                empresa,
                antiguedad,
                puesto,
                polvo,
                ruido,
                humo,
                radiacion,
                quimicos_solventes,
                calor_frio,
                vibracion,
                movimiento_repetitivo,
                cargas,
                riesgos_psicosociales,
                equipo_proteccion,
                accidentes,
                fecha_accidente,
                lesion,
                pagos_accidente,
                pagado_por,
                secuelas,
                fecha_secuela,
                secuela,
                fecha_creacion,
                fecha_actualizacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($result->num_rows > 0) {
        $stmt->bind_param(
            "isssiiiiiiiiississsssssi",
            $edad_inicio_trabajo,
            $empresa,
            $antiguedad,
            $puesto,
            $polvo,
            $ruido,
            $humo,
            $radiacion,
            $quimicos,
            $calor_frio,
            $vibracion,
            $movimiento_repetitivo,
            $cargas,
            $riesgos_psicosociales,
            $equipo_proteccion,
            $accidentes,
            $fecha_accidente,
            $lesion,
            $pagos_accidente,
            $pagado_por,
            $secuelas,
            $fecha_secuela,
            $secuela,
            $id_empleado
        );
    } else {
        $stmt->bind_param(
            "iisssiiiiiiiiiisissisiss",
            $id_empleado,
            $edad_inicio_trabajo,
            $empresa,
            $antiguedad,
            $puesto,
            $polvo,
            $ruido,
            $humo,
            $radiacion,
            $quimicos,
            $calor_frio,
            $vibracion,
            $movimiento_repetitivo,
            $cargas,
            $riesgos_psicosociales,
            $equipo_proteccion,
            $accidentes,
            $fecha_accidente,
            $lesion,
            $pagos_accidente,
            $pagado_por,
            $secuelas,
            $fecha_secuela,
            $secuela
        );
    }
    
    $stmt->execute();
    $stmt->close();
    
    $conn->commit();
    header("Location: ../views/ver_pacientes.php?id=" . $id_empleado);
    exit();
    
} catch (Exception $e) {
    $conn->rollback();
    die("Error al guardar los datos: " . $e->getMessage());
}

$conn->close();
?>