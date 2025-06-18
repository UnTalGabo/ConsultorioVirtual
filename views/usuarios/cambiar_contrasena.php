<?php
session_start();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
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
            <h3 class="mb-4 text-center text-primary"><i class="bi bi-key"></i> Cambiar Contraseña</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form action="../../php/usuarios/cambiar_contrasena.php?id=<?php echo $id; ?>" method="post" autocomplete="off">
                <div class="mb-3">
                    <label for="actual" class="form-label">Contraseña Actual</label>
                    <input type="password" id="actual" name="actual" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="nueva" class="form-label">Nueva Contraseña</label>
                    <input type="password" id="nueva" name="nueva" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" id="confirmar" name="confirmar" class="form-control" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-key"></i> Cambiar Contraseña
                    </button>
                    <a href="lista_usuarios.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a usuarios
                    </a>
                </div>
            </form>
        </div>
    </div>