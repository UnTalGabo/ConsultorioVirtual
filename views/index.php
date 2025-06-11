<?php
session_start();
if (
    !isset($_SESSION['usuario_rol']) ||
    !in_array($_SESSION['usuario_rol'], ['doctor', 'admin'])
) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Consultorio Médico</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #e0e7ff 0%, #f9fafb 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      color: #2e3c81;
    }

    .navbar {
      background-color: #2e3c81;
    }

    .navbar-brand,
    .navbar-nav .nav-link {
      color: #fff !important;
      font-weight: 500;
    }

    .main-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 24px rgba(30, 42, 120, 0.08);
      padding: 40px 32px;
      margin-top: 48px;
      animation: fadeInContainer 0.8s cubic-bezier(.39, .575, .565, 1.000);
    }

    @keyframes fadeInContainer {
      from {
        opacity: 0;
        transform: translateY(40px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .option-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: #1e2a78;
      color: #fff;
      border-radius: 12px;
      padding: 32px 0 20px 0;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      transition: background 0.2s, transform 0.15s;
      box-shadow: 0 2px 8px rgba(30, 42, 120, 0.07);
    }

    .option-btn:hover {
      background: #0d1a5a;
      transform: translateY(-4px) scale(1.04);
      color: #fff;
      text-decoration: none;
    }

    .option-btn i {
      font-size: 2.5rem;
      margin-bottom: 12px;
    }

    footer {
      margin-top: 60px;
      text-align: center;
      font-size: 13px;
      color: #888;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="agregar_usuario.php">
        <i class="bi bi-hospital"></i> Consultorio Médico
      </a>
      <div class="d-flex">
        <a href="../php/logout.php" class="btn btn-outline-light ms-3">
          <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </a>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="main-card mx-auto" style="max-width: 600px;">
      <h2 class="mb-4 text-center" style="color:#1e2a78;">
        <i class="bi bi-person-vcard"></i> Panel Principal
      </h2>
      <div class="row g-4">
        <div class="col-12 col-md-6">
          <a href="../views/registro/crear_paciente.php" class="option-btn">
            <i class="bi bi-person-plus"></i>
            Registrar Paciente
          </a>
        </div>
        <div class="col-12 col-md-6">
          <a href="ver_pacientes.php" class="option-btn">
            <i class="bi bi-people"></i>
            Ver Pacientes
          </a>
        </div>
        <div class="col-12 col-md-12">
          <a href="consulta/seleccionar_paciente.php" class="option-btn">
            <i class="bi bi-journal-medical"></i>
            Nueva Consulta
          </a>
        </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Actualizar Paciente -->
  <div class="modal fade" id="modalActualizarPaciente" tabindex="-1" aria-labelledby="modalActualizarPacienteLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formActualizarPaciente" autocomplete="off">
          <div class="modal-header">
            <h5 class="modal-title" id="modalActualizarPacienteLabel">Actualizar Paciente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="numeroEmpleado" class="form-label">Número de empleado</label>
              <input type="text" class="form-control" id="numeroEmpleado" name="numeroEmpleado" required autofocus>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Buscar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer>
    Sistema desarrollado por Gabriel Orozco - Prototipo
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('formActualizarPaciente').addEventListener('submit', function (e) {
      e.preventDefault();
      const numeroEmpleado = document.getElementById('numeroEmpleado').value.trim();
      if (numeroEmpleado) {
        window.location.href = '../views/registro/paso1.php?id=' + encodeURIComponent(numeroEmpleado);
      }
    });
  </script>
</body>

</html>