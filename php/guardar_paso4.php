<?php
require_once "conexion.php";


// Validar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paso4.php?id=" . $_POST['id_empleado']);
    exit();
}
$id_empleado = $_POST['id_empleado'];
$accion = $_POST['accion'] ?? '';
$sql_genero = "SELECT genero FROM pacientes WHERE id_empleado = ?";
$stmt_genero = $conn->prepare($sql_genero);
$stmt_genero->bind_param("i", $id_empleado);
$stmt_genero->execute();
$result_genero = $stmt_genero->get_result();
$paciente = $result_genero->fetch_assoc();
$genero = $paciente['genero'];
$stmt_genero->close();

// Obtener datos del formulario

$fuma = isset($_POST['fuma']) ? 1 : 0;
$cigarros_dia = isset($_POST['cigarros_dia']) ? intval($_POST['cigarros_dia']) : null;
$anos_fumando = isset($_POST['anos_fumando']) ? intval($_POST['anos_fumando']) : null;
$bebe = isset($_POST['bebe']) ? 1 : 0;
$anos_bebiendo = isset($_POST['anos_bebiendo']) ? intval($_POST['anos_bebiendo']) : null;
$frecuencia_alcohol = isset($_POST['frecuencia_alcohol']) ? $_POST['frecuencia_alcohol'] : null;
$medicamentos_controlados = isset($_POST['medicamentos_controlados']) ? 1 : 0;
$nombre_medicamento = isset($_POST['nombre_medicamento_controlado']) ? $_POST['nombre_medicamento_controlado'] : null;
$desde_cuando_medicamento_controlado = isset($_POST['desde_cuando_medicamento_controlado']) ? $_POST['desde_cuando_medicamento_controlado'] : null;
$usa_drogas = isset($_POST['usa_drogas']) ? 1 : 0;
$tipo_droga = isset($_POST['tipo_droga']) ? $_POST['tipo_droga'] : null;
$practica_deporte = isset($_POST['practica_deporte']) ? 1 : 0;
$tipo_deporte = isset($_POST['tipo_deporte']) ? $_POST['tipo_deporte'] : null;
$frecuencia_deporte = isset($_POST['frecuencia_deporte']) ? $_POST['frecuencia_deporte'] : null;
$tatuajes = isset($_POST['tatuajes']) ? 1 : 0;
$cantidad_tatuajes = isset($_POST['cantidad_tatuajes']) ? intval($_POST['cantidad_tatuajes']) : null;
$ubicacion_tatuajes = isset($_POST['ubicacion_tatuajes']) ? $_POST['ubicacion_tatuajes'] : null;
$transfusiones = isset($_POST['transfusiones']) ? 1 : 0;
$transfusiones_recibidas = isset($_POST['transfusiones_recibidas']) ? 1 : 0;
$fobias = isset($_POST['fobias']) ? 1 : 0;
$cual_fobia = isset($_POST['cual_fobia']) ? $_POST['cual_fobia'] : null;
// Verificar si ya existe un registro para este paciente
$sql_check = "SELECT id FROM antecedentes_no_patologicos WHERE id_empleado = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id_empleado);
$stmt_check->execute();
$result = $stmt_check->get_result();


if ($result->num_rows > 0) {
    // Actualizar registro existente
    $sql = "UPDATE antecedentes_no_patologicos SET 
            fuma = ?, 
            cigarros_dia = ?, 
            anos_fumando = ?, 
            bebe = ?, 
            anos_bebiendo = ?, 
            frecuencia_alcohol = ?, 
            medicamentos_controlados = ?, 
            nombre_medicamento_controlado = ?,
            desde_cuando_medicamento_controlado = ?,
            usa_drogas = ?, 
            tipo_droga = ?, 
            practica_deporte = ?, 
            tipo_deporte = ?, 
            frecuencia_deporte = ?,
            tatuajes = ?, 
            cantidad_tatuajes = ?,
            ubicacion_tatuajes = ?,
            transfusiones = ?, 
            transfusiones_recibidas = ?, 
            fobias = ?,
            cual_fobia = ?
            WHERE id_empleado = ?";
} else {
    // Insertar nuevo registro
    $sql = "INSERT INTO antecedentes_no_patologicos (
            id_empleado, 
            fuma, 
            cigarros_dia, 
            anos_fumando, 
            bebe, 
            anos_bebiendo, 
            frecuencia_alcohol, 
            medicamentos_controlados, 
            nombre_medicamento_controlado,
            desde_cuando_medicamento_controlado,
            usa_drogas, 
            tipo_droga, 
            practica_deporte, 
            tipo_deporte, 
            frecuencia_deporte,
            tatuajes, 
            cantidad_tatuajes,
            ubicacion_tatuajes,
            transfusiones, 
            transfusiones_recibidas,
            fobias,
            cual_fobia
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);

if ($result->num_rows > 0) {
    $stmt->bind_param(
        "iiiiisissisissiisiiisi",
        $fuma,
        $cigarros_dia,
        $anos_fumando,
        $bebe,
        $anos_bebiendo,
        $frecuencia_alcohol,
        $medicamentos_controlados,
        $nombre_medicamento,
        $desde_cuando_medicamento_controlado,
        $usa_drogas,
        $tipo_droga,
        $practica_deporte,
        $tipo_deporte,
        $frecuencia_deporte,
        $tatuajes,
        $cantidad_tatuajes,
        $ubicacion_tatuajes,
        $transfusiones,
        $transfusiones_recibidas,
        $fobias,
        $cual_fobia,
        $id_empleado
    );
} else {
    $stmt->bind_param(
        "iiiiiisissisissiisiiis",
        $id_empleado,
        $fuma,
        $cigarros_dia,
        $anos_fumando,
        $bebe,
        $anos_bebiendo,
        $frecuencia_alcohol,
        $medicamentos_controlados,
        $nombre_medicamento,
        $desde_cuando_medicamento_controlado,
        $usa_drogas,
        $tipo_droga,
        $practica_deporte,
        $tipo_deporte,
        $frecuencia_deporte,
        $tatuajes,
        $cantidad_tatuajes,
        $ubicacion_tatuajes,
        $transfusiones,
        $transfusiones_recibidas,
        $fobias,
        $cual_fobia
    );
}

if ($stmt->execute()) {
    // Redirigir según el género
    if (strtolower($genero) === 'femenino' && $accion === 'guardar_continuar') {
        header("Location: ../views/paso5.php?id=" . $id_empleado);
    } else if ($accion === 'guardar_continuar') {
        header("Location: ../views/paso6.php?id=" . $id_empleado);
    }
} else {
    // Mostrar error
    die("Error al guardar los datos: " . $conn->error);
}

// Cerrar conexiones
$stmt_check->close();
$stmt->close();
$conn->close();
