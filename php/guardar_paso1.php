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
$parentesco = $_POST['parentesco'];
$departamento = $_POST['departamento'];

// Validar que no exista ya un paciente con ese ID
$verifica = $conn->query("SELECT * FROM pacientes WHERE id_empleado = $id_empleado");

if ($verifica->num_rows > 0) {
    echo "Ya existe un paciente con ese número de empleado.";
    exit;
}

// Insertar en la base de datos
$sql = "INSERT INTO pacientes (
            id_empleado, nombre_completo, fecha_nacimiento, genero, estado_civil, 
            telefono, direccion, escolaridad, puesto, contacto_emergencia, parentesco, departamento
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isssssssssss", 
    $id_empleado, $nombre, $fecha_nacimiento, $genero, $estado_civil,
    $telefono, $direccion, $escolaridad, $puesto, $contacto_emergencia,
    $parentesco, $departamento
);

if ($stmt->execute()) {
    // Redirigir al siguiente paso
    header("C:\xampp\htdocs\ConsultorioVirtual\views\index.htmlation: paso2_no_patologicos.html");
    exit;
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
