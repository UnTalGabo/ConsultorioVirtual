<?php
// filepath: c:\xampp\htdocs\ConsultorioVirtual\php\usuarios\cambiar_rol.php
session_start();
require_once "../conexion.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: ../../views/usuarios/cambiar_rol.php?id=$id&error=ID inválido");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol = $_POST['rol'] ?? '';

    if (!in_array($rol, ['doctor', 'admin'])) {
        header("Location: ../../views/usuarios/cambiar_rol.php?id=$id&error=Rol no válido");
        exit;
    }

    $stmt = $conn->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
    $stmt->bind_param("si", $rol, $id);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../../views/usuarios/cambiar_rol.php?id=$id&rol=$rol&success=Rol actualizado correctamente.");
        exit;
    } else {
        $stmt->close();
        header("Location: ../../views/usuarios/cambiar_rol.php?id=$id&rol=$rol&error=No se pudo actualizar el rol.");
        exit;
    }
} else {
    header("Location: ../../views/usuarios/cambiar_rol.php?id=$id&rol=$rol_actual");
    exit;
}