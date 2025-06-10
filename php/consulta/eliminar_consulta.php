<?php
require_once "../conexion.php";

$id_consulta = isset($_GET['id_consulta']) ? intval($_GET['id_consulta']) : 0;
$id_empleado = isset($_GET['id_empleado']) ? intval($_GET['id_empleado']) : 0;

if ($id_consulta > 0) {
    $stmt = $conn->prepare("DELETE FROM consultas WHERE id_consulta = ?");
    $stmt->bind_param("i", $id_consulta);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Redirige de vuelta al historial del paciente
header("Location: ../../views/consulta/historial.php?id=$id_empleado");
exit;