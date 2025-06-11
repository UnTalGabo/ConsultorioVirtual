<?php
session_start();
if (
    !isset($_SESSION['usuario_rol']) ||
    !in_array($_SESSION['usuario_rol'], ['doctor', 'admin'])
) {
    header('Location: login.php');
    exit();
}
require_once '../php/conexion.php'; // Ajusta la ruta si tu archivo de conexión está en otra carpeta

// Solo admin puede agregar usuarios
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: index.php');
    exit();
}

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
            $mensaje = "Usuario agregado correctamente.";
        } else {
            $mensaje = "Error: El usuario ya existe o hubo un problema.";
        }
        $stmt->close();
    } else {
        $mensaje = "Completa todos los campos correctamente.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
</head>
<body>
    <h2>Agregar Usuario</h2>
    <?php if ($mensaje) echo "<p>$mensaje</p>"; ?>
    <form method="post">
        Usuario: <input type="text" name="usuario" required><br>
        Contraseña: <input type="password" name="password" required><br>
        Rol:
        <select name="rol" required>
            <option value="doctor">Doctor</option>
            <option value="admin">Admin</option>
        </select><br>
        <button type="submit">Agregar</button>
    </form>
    <a href="index.php">Volver al inicio</a>
</body>
</html>