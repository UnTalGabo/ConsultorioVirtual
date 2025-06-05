<?php
require_once "../conexion.php";

$id_empleado = $_GET['id'];


$sql = "DELETE FROM pacientes WHERE id_empleado = ?";



$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_empleado);


if ($stmt->execute()) {
    header("Location: ../../views/ver_pacientes.php?msg=eliminado");
    exit;
} else {
    echo "Error al eliminar" . $conn->error;
}

$conn->close();
