<?php
require_once "../../php/conexion.php";
session_start();
if (
    !isset($_SESSION['usuario_rol']) ||
    $_SESSION['usuario_rol'] !== 'admin'
) {
    header('Location: ../index.php');
    exit();
}

// Obtener todos los usuarios
$resultado = $conn->query("SELECT id, usuario, rol FROM usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Usuarios registrados</h2>
    <div class="mb-3">
        <a href="agregar_usuario.php" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Agregar usuario
        </a>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($usuario = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                <td class="text-center">
                    <a href="../../php/usuarios/eliminar_usuario.php?id=<?php echo $usuario['id']; ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">
                        Eliminar
                    </a>
                    <a href="../usuarios/cambiar_contrasena.php?id=<?php echo $usuario['id']; ?>" 
                       class="btn btn-warning btn-sm">
                        Cambiar Contraseña
                    </a>
                    <a href="../usuarios/cambiar_rol.php?id=<?php echo $usuario['id']; ?>" 
                       class="btn btn-info btn-sm">
                        Cambiar Rol
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="../index.php" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Volver
</a>
</div>

</body>
</html>
<?php $conn->close(); ?>