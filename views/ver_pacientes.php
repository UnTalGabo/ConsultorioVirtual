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
</head>
<body class="bg-light">

  <div class="container py-5">
    <h2 class="text-center mb-4 text-primary">Pacientes Registrados</h2>

    <?php if ($resultado->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
          <thead class="table-primary">
            <tr>
              <th scope="col">ID Empleado</th>
              <th scope="col">Nombre</th>
              <th scope="col">Teléfono</th>
              <th scope="col">Área</th>
              <th scope="col">Acciones</th>
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
                  
                  <a href="registro_paciente.php?id=<?php echo $fila['id_empleado']; ?>" class="btn btn-sm btn-warning">Editar</a>
                  <a href="paso8.php?id=<?php echo $fila['id_empleado']; ?>" class="btn btn-sm btn-info text-white">Examen Medico</a>
                  <a href="../php/eliminar_paciente.php?id=<?php echo $fila['id_empleado']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este paciente y todos sus datos?');">Eliminar</a>
                </td>
                <td>
                  <a href="../php/crear_pdf.php?id=<?php echo $fila['id_empleado'];?>" class="btn btn-primary">Generar PDF</a>
                  <!-- Otros botones -->
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>
