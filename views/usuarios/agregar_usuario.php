<?php
session_start();
if (
    !isset($_SESSION['usuario_rol']) ||
    !in_array($_SESSION['usuario_rol'], ['doctor', 'admin'])
) {
    header('Location: login.php');
    exit();
}
require_once '../../php/conexion.php';

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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <i class="bi bi-hospital-fill fs-3"></i>
                Consultorio Virtual
            </a>
            <div class="ms-auto">
                <a href="lista_usuarios.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="bi bi-people"></i> Usuarios
                </a>
                <a href="index.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-house"></i> Inicio
                </a>
            </div>
        </div>
    </nav>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card shadow p-4" style="min-width:350px;max-width:400px;width:100%;">
            <h3 class="mb-4 text-center text-primary"><i class="bi bi-person-plus"></i> Agregar Usuario</h3>
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo strpos($mensaje, 'correctamente') !== false ? 'success' : 'danger'; ?> text-center">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select name="rol" id="rol" class="form-select" required>
                        <option value="doctor">Doctor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Agregar
                    </button>
                    <a href="lista_usuarios.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a usuarios
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>