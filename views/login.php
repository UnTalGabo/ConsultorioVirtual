<?php
session_start();
require_once '../php/conexion.php'; // Ajusta la ruta si es necesario

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, rol FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash, $rol);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_rol'] = $rol;
            header('Location: index.php');
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login de Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #f4f4f4; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #fff;
            padding: 2rem 2.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 350px;
        }
    </style>
</head>
<body>
    <div class="login-container mx-auto">
        <h2 class="mb-4 text-center">Iniciar Sesión</h2>
        <?php if ($error) echo "<p class='text-danger text-center'>$error</p>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
        <div class="text-center mt-3">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#registroModal">
                Registrar paciente
            </button>
        </div>
    </div>

    <!-- Modal de registro -->
    <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="registroModalLabel">Registro de paciente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form id="formRegistroPaciente" onsubmit="return redirigirPaso1(event)">
              <div class="mb-3">
                <label for="numeroEmpleadoRegistro" class="form-label">Número de empleado</label>
                <input type="text" class="form-control" id="numeroEmpleadoRegistro" name="numeroEmpleadoRegistro" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Continuar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function redirigirPaso1(event) {
        event.preventDefault();
        const numero = document.getElementById('numeroEmpleadoRegistro').value.trim();
        if (numero) {
            window.location.href = `../views/paso1.php?id=${encodeURIComponent(numero)}`;
        }
        return false;
    }
    </script>
</body>
</html>