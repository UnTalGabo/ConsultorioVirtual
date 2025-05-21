<?php
require_once "../php/conexion.php";

$id_paciente = isset($_GET['id']) ? intval($_GET['id']) : 0;
$paciente = [];

if ($id_paciente > 0) {
    $sql = "SELECT * FROM pacientes WHERE id_empleado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_paciente);
    $stmt->execute();
    $result = $stmt->get_result();
    $paciente = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Paso 1 - Ficha de Identificación</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6fa;
      color: #1e2a78;
      max-width: 800px;
      margin: auto;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #1e2a78;
    }

    form {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .button-next {
      background-color: #1e2a78;
      color: white;
      border: none;
      padding: 12px 20px;
      margin-top: 20px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }

    .button-next:hover {
      background-color: #16205a;
    }
  </style>
</head>
<body>

  <h2>Paso 1: Ficha de Identificación</h2>

  <form action="..\php\guardar_paso1.php" method="POST">
    <input type="hidden" name="id_empleado" value="<?php echo $id_paciente; ?>">

    <label for="id_empleado">Número de empleado</label>
    <input type="number" name="id_empleado"
      value="<?php echo isset($paciente['id_empleado']) ? $paciente['id_empleado'] : ''; ?>"
      required placeholder="Ej. 1023">

    <label for="nombre_completo">Nombre completo</label>
    <input type="text" name="nombre_completo" 
      value="<?php echo isset($paciente['nombre_completo']) ? $paciente['nombre_completo'] : ''; ?>" 
      required placeholder="Ej. Juan Pérez López">

    <label for="puesto">Puesto</label>
    <input type="text" name="puesto"
      value="<?php echo isset($paciente['puesto']) ? $paciente['puesto'] : ''; ?>"
      required placeholder="Ej. Analista de Sistemas, Jefe de Urgencias">

    <label for="area">Área</label>
    <input type="text" name="area"
      value="<?php echo isset($paciente['area']) ? $paciente['area'] : ''; ?>"
      required placeholder="Ej. Recursos Humanos, Sistemas">

    <label for="fecha_nacimiento">Fecha de nacimiento</label>
    <input type="date" name="fecha_nacimiento"
    value="<?php echo isset($paciente['fecha_nacimiento']) ? $paciente['fecha_nacimiento'] : ''; ?>"
    required>

    <label for="genero">Género</label>
    <select name="genero" required>
      <option value="<?php echo isset($paciente['genero']) ? $paciente['genero'] : ''; ?>">
        <?php echo isset($paciente['genero']) ? $paciente['genero'] : 'Selecciona'; ?>
      </option>
      <option value="Masculino">Masculino</option>
      <option value="Femenino">Femenino</option>
      <option value="Otro">Otro</option>
    </select>

    <label for="estado_civil">Estado civil</label>
    <select name="estado_civil" required>
      <option value="<?php echo isset($paciente['estado_civil']) ? $paciente['estado_civil'] : ''; ?>">
        <?php echo isset($paciente['estado_civil']) ? $paciente['estado_civil'] : 'Selecciona'; ?></option>
      <option value="Soltero/a">Soltero/a</option>
      <option value="Casado/a">Casado/a</option>
      <option value="Divorciado/a">Divorciado/a</option>
      <option value="Viudo/a">Viudo/a</option>
    </select>

    <label for="telefono">Teléfono</label>
    <input type="tel" name="telefono" 
      value="<?php echo isset($paciente['telefono']) ? $paciente['telefono'] : ''; ?>"
    required placeholder="Ej. 4431234567">

    <label for="direccion">Domicilio actual</label>
    <textarea name="direccion" value="<?php echo isset($paciente['direccion']) ? $paciente['direccion'] : ''; ?>"
     rows="3" required placeholder="Calle, número, colonia, ciudad"><?php echo isset($paciente['direccion']) ? $paciente['direccion'] : ''; ?></textarea>

    <label for="escolaridad">Escolaridad Maxima</label>
    <input type="text" name="escolaridad"
      value="<?php echo isset($paciente['escolaridad']) ? $paciente['escolaridad'] : ''; ?>"
     required placeholder="Ej. Preparatoria, Licenciatura...">

    <label for="contacto_emergencia">Contacto en caso de emergencia</label>
    <input type="text" name="contacto_emergencia"
      value="<?php echo isset($paciente['contacto_emergencia']) ? $paciente['contacto_emergencia'] : ''; ?>"
     required placeholder="Nombre completo de la persona">

    <label for="telefono_emergencia">Teléfono de emergencia</label>
    <input type="tel" name="telefono_emergencia"
      value="<?php echo isset($paciente['telefono_emergencia']) ? $paciente['telefono_emergencia'] : ''; ?>"
     required placeholder="Ej. 4431234567">

    <label for="parentesco">Parentesco</label>
    <input type="text" name="parentesco"
      value="<?php echo isset($paciente['parentesco']) ? $paciente['parentesco'] : ''; ?>"
     required placeholder="Ej. Hermano, Madre, Amigo">

    

    <button class="button-next" type="submit">Siguiente &raquo;</button>
  </form>

</body>
</html>
