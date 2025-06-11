<?php
require_once "../../php/conexion.php";
$id_empleado = isset($_GET['id_empleado']) ? intval($_GET['id_empleado']) : 0;
$existe = false;
if ($id_empleado > 0) {
    $stmt = $conn->prepare("SELECT 1 FROM pacientes WHERE id_empleado = ?");
    $stmt->bind_param("i", $id_empleado);
    $stmt->execute();
    $stmt->store_result();
    $existe = $stmt->num_rows > 0;
    $stmt->close();
}
echo json_encode(['existe' => $existe]);