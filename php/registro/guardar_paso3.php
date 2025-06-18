<?php
require_once "../conexion.php";

// 1. Validar datos recibidos
$id_empleado = $_POST['id_empleado'];
$enfermedades_marcadas = $_POST['enfermedades'] ?? []; // Array de enfermedades marcadas
$accion = $_POST['accion'] ?? '';

// 2. Iniciar transacción para integridad de datos
$conn->begin_transaction();

// 3. Eliminar enfermedades heredadas existentes
$sql_delete = "DELETE FROM enfermedades_heredo WHERE id_empleado = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $id_empleado);
$stmt_delete->execute();
$stmt_delete->close();

try {

    // 4. Insertar nuevas enfermedades marcadas
    $sql_insert = "INSERT INTO enfermedades_heredo (id_empleado, enfermedad, parentesco, tipo) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    foreach ($enfermedades_marcadas as $enfermedad) {
        $nombre_real = $_POST["nombre_enfermedad_$enfermedad"] ?? $enfermedad;
        $parentesco = $_POST[$enfermedad . '_quien'] ?? null;
        $tipo = null;

        // Guardar el tipo para las enfermedades que lo requieren
        if ($enfermedad === 'corazon') {
            $tipo = $_POST['corazon_tipo'] ?? null;
        } elseif ($enfermedad === 'pulmonares') {
            $tipo = $_POST['pulmonares_tipo'] ?? null;
        } elseif ($enfermedad === 'rinon') {
            $tipo = $_POST['rinon_tipo'] ?? null;
        } elseif ($enfermedad === 'higado') {
            $tipo = $_POST['higado_tipo'] ?? null;
        } elseif ($enfermedad === 'alergias') {
            $tipo = $_POST['alergias_tipo'] ?? null;
        } elseif ($enfermedad === 'tumores') {
            $tipo = $_POST['tumores_tipo'] ?? null;
        }

        if (!empty($parentesco)) {
            $stmt_insert->bind_param("isss", $id_empleado, $nombre_real, $parentesco, $tipo);
            $stmt_insert->execute();
        }
    }

    $conn->commit(); // Confirmar cambios
    if ($accion === 'guardar_salir') {
        header("Location: ../../views/ver_pacientes.php");
    } else {
        header("Location: ../../views/registro/paso4.php?id=$id_empleado");
    }
    exit;
} catch (Exception $e) {
    $conn->rollback(); // Revertir en caso de error
    die("Error al guardar: " . $e->getMessage());
}

$stmt_delete->close();
$stmt_insert->close();
$conn->close();