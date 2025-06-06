<?php
session_start();
if (
    !isset($_SESSION['usuario_rol']) ||
    !in_array($_SESSION['usuario_rol'], ['doctor', 'admin'])
) {
    header('Location: login.php');
    exit();
}

require_once "../../php/conexion.php";

$sql = "SELECT * FROM pacientes ORDER BY nombre_completo ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Seleccionar Paciente</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #e9ecef 0%, #f4f6fa 100%);
      font-family: 'Segoe UI', Arial, sans-serif;
      color: #1e2a78;
    }
    .navbar {
      background: #1e2a78;
    }
    .navbar-brand, .navbar-brand i {
      color: #fff !important;
      font-weight: 600;
      letter-spacing: 1px;
    }
    .main-container {
      max-width: 1100px;
      margin: 40px auto 0 auto;
      padding: 0 15px;
    }
    .card {
      border: none;
      border-radius: 18px;
      box-shadow: 0 6px 32px 0 rgba(30, 42, 120, 0.10), 0 1.5px 6px 0 rgba(30, 42, 120, 0.04);
      background: #fff;
      animation: fadeInUp 0.8s cubic-bezier(.39, .575, .565, 1.000);
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .table-hover tbody tr {
      cursor: pointer;
      transition: background 0.2s;
    }
    .table-hover tbody tr:hover {
      background-color: #e9ecef !important;
    }
  </style>
</head>

<body>
  <!-- Barra de navegación superior -->
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
        <i class="bi bi-hospital-fill fs-3"></i>
        Consultorio Virtual
      </a>
      <div class="ms-auto d-flex gap-3">
        <a href="../views/index.php" class="nav-link text-white">Inicio</a>
        <a href="../php/logout.php" class="nav-link text-white">Cerrar sesión</a>
      </div>
    </div>
  </nav>

  <div class="main-container">
    <div class="card p-4 p-md-5">
      <h2 class="text-center mb-4 fw-bold text-primary">
        <i class="bi bi-people-fill me-2"></i>
        Seleccionar Paciente
      </h2>

      <?php if ($resultado->num_rows > 0): ?>
        <div class="row mb-3">
          <div class="col-md-6">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre o número de empleado...">
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered align-middle table-hover" id="tablaPacientes" style="width:100%">
            <thead>
              <tr>
                <th>ID Empleado</th>
                <th>Nombre</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr class="fila-paciente"
                  data-id="<?php echo $fila['id_empleado']; ?>"
                  data-nombre="<?php echo htmlspecialchars($fila['nombre_completo']); ?>"
                  data-departamento="<?php echo htmlspecialchars($fila['departamento']); ?>">
                  <td><?php echo $fila['id_empleado']; ?></td>
                  <td><?php echo $fila['nombre_completo']; ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-warning text-center">
          No hay pacientes registrados aún.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal de confirmación -->
  <div class="modal fade" id="modalConfirmar" tabindex="-1" aria-labelledby="modalConfirmarLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalConfirmarLabel">Generar nueva consulta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p><strong>Nombre:</strong> <span id="modalNombre"></span></p>
          <p><strong>ID Empleado:</strong> <span id="modalId"></span></p>
          <p><strong>Departamento:</strong> <span id="modalDepartamento"></span></p>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnNuevaConsulta" class="btn btn-primary">Nueva consulta</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Buscador de pacientes por nombre o número de empleado
    function buscarPaciente() {
      var input = document.getElementById("buscador");
      var filtro = input.value.toLowerCase();
      var tabla = document.getElementById("tablaPacientes");
      var filas = tabla.getElementsByTagName("tr");
      for (var i = 1; i < filas.length; i++) { // Empieza en 1 para saltar el encabezado
        var celdaNombre = filas[i].getElementsByTagName("td")[1]; // Columna de nombre
        var celdaId = filas[i].getElementsByTagName("td")[0]; // Columna de ID empleado
        if (celdaNombre && celdaId) {
          var textoNombre = celdaNombre.textContent || celdaNombre.innerText;
          var textoId = celdaId.textContent || celdaId.innerText;
          if (
            textoNombre.toLowerCase().indexOf(filtro) > -1 ||
            textoId.toLowerCase().indexOf(filtro) > -1
          ) {
            filas[i].style.display = "";
          } else {
            filas[i].style.display = "none";
          }
        }
      }
    }
    document.getElementById("buscador").addEventListener("keyup", buscarPaciente);

    // Modal de confirmación al seleccionar paciente
    let pacienteSeleccionado = { id: null, nombre: '', departamento: '' };
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmar'));

    document.querySelectorAll('.fila-paciente').forEach(function(row) {
      row.addEventListener('click', function() {
        pacienteSeleccionado.id = this.getAttribute('data-id');
        pacienteSeleccionado.nombre = this.getAttribute('data-nombre');
        pacienteSeleccionado.departamento = this.getAttribute('data-departamento');
        document.getElementById('modalNombre').textContent = pacienteSeleccionado.nombre;
        document.getElementById('modalId').textContent = pacienteSeleccionado.id;
        document.getElementById('modalDepartamento').textContent = pacienteSeleccionado.departamento;
        modal.show();
      });
    });

    document.getElementById('btnNuevaConsulta').addEventListener('click', function() {
      if (pacienteSeleccionado.id) {
        window.location.href = 'paso1.php?id=' + pacienteSeleccionado.id;
      }
    });
  </script>
</body>
</html>

<?php $conn->close(); ?>