<?php
require_once "conexion.php";

// Validar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paso6.php?id=" . $_POST['id_empleado']);
    exit();
}

// Obtener datos del formulario
$id_empleado = $_POST['id_empleado'];
$enfermedades = $_POST['enfermedades'] ?? [];
$fracturas_esguinces = $_POST['fracturas_esguinces'] ?? null;
$cirugias = $_POST['cirugias'] ?? null;
$enfermedad_actual = isset($_POST['enfermedad_actual']) ? 1 : 0;
$enfermedad_actual_desc = $_POST['enfermedad_actual_desc'] ?? null;
$medicamentos = $_POST['medicamentos'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;

// Procesar enfermedades adicionales (si se especificaron)
$enfermedades_extra = [];
if (!empty($_POST["otra_enfermedad_4"])) {
    $enfermedades[] = $_POST["otra_enfermedad_4"];
}

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
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
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

    // Confirmar todos los cambios
    $conn->commit();

    // Redirigir al siguiente paso (o al panel principal)
    header("Location: ../views/paso7.php?id=" . $id_empleado);
    exit();
} catch (Exception $e) {
    // Revertir cambios en caso de error
    $conn->rollback();
    die("Error al guardar los datos: " . $e->getMessage());
}

$conn->close();
