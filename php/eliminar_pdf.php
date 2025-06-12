<?php
require_once 'conexion.php';

if (!isset($_GET['ruta']) || empty($_GET['ruta'])) {
    header('Location: ../views/registro/historial_examenes.php?error=Ruta no especificada');
    exit;
}
if (!isset($_GET['accion']) || empty($_GET['accion'])) {
}


$ruta_relativa = $_GET['ruta'];
$accion = $_GET['accion'];

$stmt = $conn->prepare("SELECT id_empleado FROM pdf WHERE ruta_pdf = ?");
$stmt->bind_param("s", $ruta_relativa);
$stmt->execute();
$result = $stmt->get_result();
$paciente = $result->fetch_assoc();

// Eliminar de la base de datos
$stmt = $conn->prepare("DELETE FROM pdf WHERE ruta_pdf = ?");
$stmt->bind_param("s", $ruta_relativa);
$stmt->execute();
$stmt->close();

// Quitar el prefijo 'consultoriovirtual/' si existe
$ruta_relativa_limpia = preg_replace('#^consultoriovirtual/#i', '', ltrim($ruta_relativa, '/\\'));

// Construir la ruta absoluta al archivo
$ruta_fisica = realpath(__DIR__ . '/../' . $ruta_relativa_limpia);

// Si realpath falla, intenta construir la ruta manualmente
if (!$ruta_fisica) {
    $ruta_fisica = __DIR__ . '/../' . $ruta_relativa_limpia;
}

if (file_exists($ruta_fisica)) {
    unlink($ruta_fisica);
}

// Redirigir de vuelta al historial
if ($accion == 'examen') {
    header('Location: ../views/registro/historial_examenes.php?id=' . $paciente['id_empleado']);
    exit;
} else if ($accion == 'consulta') {
    header('Location: ../views/consulta/historial.php?id=' . $paciente['id_empleado']);
    exit;
}
