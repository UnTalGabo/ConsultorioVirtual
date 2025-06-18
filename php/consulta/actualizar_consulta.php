<?php
require_once "../conexion.php";

$id_consulta = $_POST['id_consulta'] ?? 0;
$id_empleado = $_POST['id_empleado'] ?? 0;
$evaluacion_fisica = $_POST['evaluacion_fisica'] ?? '';

if ($id_consulta > 0) {
    // Actualiza solo el campo evaluacion_fisica
    $stmt = $conn->prepare("UPDATE consultas SET evaluacion_fisica = ? WHERE id_consulta = ?");
    $stmt->bind_param("si", $evaluacion_fisica, $id_consulta);
    $stmt->execute();
    $stmt->close();

    // Redirige a pdf_consulta.php para regenerar el PDF (esto elimina el anterior y crea el nuevo)
    header("Location: ../pdf_consulta.php?id=$id_consulta&redir=1");
    exit;
} else {
    header("Location: ../../views/consulta/historial.php?id=$id_empleado&error=Consulta no válida");
    exit;
}
?>