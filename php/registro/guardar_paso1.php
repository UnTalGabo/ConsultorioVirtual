<?php
require_once "../conexion.php";

// Obtener datos del formulario
$id_empleado = $_POST['id_empleado'];
$accion = $_POST['accion'] ?? '';
$accion2= $_POST['accion2'] ?? '';
$nombre = $_POST['nombre_completo'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$genero = $_POST['genero'];
$estado_civil = $_POST['estado_civil'];
$tipo_sangre = $_POST['tipo_sangre'] ?? null;
$telefono = $_POST['telefono'];
$calle = $_POST['calle'];
$numero = $_POST['numero'];
$colonia = $_POST['colonia'];
$ciudad = $_POST['ciudad'];
$estado = $_POST['estado'];
$cp = $_POST['cp'];
$escolaridad = $_POST['escolaridad'];
$contacto_emergencia = $_POST['contacto_emergencia'];
$telefono_emergencia = $_POST['telefono_emergencia'];
$parentesco = $_POST['parentesco'];
$area = $_POST['area'];
$puesto = $_POST['puesto'];
$departamento = $_POST['departamento'];

// Validar que no exista ya un paciente con ese ID
$verifica = $conn->query("SELECT * FROM pacientes WHERE id_empleado = $id_empleado");

$verifica = $conn->query("SELECT * FROM pacientes WHERE id_empleado = $id_empleado");

if ($verifica->num_rows > 0 && $accion2 === 'nuevo') {
    // Redirige al formulario con mensaje de error
    header("Location: ../../views/registro/crear_paciente.php?error=existe");
    exit;
} else if ($verifica->num_rows > 0) {
    $sql = "UPDATE pacientes SET 
            nombre_completo = ?, 
            fecha_nacimiento = ?,
            genero = ?,
            estado_civil = ?,
            tipo_sangre = ?,
            telefono = ?,
            calle = ?,
            numero = ?,
            colonia = ?,
            ciudad = ?,
            estado = ?,
            cp = ?,
            escolaridad = ?,
            contacto_emergencia = ?,
            telefono_emergencia = ?,
            parentesco = ?,
            area = ?,
            puesto = ?,
            departamento = ?
            WHERE id_empleado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssssssssssssi",
        $nombre,
        $fecha_nacimiento,
        $genero,
        $estado_civil,
        $tipo_sangre,
        $telefono,
        $calle,
        $numero,
        $colonia,
        $ciudad,
        $estado,
        $cp,
        $escolaridad,
        $contacto_emergencia,
        $telefono_emergencia,
        $parentesco,
        $area,
        $puesto,
        $departamento,
        $id_empleado
    );
    if ($stmt->execute()) {
        if ($accion === 'guardar_salir') {
            header("Location: ../../views/ver_pacientes.php");
        } else {
            header("Location: ../../views/registro/paso2.php?id=$id_empleado");
        }
        exit;
    } else {
        echo "Error al Actualizar: " . $stmt->error;
    }
} else {
    // Si no existe, insertar un nuevo registro
    $sql = "INSERT INTO pacientes (
            id_empleado, nombre_completo, fecha_nacimiento, genero, estado_civil, 
            tipo_sangre, telefono, calle, numero, colonia, ciudad, estado, cp, escolaridad, 
            contacto_emergencia, telefono_emergencia, parentesco, area, puesto, departamento
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isssssssssssssssssss",
        $id_empleado,
        $nombre,
        $fecha_nacimiento,
        $genero,
        $estado_civil,
        $tipo_sangre,
        $telefono,
        $calle,
        $numero,
        $colonia,
        $ciudad,
        $estado,
        $cp,
        $escolaridad,
        $contacto_emergencia,
        $telefono_emergencia,
        $parentesco,
        $area,
        $puesto,
        $departamento
    );
    if ($stmt->execute()) {
        // Redirigir al siguiente paso
        if ($accion === 'guardar_salir') {
            header("Location: ../../views/ver_pacientes.php");
        } else {
            header("Location: ../../views/registro/paso2.php?id=$id_empleado");
        }
        exit;
    } else {
        echo "Error al guardar: " . $stmt->error;
    }
}


$stmt->close();
$conn->close();
