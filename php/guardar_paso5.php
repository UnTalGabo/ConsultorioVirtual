<?php
require_once "conexion.php";

// Validar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paso5.php?id=" . $_POST['id_empleado']);
    exit();
}

// Obtener datos del formulario
$id_empleado = $_POST['id_empleado'];
$edad_inicio_regla = !empty($_POST['edad_inicio_regla']) ? intval($_POST['edad_inicio_regla']) : null;
$ritmo_ciclo_menstrual = !empty($_POST['ritmo_ciclo_menstrual']) ? intval($_POST['ritmo_ciclo_menstrual']) : null;
$fecha_ultima_menstruacion = !empty($_POST['fecha_ultima_menstruacion']) ? $_POST['fecha_ultima_menstruacion'] : null;
$numero_gestas = !empty($_POST['numero_gestas']) ? intval($_POST['numero_gestas']) : 0;
$numero_partos = !empty($_POST['numero_partos']) ? intval($_POST['numero_partos']) : 0;
$numero_abortos = !empty($_POST['numero_abortos']) ? intval($_POST['numero_abortos']) : 0;
$numero_cesareas = !empty($_POST['numero_cesareas']) ? intval($_POST['numero_cesareas']) : 0;
$complicaciones_menstruacion = !empty($_POST['complicaciones_menstruacion']) ? $_POST['complicaciones_menstruacion'] : null;
$fecha_ultima_citologia = !empty($_POST['fecha_ultima_citologia']) ? $_POST['fecha_ultima_citologia'] : null;
$mastografia = isset($_POST['mastografia']) ? 1 : 0;

// Verificar si ya existe un registro para este paciente
$sql_check = "SELECT id FROM antecedentes_gineco_obstetricos WHERE id_empleado = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id_empleado);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Actualizar registro existente
    $sql = "UPDATE antecedentes_gineco_obstetricos SET 
            edad_inicio_regla = ?, 
            ritmo_ciclo_menstrual = ?, 
            fecha_ultima_menstruacion = ?, 
            numero_gestas = ?, 
            numero_partos = ?, 
            numero_abortos = ?, 
            numero_cesareas = ?, 
            complicaciones_menstruacion = ?, 
            fecha_ultima_citologia = ?, 
            mastografia = ? 
            WHERE id_empleado = ?";
} else {
    // Insertar nuevo registro
    $sql = "INSERT INTO antecedentes_gineco_obstetricos (
            id_empleado, 
            edad_inicio_regla, 
            ritmo_ciclo_menstrual, 
            fecha_ultima_menstruacion, 
            numero_gestas, 
            numero_partos, 
            numero_abortos, 
            numero_cesareas, 
            complicaciones_menstruacion, 
            fecha_ultima_citologia, 
            mastografia
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);

if ($result->num_rows > 0) {
    $stmt->bind_param(
        "iisiiiissii",
        $edad_inicio_regla,
        $ritmo_ciclo_menstrual,
        $fecha_ultima_menstruacion,
        $numero_gestas,
        $numero_partos,
        $numero_abortos,
        $numero_cesareas,
        $complicaciones_menstruacion,
        $fecha_ultima_citologia,
        $mastografia,
        $id_empleado
    );
} else {
    $stmt->bind_param(
        "iiisiiiissi",
        $id_empleado,
        $edad_inicio_regla,
        $ritmo_ciclo_menstrual,
        $fecha_ultima_menstruacion,
        $numero_gestas,
        $numero_partos,
        $numero_abortos,
        $numero_cesareas,
        $complicaciones_menstruacion,
        $fecha_ultima_citologia,
        $mastografia
    );
}

if ($stmt->execute()) {
    // Redirigir al siguiente paso (paso 6)
    header("Location: ../views/paso6.php?id=" . $id_empleado);
} else {
    // Mostrar error
    die("Error al guardar los datos: " . $conn->error);
}

// Cerrar conexiones
$stmt_check->close();
$stmt->close();
$conn->close();
