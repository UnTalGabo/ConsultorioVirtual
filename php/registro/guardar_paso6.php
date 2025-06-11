<?php
require_once "../conexion.php";

// Validar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paso6.php?id=" . $_POST['id_empleado']);
    exit();
}

// Obtener datos del formulario
$id_empleado = $_POST['id_empleado'];
$accion = $_POST['accion'] ?? '';
$enfermedades = $_POST['enfermedades'] ?? [];
$fracturas_esguinces = $_POST['fracturas_esguinces'] ?? null;
$cirugias = $_POST['cirugias'] ?? null;
$enfermedad_actual = isset($_POST['enfermedad_actual']) ? 1 : 0;
$enfermedad_actual_desc = $_POST['enfermedad_actual_desc'] ?? null;
$medicamentos = $_POST['medicamentos'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;

// Procesar enfermedades adicionales (si se especificaron)
if (!empty($_POST["otra_enfermedad_4"])) {
    $enfermedades[] = $_POST["otra_enfermedad_4"];
}

// Procesar vacunas
$vacunas = $_POST['vacunas'] ?? [];
$vacunas_fecha = $_POST['vacunas_fecha'] ?? [];

// Lista de campos de vacunas y fechas (debe coincidir con tu tabla)
$vacunas_campos = [
    'covid', 'influenza', 'sarampion', 'tetanos', 'varicela', 'herpes', 'vph',
    'hepatitis_a', 'hepatitis_b', 'neumococo', 'meningococo', 'rabia', 'fiebre_amarilla'
];
$vacunas_fechas = [
    'covid_penultima', 'covid_ultima',
    'influenza_penultima', 'influenza_ultima',
    'sarampion_1', 'sarampion_2',
    'tetanos_1', 'tetanos_2', 'tetanos_3', 'tetanos_refuerzo',
    'varicela_1', 'varicela_2',
    'herpes_1', 'herpes_2',
    'vph_1', 'vph_2', 'vph_3',
    'hepatitis_a_1', 'hepatitis_a_2',
    'hepatitis_b_1', 'hepatitis_b_2', 'hepatitis_b_3',
    'neumococo_penultima', 'neumococo_ultima',
    'meningococo_1',
    'rabia_1',
    'fiebre_amarilla_1'
];

// Iniciar transacción para asegurar integridad de datos
$conn->begin_transaction();

try {
    // 1. Eliminar registros existentes de enfermedades patológicas
    $sql_delete = "DELETE FROM enfermedades_patologicas WHERE id_empleado = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_empleado);
    $stmt_delete->execute();
    $stmt_delete->close();

    // 2. Insertar nuevas enfermedades patológicas
    if (!empty($enfermedades)) {
        $sql_insert = "INSERT INTO enfermedades_patologicas (id_empleado, enfermedad) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);

        foreach ($enfermedades as $enfermedad) {
            $stmt_insert->bind_param("is", $id_empleado, $enfermedad);
            $stmt_insert->execute();
        }
        $stmt_insert->close();
    }

    // 3. Actualizar o insertar antecedentes patológicos
    $sql_check = "SELECT id FROM antecedentes_patologicos WHERE id_empleado = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_empleado);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Actualizar registro existente
        $sql = "UPDATE antecedentes_patologicos SET 
                fracturas_esguinces = ?,
                cirugias = ?,
                enfermedad_actual_desc = ?,
                medicamentos = ?,
                observaciones = ?,
                fecha_actualizacion = NOW()
                WHERE id_empleado = ?";
    } else {
        // Insertar nuevo registro
        $sql = "INSERT INTO antecedentes_patologicos (
                id_empleado,
                fracturas_esguinces,
                cirugias,
                enfermedad_actual_desc,
                medicamentos,
                observaciones,
                fecha_creacion,
                fecha_actualizacion
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
    }

    $stmt = $conn->prepare($sql);

    if ($result->num_rows > 0) {
        $stmt->bind_param(
            "sssssi",
            $fracturas_esguinces,
            $cirugias,
            $enfermedad_actual_desc,
            $medicamentos,
            $observaciones,
            $id_empleado
        );
    } else {
        $stmt->bind_param(
            "isssss",
            $id_empleado,
            $fracturas_esguinces,
            $cirugias,
            $enfermedad_actual_desc,
            $medicamentos,
            $observaciones
        );
    }

    $stmt->execute();
    $stmt->close();

    // 4. Guardar vacunas (insertar o actualizar)
    // Preparar los datos
    $vacunas_data = [];
    foreach ($vacunas_campos as $campo) {
        $vacunas_data[$campo] = isset($vacunas[$campo]) ? 1 : 0;
    }
    foreach ($vacunas_fechas as $campo) {
        $vacunas_data[$campo] = $vacunas_fecha[$campo] ?? null;
    }

    // Revisar si ya existe registro de vacunas
    $sql_check_vac = "SELECT id FROM vacunas WHERE id_empleado = ?";
    $stmt_check_vac = $conn->prepare($sql_check_vac);
    $stmt_check_vac->bind_param("i", $id_empleado);
    $stmt_check_vac->execute();
    $result_vac = $stmt_check_vac->get_result();

    if ($result_vac->num_rows > 0) {
        // UPDATE
        $set = [];
        $types = '';
        $values = [];
        foreach ($vacunas_campos as $campo) {
            $set[] = "$campo = ?";
            $types .= 'i';
            $values[] = $vacunas_data[$campo];
        }
        foreach ($vacunas_fechas as $campo) {
            $set[] = "$campo = ?";
            $types .= 's';
            $values[] = $vacunas_data[$campo];
        }
        $types .= 'i';
        $values[] = $id_empleado;
        $sql_vac = "UPDATE vacunas SET " . implode(', ', $set) . ", fecha_actualizacion = NOW() WHERE id_empleado = ?";
        $stmt_vac = $conn->prepare($sql_vac);
        $stmt_vac->bind_param($types, ...$values);
    } else {
        // INSERT
        $campos = implode(', ', array_merge(['id_empleado'], $vacunas_campos, $vacunas_fechas));
        $placeholders = implode(', ', array_fill(0, count($vacunas_campos), '?')) . ', ' . implode(', ', array_fill(0, count($vacunas_fechas), '?'));
        $placeholders = '?, ' . $placeholders;
        $types = 'i' . str_repeat('i', count($vacunas_campos)) . str_repeat('s', count($vacunas_fechas));
        $values = array_merge([$id_empleado], array_map(function($c) use ($vacunas_data) { return $vacunas_data[$c]; }, $vacunas_campos), array_map(function($c) use ($vacunas_data) { return $vacunas_data[$c]; }, $vacunas_fechas));
        $sql_vac = "INSERT INTO vacunas ($campos, fecha_creacion, fecha_actualizacion) VALUES ($placeholders, NOW(), NOW())";
        $stmt_vac = $conn->prepare($sql_vac);
        $stmt_vac->bind_param($types, ...$values);
    }
    $stmt_vac->execute();
    $stmt_vac->close();

    // Confirmar todos los cambios
    $conn->commit();

    // Redirigir al siguiente paso (o al panel principal)
    if ($accion === 'guardar_continuar') {
        header("Location: ../../views/registro/paso7.php?id=" . $id_empleado);
    } else if  ($accion === 'guardar_salir') {
        header("Location: ../../views/ver_pacientes.php");
    }
    exit();
} catch (Exception $e) {
    // Revertir cambios en caso de error
    $conn->rollback();
    die("Error al guardar los datos: " . $e->getMessage());
}

$conn->close();