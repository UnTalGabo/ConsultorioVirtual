<?php
session_start();
if (
    !isset($_SESSION['usuario_rol']) ||
    $_SESSION['usuario_rol'] !== 'admin'
) {
    header('Location: ../../views/usuarios/agregar_usuario.php?error=No autorizado');
    exit();
}

require_once '../../php/conexion.php';

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    if ($usuario && $password && in_array($rol, ['doctor', 'admin'])) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario, $hash, $rol);

        if ($stmt->execute()) {
            header("Location: ../../views/usuarios/agregar_usuario.php?msg=Usuario agregado correctamente.");
            exit();
        } else {
            header("Location: ../../views/usuarios/agregar_usuario.php?error=El usuario ya existe o hubo un problema.");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: ../../views/usuarios/agregar_usuario.php?error=Completa todos los campos correctamente.");
        exit();
    }
}
header("Location: ../../views/usuarios/agregar_usuario.php");
exit();
?>