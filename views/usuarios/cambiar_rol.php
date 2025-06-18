<?php
// filepath: c:\xampp\htdocs\ConsultorioVirtual\views\usuarios\cambiar_rol.php
session_start();
if (
    !isset($_SESSION['usuario_rol']) ||
    $_SESSION['usuario_rol'] !== 'admin'
) {
    header('Location: ../index.php');
    exit();
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$rol_actual = $_GET['rol'] ?? '';
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Rol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
        }
        .card {
            border-radius: 16px;
        }
        .navbar {
            border-radius: 0 0 12px 12px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
                <i class="bi bi-hospital-fill fs-3"></i>
                Consultorio Virtual
            </a>
            <div class="ms-auto">
                <a href="lista_usuarios.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="bi bi-people"></i> Usuarios
                </a>
                <a href="../index.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-house"></i> Inicio
                </a>
            </div>
        </div>
    </nav>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card shadow p-4" style="min-width:350px;max-width:400px;width:100%;">
            <h3 class="mb-4 text-center text-primary"><i class="bi bi-person-gear"></i> Cambiar Rol</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form action="../../php/usuarios/cambiar_rol.php?id=<?php echo $id; ?>" method="post" autocomplete="off">
                <div class="mb-3">
                    <label for="rol" class="form-label">Nuevo Rol</label>
                    <select name="rol" id="rol" class="form-select" required>
                        <option value="doctor" <?php if($rol_actual=='doctor') echo 'selected'; ?>>Doctor</option>
                        <option value="admin" <?php if($rol_actual=='admin') echo 'selected'; ?>>Admin</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-person-gear"></i> Cambiar Rol
                    </button>
                    <a href="lista_usuarios.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a usuarios
                    </a>
                </div>
            </form>
        </div>
    </div>