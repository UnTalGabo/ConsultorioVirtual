<?php
require_once "../conexion.php";

$id_consulta = isset($_GET['id_consulta']) ? intval($_GET['id_consulta']) : 0;
$id_empleado = isset($_GET['id_empleado']) ? intval($_GET['id_empleado']) : 0;

if ($id_consulta > 0) {
    $stmt = $conn->prepare("
    SELECT pdf.ruta_pdf 
    FROM consultas 
    JOIN pdf ON consultas.pdf = pdf.id 
    WHERE consultas.id_consulta = ?
");
    $stmt->bind_param("i", $id_consulta);
    $stmt->execute();
    $stmt->bind_result($ruta_pdf);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM consultas WHERE id_consulta = ?");
    $stmt->bind_param("i", $id_consulta);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

if ($ruta_pdf) {
    header('Location: ../eliminar_pdf.php?ruta=' . ($ruta_pdf) . '&accion=consulta');
    exit;
} else {
    // Maneja el caso donde no hay PDF relacionado
    echo "No hay PDF relacionado a esta consulta.";
}
