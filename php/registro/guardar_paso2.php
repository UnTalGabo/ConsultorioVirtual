<?php
require_once "../conexion.php";

// 1. Validar datos recibidos
if (!isset($_POST['id_empleado'])) {
    die("Error: ID de empleado no proporcionado");
}

$id_empleado = $_POST['id_empleado'];
$acepta_terminos = isset($_POST['acepta_terminos']) ? 1 : 0;

// 2. Actualizar la base de datos
$sql = "UPDATE pacientes SET acepta_terminos = ? WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $acepta_terminos, $id_empleado);

if ($stmt->execute()) {
    // 3. Redirigir al siguiente paso con el ID
    header("Location: ../../views/registro/paso3.php?id=" . $id_empleado);
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
