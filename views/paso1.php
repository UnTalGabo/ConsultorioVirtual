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
      max-width: 900px;
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

    .form-section {
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #e9ecef;
      animation: sectionFadeIn 0.7s forwards;
      opacity: 0;
      transform: translateY(30px);
    }
    .form-section:nth-child(1) { animation-delay: 0.1s; }
    .form-section:nth-child(2) { animation-delay: 0.2s; }
    .form-section:nth-child(3) { animation-delay: 0.3s; }
    .form-section:nth-child(4) { animation-delay: 0.4s; }
    .form-section:nth-child(5) { animation-delay: 0.5s; }
    @keyframes sectionFadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-section:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }

    .form-section h3 {
      color: #2e3c81;
      font-weight: 600;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .form-label {
      color: #1e2a78;
      font-weight: 500;
    }

    .form-control:focus, .form-select:focus {
      border-color: #2e3c81;
      box-shadow: 0 0 0 2px #2e3c8133;
    }

    .btn-primary {
      background-color: #2e3c81;
      border: none;
      font-size: 1.15rem;
      font-weight: 500;
      padding: 0.75rem 2.2rem;
      border-radius: 8px;
      transition: background 0.2s, transform 0.1s;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .btn-primary:hover, .btn-primary:focus {
      background-color: #1e2a78;
      transform: scale(0.98);
    }

    .btn-danger {
      font-size: 1.1rem;
      padding: 0.75rem 1.8rem;
      border-radius: 8px;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    @media (max-width: 767px) {
      .main-container {
        margin-top: 20px;
      }
      .card {
        padding: 0.5rem;
      }
      .form-section {
        padding-bottom: 0.5rem;
      }
    }
  </style>
</head>

<body>
  <!-- Barra de navegación superior -->
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <i class="bi bi-hospital-fill fs-3"></i>
        Consultorio Virtual
      </a>
    </div>
  </nav>

  <div class="main-container">
    <div class="card p-4 p-md-5">
      <h2 class="text-center mb-4 fw-bold">
        <i class="bi bi-person-vcard-fill me-2"></i>
        <?php echo $id_paciente ? 'Editar' : 'Nuevo'; ?> Paciente - Información Básica
      </h2>
      <form action="../php/guardar_paso1.php" method="POST" autocomplete="off">
        <input type="hidden" name="id_empleado" value="<?php echo $id_paciente; ?>">

        <!-- Sección 1: Datos del Empleado -->
        <div class="form-section">
          <h3><i class="bi bi-person-badge"></i> Datos del Empleado</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="id_empleado" class="form-label">Número de empleado</label>
              <input type="number" name="id_empleado" class="form-control"
                value="<?php echo $id_paciente ?>"
                required placeholder="Ej. 1023">
            </div>
            <div class="col-md-4">
              <label for="puesto" class="form-label">Puesto</label>
              <input type="text" name="puesto" class="form-control"
                value="<?php echo isset($paciente['puesto']) ? $paciente['puesto'] : ''; ?>"
                required placeholder="Ej. Analista de Sistemas">
            </div>
            <div class="col-md-4">
              <label for="area" class="form-label">Área/Departamento</label>
              <input type="text" name="area" class="form-control"
                value="<?php echo isset($paciente['area']) ? $paciente['area'] : ''; ?>"
                required placeholder="Ej. Desarrollo Organizacional">
            </div>
            <div class="col-md-4">
              <label for="departamento" class="form-label">Departamento</label>
              <input type="text" name="departamento" class="form-control"
                value="<?php echo isset($paciente['departamento']) ? $paciente['departamento'] : ''; ?>"
                placeholder="Ej. Administración">
            </div>
          </div>
        </div>

        <!-- Sección 2: Datos Personales -->
        <div class="form-section">
          <h3><i class="bi bi-person-lines-fill"></i> Datos Personales</h3>
          <div class="row g-3">
            <div class="col-12">
              <label for="nombre_completo" class="form-label">Nombre completo</label>
              <input type="text" name="nombre_completo" class="form-control"
                value="<?php echo isset($paciente['nombre_completo']) ? $paciente['nombre_completo'] : ''; ?>"
                required placeholder="Ej. Juan Pérez López">
            </div>
            <div class="col-md-4">
              <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
              <input type="date" name="fecha_nacimiento" class="form-control"
                value="<?php echo isset($paciente['fecha_nacimiento']) ? $paciente['fecha_nacimiento'] : ''; ?>"
                required>
            </div>
            <div class="col-md-4">
              <label for="genero" class="form-label">Género</label>
              <select name="genero" class="form-select" required>
                <option value="">Seleccione...</option>
                <option value="Masculino" <?php echo (isset($paciente['genero']) && $paciente['genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                <option value="Femenino" <?php echo (isset($paciente['genero']) && $paciente['genero'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                <option value="Otro" <?php echo (isset($paciente['genero']) && $paciente['genero'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="estado_civil" class="form-label">Estado civil</label>
              <select name="estado_civil" class="form-select" required>
                <option value="">Seleccione...</option>
                <option value="Soltero/a" <?php echo (isset($paciente['estado_civil']) && $paciente['estado_civil'] == 'Soltero/a') ? 'selected' : ''; ?>>Soltero/a</option>
                <option value="Casado/a" <?php echo (isset($paciente['estado_civil']) && $paciente['estado_civil'] == 'Casado/a') ? 'selected' : ''; ?>>Casado/a</option>
                <option value="Divorciado/a" <?php echo (isset($paciente['estado_civil']) && $paciente['estado_civil'] == 'Divorciado/a') ? 'selected' : ''; ?>>Divorciado/a</option>
                <option value="Viudo/a" <?php echo (isset($paciente['estado_civil']) && $paciente['estado_civil'] == 'Viudo/a') ? 'selected' : ''; ?>>Viudo/a</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="tipo_sangre" class="form-label">Tipo de sangre</label>
              <select name="tipo_sangre" class="form-select" required>
                <option value="">Seleccione...</option>
                <option value="A+" <?php echo (isset($paciente['tipo_sangre']) && $paciente['tipo_sangre'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                <option value="A-" <?php echo (isset($paciente['tipo_sangre']) && $paciente['tipo_sangre'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                <option value="B+" <?php echo (isset($paciente['tipo_sangre']) && $paciente['tipo_sangre'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                <option value="B-" <?php echo (isset($paciente['tipo_sangre']) && $paciente['tipo_sangre'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                <option value="AB+" <?php echo (isset($paciente['tipo_sangre']) && $paciente['tipo_sangre'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                <option value="AB-" <?php echo (isset($paciente['tipo_sangre']) && $paciente['tipo_sangre'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                <option value="O+" <?php echo (isset($paciente['tipo_sangre']) && $paciente['tipo_sangre'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                <option value="O-" <?php echo (isset($paciente['tipo_sangre']) && $paciente['tipo_sangre'] == 'O-') ? 'selected' : ''; ?>>O-</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Sección 3: Datos de Contacto -->
        <div class="form-section">
          <h3><i class="bi bi-telephone-fill"></i> Datos de Contacto</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="tel" name="telefono" class="form-control"
                value="<?php echo isset($paciente['telefono']) ? $paciente['telefono'] : ''; ?>"
                required placeholder="Ej. 4431234567">
            </div>
            <div class="col-md-6">
              <label for="escolaridad" class="form-label">Escolaridad Máxima</label>
              <input type="text" name="escolaridad" class="form-control"
                value="<?php echo isset($paciente['escolaridad']) ? $paciente['escolaridad'] : ''; ?>"
                required placeholder="Ej. Preparatoria, Licenciatura">
            </div>
          </div>
        </div>

        <!-- Sección 4: Dirección -->
        <div class="form-section">
          <h3><i class="bi bi-geo-alt-fill"></i> Dirección</h3>
          <div class="row g-3">
            <div class="col-md-8">
              <label for="calle" class="form-label">Calle</label>
              <input type="text" name="calle" class="form-control"
                value="<?php echo isset($paciente['calle']) ? $paciente['calle'] : ''; ?>"
                required placeholder="Ej. Francisco I. Madero">
            </div>
            <div class="col-md-4">
              <label for="numero" class="form-label">Número</label>
              <input type="text" name="numero" class="form-control"
                value="<?php echo isset($paciente['numero']) ? $paciente['numero'] : ''; ?>"
                required placeholder="Ej. 123">
            </div>
            <div class="col-md-6">
              <label for="colonia" class="form-label">Colonia</label>
              <input type="text" name="colonia" class="form-control"
                value="<?php echo isset($paciente['colonia']) ? $paciente['colonia'] : ''; ?>"
                required placeholder="Ej. Vasco de Quiroga">
            </div>
            <div class="col-md-6">
              <label for="ciudad" class="form-label">Ciudad</label>
              <input type="text" name="ciudad" class="form-control"
                value="<?php echo isset($paciente['ciudad']) ? $paciente['ciudad'] : 'Morelia'; ?>"
                required >
            </div>
            <div class="col-md-8">
              <label for="estado" class="form-label">Estado</label>
              <input type="text" name="estado" class="form-control"
                value="<?php echo isset($paciente['estado']) ? $paciente['estado'] : ''; ?>"
                required placeholder="Ej. Michoacán">
            </div>
            <div class="col-md-4">
              <label for="cp" class="form-label">Código Postal</label>
              <input type="text" name="cp" class="form-control"
                value="<?php echo isset($paciente['cp']) ? $paciente['cp'] : ''; ?>"
                required placeholder="Ej. 58295">
            </div>
          </div>
        </div>

        <!-- Sección 5: Contacto de Emergencia -->
        <div class="form-section">
          <h3><i class="bi bi-exclamation-triangle-fill"></i> Contacto de Emergencia</h3>
          <div class="row g-3">
            <div class="col-md-5">
              <label for="contacto_emergencia" class="form-label">Nombre completo</label>
              <input type="text" name="contacto_emergencia" class="form-control"
                value="<?php echo isset($paciente['contacto_emergencia']) ? $paciente['contacto_emergencia'] : ''; ?>"
                required placeholder="Nombre del contacto">
            </div>
            <div class="col-md-4">
              <label for="telefono_emergencia" class="form-label">Teléfono</label>
              <input type="tel" name="telefono_emergencia" class="form-control"
                value="<?php echo isset($paciente['telefono_emergencia']) ? $paciente['telefono_emergencia'] : ''; ?>"
                required placeholder="Ej. 4431234567">
            </div>
            <div class="col-md-3">
              <label for="parentesco" class="form-label">Parentesco</label>
              <input type="text" name="parentesco" class="form-control"
                value="<?php echo isset($paciente['parentesco']) ? $paciente['parentesco'] : ''; ?>"
                required placeholder="Ej. Hermano, Madre">
            </div>
          </div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
          <a href="ver_pacientes.php" class="btn btn-danger btn-lg">
            <i class="bi bi-box-arrow-left"></i> Salir sin guardar
          </a>
          <div class="ms-auto d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-lg" name="accion" value="guardar_salir">
              <i class="bi bi-save2"></i> Guardar y Salir
            </button>
            <button type="submit" class="btn btn-success btn-lg" name="accion" value="guardar_continuar">
              <i class="bi bi-arrow-right-circle"></i> Guardar y Continuar &raquo;
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS (opcional, para componentes interactivos) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Animación de fade-in para las secciones
    document.addEventListener("DOMContentLoaded", function () {
      document.querySelectorAll('.form-section').forEach(function (section, i) {
        setTimeout(() => {
          section.style.opacity = 1;
          section.style.transform = 'translateY(0)';
        }, 150 + i * 120);
      });
    });
  </script>
</body>

</html>