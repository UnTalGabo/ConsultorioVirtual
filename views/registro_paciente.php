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
  <title><?php echo $id_paciente ? 'Editar' : 'Nuevo'; ?> Paciente - Paso 1</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6fa;
      color: #1e2a78;
      max-width: 800px;
      margin: 20px auto;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #1e2a78;
      margin-bottom: 30px;
    }

    .form-container {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    /* Animación de fade-in para el contenedor del formulario */
    .form-container {
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

    /* Animación de entrada para cada sección */
    .form-section {
      opacity: 0;
      transform: translateY(30px);
      animation: sectionFadeIn 0.7s forwards;
    }

    .form-section:nth-child(1) {
      animation-delay: 0.1s;
    }

    .form-section:nth-child(2) {
      animation-delay: 0.2s;
    }

    .form-section:nth-child(3) {
      animation-delay: 0.3s;
    }

    .form-section:nth-child(4) {
      animation-delay: 0.4s;
    }

    .form-section:nth-child(5) {
      animation-delay: 0.5s;
    }

    @keyframes sectionFadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Animación al enfocar inputs */
    input:focus,
    select:focus,
    textarea:focus {
      outline: none;
      border-color: #2e3c81;
      box-shadow: 0 0 0 2px #2e3c8133;
      transition: box-shadow 0.3s, border-color 0.3s;
    }

    /* Animación de botón al hacer clic */
    .button-next:active,
    .btn-salir:active {
      transform: scale(0.97);
      transition: transform 0.1s;
    }

    .form-section h3 {
      color: #2e3c81;
      margin-top: 0;
      margin-bottom: 15px;
      padding-bottom: 8px;
      border-bottom: 2px solid #2e3c81;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-bottom: 15px;
    }

    .form-group {
      flex: 1;
      min-width: 200px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #1e2a78;
    }

    input,
    select,
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
      font-size: 14px;
    }

    .button-container {
      display: flex;
      justify-content: space-between;
      margin-top: 25px;
    }

    .button-next {
      background-color: #2e3c81;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
    }

    .button-next:hover {
      background-color: #1e2a78;
    }

    .btn-salir {
      background-color: #dc3545;
      color: white;
      padding: 12px 20px;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s;
    }

    .btn-salir:hover {
      background-color: #c82333;
    }
  </style>
</head>

<body>

  <h2><?php echo $id_paciente ? 'Editar' : 'Nuevo'; ?> Paciente - Información Básica</h2>

  <div class="form-container">
    <form action="../php/guardar_paso1.php" method="POST">
      <input type="hidden" name="id_empleado" value="<?php echo $id_paciente; ?>">

      <!-- Sección 1: Datos del Empleado -->
      <div class="form-section">
        <h3>Datos del Empleado</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="id_empleado">Número de empleado</label>
            <input type="number" name="id_empleado"
              value="<?php echo isset($paciente['id_empleado']) ? $paciente['id_empleado'] : ''; ?>"
              required placeholder="Ej. 1023">
          </div>

          <div class="form-group">
            <label for="puesto">Puesto</label>
            <input type="text" name="puesto"
              value="<?php echo isset($paciente['puesto']) ? $paciente['puesto'] : ''; ?>"
              required placeholder="Ej. Analista de Sistemas">
          </div>

          <div class="form-group">
            <label for="area">Área/Departamento</label>
            <input type="text" name="area"
              value="<?php echo isset($paciente['area']) ? $paciente['area'] : ''; ?>"
              required placeholder="Ej. Recursos Humanos">
          </div>
        </div>
      </div>

      <!-- Sección 2: Datos Personales -->
      <div class="form-section">
        <h3>Datos Personales</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="nombre_completo">Nombre completo</label>
            <input type="text" name="nombre_completo"
              value="<?php echo isset($paciente['nombre_completo']) ? $paciente['nombre_completo'] : ''; ?>"
              required placeholder="Ej. Juan Pérez López">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento"
              value="<?php echo isset($paciente['fecha_nacimiento']) ? $paciente['fecha_nacimiento'] : ''; ?>"
              required>
          </div>

          <div class="form-group">
            <label for="genero">Género</label>
            <select name="genero" required>
              <option value="">Seleccione...</option>
              <option value="Masculino" <?php echo (isset($paciente['genero']) && $paciente['genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
              <option value="Femenino" <?php echo (isset($paciente['genero']) && $paciente['genero'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
              <option value="Otro" <?php echo (isset($paciente['genero']) && $paciente['genero'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
            </select>
          </div>

          <div class="form-group">
            <label for="estado_civil">Estado civil</label>
            <select name="estado_civil" required>
              <option value="">Seleccione...</option>
              <option value="Soltero/a" <?php echo (isset($paciente['estado_civil']) && $paciente['estado_civil'] == 'Soltero/a') ? 'selected' : ''; ?>>Soltero/a</option>
              <option value="Casado/a" <?php echo (isset($paciente['estado_civil']) && $paciente['estado_civil'] == 'Casado/a') ? 'selected' : ''; ?>>Casado/a</option>
              <option value="Divorciado/a" <?php echo (isset($paciente['estado_civil']) && $paciente['estado_civil'] == 'Divorciado/a') ? 'selected' : ''; ?>>Divorciado/a</option>
              <option value="Viudo/a" <?php echo (isset($paciente['estado_civil']) && $paciente['estado_civil'] == 'Viudo/a') ? 'selected' : ''; ?>>Viudo/a</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Sección 3: Datos de Contacto -->
      <div class="form-section">
        <h3>Datos de Contacto</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" name="telefono"
              value="<?php echo isset($paciente['telefono']) ? $paciente['telefono'] : ''; ?>"
              required placeholder="Ej. 4431234567">
          </div>

          <div class="form-group">
            <label for="escolaridad">Escolaridad Máxima</label>
            <input type="text" name="escolaridad"
              value="<?php echo isset($paciente['escolaridad']) ? $paciente['escolaridad'] : ''; ?>"
              required placeholder="Ej. Preparatoria, Licenciatura">
          </div>
        </div>
      </div>

      <!-- Sección 4: Dirección -->
      <div class="form-section">
        <h3>Dirección</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="calle">Calle</label>
            <input type="text" name="calle"
              value="<?php echo isset($paciente['calle']) ? $paciente['calle'] : ''; ?>"
              required placeholder="Ej. Francisco I. Madero">
          </div>

          <div class="form-group" style="max-width: 100px;">
            <label for="numero">Número</label>
            <input type="text" name="numero"
              value="<?php echo isset($paciente['numero']) ? $paciente['numero'] : ''; ?>"
              required placeholder="Ej. 123">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="colonia">Colonia</label>
            <input type="text" name="colonia"
              value="<?php echo isset($paciente['colonia']) ? $paciente['colonia'] : ''; ?>"
              required placeholder="Ej. Vasco de Quiroga">
          </div>

          <div class="form-group">
            <label for="ciudad">Ciudad</label>
            <input type="text" name="ciudad"
              value="<?php echo isset($paciente['ciudad']) ? $paciente['ciudad'] : ''; ?>"
              required placeholder="Ej. Morelia">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="estado">Estado</label>
            <input type="text" name="estado"
              value="<?php echo isset($paciente['estado']) ? $paciente['estado'] : ''; ?>"
              required placeholder="Ej. Michoacán">
          </div>

          <div class="form-group" style="max-width: 150px;">
            <label for="cp">Código Postal</label>
            <input type="text" name="cp"
              value="<?php echo isset($paciente['cp']) ? $paciente['cp'] : ''; ?>"
              required placeholder="Ej. 58295">
          </div>
        </div>
      </div>

      <!-- Sección 5: Contacto de Emergencia -->
      <div class="form-section">
        <h3>Contacto de Emergencia</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="contacto_emergencia">Nombre completo</label>
            <input type="text" name="contacto_emergencia"
              value="<?php echo isset($paciente['contacto_emergencia']) ? $paciente['contacto_emergencia'] : ''; ?>"
              required placeholder="Nombre del contacto">
          </div>

          <div class="form-group">
            <label for="telefono_emergencia">Teléfono</label>
            <input type="tel" name="telefono_emergencia"
              value="<?php echo isset($paciente['telefono_emergencia']) ? $paciente['telefono_emergencia'] : ''; ?>"
              required placeholder="Ej. 4431234567">
          </div>

          <div class="form-group">
            <label for="parentesco">Parentesco</label>
            <input type="text" name="parentesco"
              value="<?php echo isset($paciente['parentesco']) ? $paciente['parentesco'] : ''; ?>"
              required placeholder="Ej. Hermano, Madre">
          </div>
        </div>
      </div>

      <div class="button-container">
        <a href="ver_pacientes.php" class="btn-salir">Salir</a>
        <button type="submit" class="button-next"><?php echo $id_paciente ? 'Actualizar' : 'Guardar'; ?> y Continuar &raquo;</button>
      </div>
    </form>
  </div>

</body>

</html>