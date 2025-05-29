<?php
require_once "../php/conexion.php";

$sql = "SELECT * FROM pacientes ORDER BY nombre_completo ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Pacientes Registrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Animación de fade-in para el contenedor principal */
    .container {
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

    /* Animación para las filas de la tabla */
    tbody tr {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInRow 0.6s forwards;
    }

    tbody tr:nth-child(1) {
      animation-delay: 0.1s;
    }

    tbody tr:nth-child(2) {
      animation-delay: 0.2s;
    }

    tbody tr:nth-child(3) {
      animation-delay: 0.3s;
    }

    tbody tr:nth-child(4) {
      animation-delay: 0.4s;
    }

    tbody tr:nth-child(5) {
      animation-delay: 0.5s;
    }

    /* ...agrega más si esperas más filas... */
    @keyframes fadeInRow {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Animación al enfocar el input de búsqueda */
    #buscador:focus {
      border-color: #2e3c81;
      box-shadow: 0 0 0 2px #2e3c8133;
      transition: box-shadow 0.3s, border-color 0.3s;
    }
  </style>
</head>

<body class="bg-light">

  <div class="container py-5">
    <h2 class="text-center mb-4 text-primary">Pacientes Registrados</h2>

    <!-- Buscador de pacientes -->
    <div class="row mb-4 justify-content-center">
      <div class="col-md-6">
        <div class="input-group">
          <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre o número de empleado...">
        </div>
      </div>
    </div>
    <!-- Fin buscador -->

    <?php if ($resultado->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle" id="tablaPacientes">
          <thead class="table-primary">
            <tr>
              <th scope="col">ID Empleado</th>
              <th scope="col">Nombre</th>
              <th scope="col">Teléfono</th>
              <th scope="col">Área</th>
              <th scope="col">Acciones</th>
              <th scope="col">PDF</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
              <tr>
                <td><?php echo $fila['id_empleado']; ?></td>
                <td><?php echo $fila['nombre_completo']; ?></td>
                <td><?php echo $fila['telefono']; ?></td>
                <td><?php echo $fila['area']; ?></td>
                <td>
                  <button 
                    class="btn btn-sm btn-warning btn-editar"
                    data-id="<?php echo $fila['id_empleado']; ?>"
                    data-genero="<?php echo strtolower($fila['genero']); ?>"
                    data-nombre="<?php echo htmlspecialchars($fila['nombre_completo']); ?>"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditar"
                  >Editar</button>
                  <a href="paso8.php?id=<?php echo $fila['id_empleado']; ?>" class="btn btn-sm btn-info text-white">Examen Medico</a>
                  <a href="../php/eliminar_paciente.php?id=<?php echo $fila['id_empleado']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este paciente y todos sus datos?');">Eliminar</a>
                </td>
                <td>
                  <a href="../php/crear_pdf.php?id=<?php echo $fila['id_empleado']; ?>" class="btn btn-primary" target="_blank">Generar PDF</a>
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

    <div class="text-center mt-4">
      <a href="../views/index.html" class="btn btn-secondary">Volver al panel principal</a>
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
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        document.getElementById('btnDatosGenerales').href = `registro_paciente.php?id=${idSeleccionado}`;
        document.getElementById('btnHeredoFamiliares').href = `paso3.php?id=${idSeleccionado}`;
        document.getElementById('btnNoPatologicos').href = `paso4.php?id=${idSeleccionado}`;
        document.getElementById('btnGinecoObstetricos').href = `paso5.php?id=${idSeleccionado}`;
        document.getElementById('btnPatologicos').href = `paso6.php?id=${idSeleccionado}`;
        document.getElementById('btnMedicoLaborales').href = `paso7.php?id=${idSeleccionado}`;
      });
    });
  </script>
</body>

</html>

<?php $conn->close(); ?>