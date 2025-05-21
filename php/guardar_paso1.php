<?php
require_once "conexion.php";

// Obtener datos del formulario
$id_empleado = $_POST['id_empleado'];
$nombre = $_POST['nombre_completo'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$genero = $_POST['genero'];
$estado_civil = $_POST['estado_civil'];
$telefono = $_POST['telefono'];
$direccion = $_POST['direccion'];
$escolaridad = $_POST['escolaridad'];
$contacto_emergencia = $_POST['contacto_emergencia'];
$telefono_emergencia = $_POST['telefono_emergencia'];
$parentesco = $_POST['parentesco'];
$area = $_POST['area'];
$puesto = $_POST['puesto'];

// Validar que no exista ya un paciente con ese ID
$verifica = $conn->query("SELECT * FROM pacientes WHERE id_empleado = $id_empleado");

if ($verifica->num_rows > 0) {
    $sql = "UPDATE pacientes SET 
            nombre_completo = ?, 
            fecha_nacimiento = ?,
            genero = ?
            estado_civil = ?,
            telefono = ?,
            direccion = ?,
            escolaridad = ?,
            contacto_emergencia = ?,
            telefono_emergencia = ?,
            parentesco = ?,
            area = ?,
            puesto = ?
            WHERE id_empleado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $nombre, $fecha_nacimiento, $genero, $estado_civil,
    $telefono, $direccion, $escolaridad, $contacto_emergencia, $telefono_emergencia, $parentesco, $area, $puesto, $id_empleado);
    if ($stmt->execute()) {
    // Redirigir al siguiente paso
    header("Location: ../views/paso2.php?id=" . $id_empleado);
    exit;
} else {
    echo "Error al Actualizar: " . $stmt->error;
}
} else {
    // Si no existe, insertar un nuevo registro
    $sql = "INSERT INTO pacientes (
            id_empleado, nombre_completo, fecha_nacimiento, genero, estado_civil, 
            telefono, direccion, escolaridad, contacto_emergencia, telefono_emergencia, parentesco, area, puesto
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssssss", 
    $id_empleado, $nombre, $fecha_nacimiento, $genero, $estado_civil,
    $telefono, $direccion, $escolaridad, $contacto_emergencia, $telefono_emergencia,
    $parentesco, $area, $puesto);
    if ($stmt->execute()) {
    // Redirigir al siguiente paso
    header("Location: ../views/paso2.php?id=" . $id_empleado);
    exit;
} else {
    echo "Error al guardar: " . $stmt->error;
}
}


$stmt->close();
$conn->close();
?>
