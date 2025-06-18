<?php
// filepath: c:\xampp\htdocs\ConsultorioVirtual\php\usuarios\cambiar_contrasena.php
session_start();
require_once "../conexion.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: ../../views/usuarios/cambiar_contrasena.php?id=$id&error=ID inválido");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual = $_POST['actual'] ?? '';
    $nueva = $_POST['nueva'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    // Validar campos
    if (!$actual || !$nueva || !$confirmar) {
        header("Location: ../../views/usuarios/cambiar_contrasena.php?id=$id&error=Completa todos los campos.");
        exit;
    }
    if ($nueva !== $confirmar) {
        header("Location: ../../views/usuarios/cambiar_contrasena.php?id=$id&error=Las contraseñas no coinciden.");
        exit;
    }

    // Obtener contraseña actual
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($hash_actual);
    if ($stmt->fetch()) {
        $stmt->close();
        if (!password_verify($actual, $hash_actual)) {
            header("Location: ../../views/usuarios/cambiar_contrasena.php?id=$id&error=La contraseña actual es incorrecta.");
            exit;
        }
        // Actualizar contraseña
        $nuevo_hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt2->bind_param("si", $nuevo_hash, $id);
        if ($stmt2->execute()) {
            $stmt2->close();
            header("Location: ../../views/usuarios/cambiar_contrasena.php?id=$id&success=Contraseña actualizada correctamente.");
            exit;
        } else {
            $stmt2->close();
            header("Location: ../../views/usuarios/cambiar_contrasena.php?id=$id&error=No se pudo actualizar la contraseña.");
            exit;
        }
    } else {
        $stmt->close();
        header("Location: ../../views/usuarios/cambiar_contrasena.php?id=$id&error=Usuario no encontrado.");
        exit;
    }
} else {
    header("Location: ../../views/usuarios/cambiar_contrasena.php?id=$id");
    exit;
}
