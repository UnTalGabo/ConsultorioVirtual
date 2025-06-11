<?php
session_start();
if (
  !isset($_SESSION['usuario_rol']) ||
  !in_array($_SESSION['usuario_rol'], ['doctor', 'admin'])
) {
  header('Location: login.php');
  exit();
}

$orden = $_GET['orden'] ?? 'nombre_completo';

require_once "../php/conexion.php";

$sql = "SELECT * FROM pacientes ORDER BY $orden ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Pacientes Registrados</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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

    .navbar-brand,
    .navbar-brand i {
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

    .volver-btn {
      margin-bottom: 1.5rem;
      font-size: 1.1rem;
      padding: 0.5rem 1.5rem;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .table thead th {
      background: #2e3c81 !important;
      color: #fff;
      font-weight: 600;
      letter-spacing: 0.5px;
      border-top: none;
    }

    .table-striped>tbody>tr:nth-of-type(odd) {
      background-color: #f4f6fa;
    }

    .table-bordered {
      border-radius: 12px;
      overflow: hidden;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
      border-radius: 6px !important;
      margin: 0 2px;
      border: none !important;
      background: #e9ecef !important;
      color: #2e3c81 !important;
      font-weight: 500;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background: #2e3c81 !important;
      color: #fff !important;
    }

    .dataTables_wrapper .dataTables_filter input {
      border-radius: 8px;
      border: 1px solid #2e3c81;
      padding: 0.4rem 0.8rem;
      margin-left: 0.5em;
    }

    .dataTables_length select {
      border-radius: 8px;
      border: 1px solid #2e3c81;
      padding: 0.2rem 0.6rem;
      margin-left: 0.5em;
    }

    .btn-editar,
    .btn-info,
    .btn-danger,
    .btn-primary {
      border-radius: 6px;
      font-size: 0.98rem;
      font-weight: 500;
      margin-right: 0.2rem;
      margin-bottom: 0.2rem;
    }

    .btn-editar {
      background: #ffe066;
      color: #7c6f00;
      border: none;
    }

    .btn-editar:hover {
      background: #fff3bf;
      color: #7c6f00;
    }

    .btn-info {
      background: #2e3c81;
      color: #fff;
      border: none;
    }

    .btn-info:hover {
      background: #1e2a78;
      color: #fff;
    }

    .btn-danger {
      background: #e03131;
      color: #fff;
      border: none;
    }

    .btn-danger:hover {
      background: #c92a2a;
      color: #fff;
    }

    .btn-primary {
      background: #228be6;
      border: none;
    }

    .btn-primary:hover {
      background: #1864ab;
    }

    .text-nowrap {
      white-space: nowrap;
    }

    @media (max-width: 991px) {
      .main-container {
        margin-top: 20px;
      }

      .card {
        padding: 0.5rem;
      }

      .table-responsive {
        font-size: 0.97rem;
      }
    }
  </style>
</head>

<body>
  <!-- Barra de navegación superior -->
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
        <i class="bi bi-hospital-fill fs-3"></i>
        Consultorio Virtual
      </a>
      <div class="ms-auto d-flex gap-3">
        <a href="../views/index.php" class="nav-link text-white">Inicio</a>
        <a href="../views/ver_pacientes.php" class="nav-link text-white fw-bold">Pacientes</a>
        <a href="../php/logout.php" class="nav-link text-white">Cerrar sesión</a>
      </div>
    </div>
  </nav>

  <div class="main-container">
    <div class="card p-4 p-md-5">
      <h2 class="text-center mb-4 fw-bold text-primary">
        <i class="bi bi-people-fill me-2"></i>
        Pacientes Registrados
      </h2>

      <?php if ($resultado->num_rows > 0): ?>
        <div class="row mb-3">
          <div class="col-md-6">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre o número de empleado...">
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered align-middle" id="tablaPacientes" style="width:100%">
            <thead>
              <tr>
                <th>ID Empleado</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Departamento</th>
                <th>Acciones</th>
                <th>PDF</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $fila['id_empleado']; ?></td>
                  <td><?php echo $fila['nombre_completo']; ?></td>
                  <td><?php echo $fila['telefono']; ?></td>
                  <td><?php echo $fila['departamento']; ?></td>
                  <td class="text-nowrap">
                    <div class="d-flex flex-column flex-wrap" style="min-width: 180px;">
                      <div class="d-flex flex-row mb-1 gap-1">
                        <button
                          class="btn btn-editar btn-sm flex-fill"
                          data-id="<?php echo $fila['id_empleado']; ?>"
                          data-genero="<?php echo strtolower($fila['genero']); ?>"
                          data-nombre="<?php echo htmlspecialchars($fila['nombre_completo']); ?>"
                          type="button"
                          data-bs-toggle="modal"
                          data-bs-target="#modalEditar"><i class="bi bi-pencil-square"></i> Editar</button>

                      </div>
                      <div class="d-flex flex-row gap-1">
                        <a href="../views/consulta/historial.php?id=<?php echo $fila['id_empleado']; ?>" class="btn btn-secondary btn-sm flex-fill">
                          <i class="bi bi-journal-medical"></i> Ver Consultas
                        </a>
                        <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                          <a href="../php/registro/eliminar_paciente.php?id=<?php echo $fila['id_empleado']; ?>" class="btn btn-danger btn-sm flex-fill" onclick="return confirm('¿Seguro que deseas eliminar este paciente y todos sus datos?');">
                            <i class="bi bi-trash"></i> Eliminar
                          </a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td>
                    <a href="../php/crear_pdf.php?id=<?php echo $fila['id_empleado']; ?>" class="btn btn-primary btn-sm" target="_blank">
                      <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>
                  </td>
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

  <!-- Modal para elegir sección a editar -->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarLabel">¿Qué sección deseas editar?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div id="nombrePaciente" class="mb-3 fw-bold text-primary"></div>
          <div class="d-grid gap-2">
            <a id="btnDatosGenerales" class="btn btn-outline-primary" href="#">Datos Generales</a>
            <a id="btnHeredoFamiliares" class="btn btn-outline-secondary" href="#">Antecedentes Heredo-Familiares</a>
            <a id="btnNoPatologicos" class="btn btn-outline-success" href="#">Antecedentes Personales No Patológicos</a>
            <a id="btnGinecoObstetricos" class="btn btn-outline-warning" href="#" style="display:none;">Antecedentes Gineco-Obstétricos</a>
            <a id="btnPatologicos" class="btn btn-outline-danger" href="#">Antecedentes Patológicos</a>
            <a id="btnMedicoLaborales" class="btn btn-outline-info" href="#">Antecedentes Médico-Laborales</a>
            <a id="btnExamenMedico" class="btn btn-outline-primary" href="#">Examen Médico</a>
          </div>
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
    // Buscar en tiempo real al escribir
    document.getElementById("buscador").addEventListener("keyup", buscarPaciente);
  </script>
  <script>
    // Modal editar lógica
    let idSeleccionado = null;
    let generoSeleccionado = null;
    let nombreSeleccionado = null;

    document.querySelectorAll('.btn-editar').forEach(btn => {
      btn.addEventListener('click', function() {
        idSeleccionado = this.getAttribute('data-id');
        generoSeleccionado = this.getAttribute('data-genero');
        nombreSeleccionado = this.getAttribute('data-nombre');
        document.getElementById('nombrePaciente').textContent = nombreSeleccionado;

        // Mostrar botón Gineco-Obstétricos solo si es mujer
        if (generoSeleccionado === 'femenino' || generoSeleccionado === 'mujer') {
          document.getElementById('btnGinecoObstetricos').style.display = '';
        } else {
          document.getElementById('btnGinecoObstetricos').style.display = 'none';
        }

        // Actualiza los hrefs de los botones
        document.getElementById('btnDatosGenerales').href = `../views/registro/paso1.php?id=${idSeleccionado}`;
        document.getElementById('btnHeredoFamiliares').href = `../views/registro/paso3.php?id=${idSeleccionado}`;
        document.getElementById('btnNoPatologicos').href = `../views/registro/paso4.php?id=${idSeleccionado}`;
        document.getElementById('btnGinecoObstetricos').href = `../views/registro/paso5.php?id=${idSeleccionado}`;
        document.getElementById('btnPatologicos').href = `../views/registro/paso6.php?id=${idSeleccionado}`;
        document.getElementById('btnMedicoLaborales').href = `../views/registro/paso7.php?id=${idSeleccionado}`;
        document.getElementById('btnExamenMedico').href = `../views/registro/paso8.php?id=${idSeleccionado}`;
      });
    });
  </script>
</body>

</html>

<?php $conn->close(); ?>