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

// Validar ID del paciente
$id_empleado = $_GET['id'];
$sql = "SELECT id_empleado, nombre_completo, genero FROM pacientes WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("<h2>Error: Paciente no encontrado</h2>");
}

$paciente = $resultado->fetch_assoc();

$sql = "SELECT * FROM antecedentes_gineco_obstetricos WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado2 = $stmt->get_result();
$antecedentes = $resultado2->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Paso 5: Antecedentes Gineco-Obstétricos</title>
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

        .navbar-brand,
        .navbar-brand i {
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .main-container {
            max-width: 800px;
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

        .section-title {
            color: #2e3c81;
            font-weight: 700;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 18px;
            padding: 18px 20px;
            background: #f8fafc;
            border-radius: 10px;
            box-shadow: 0 2px 8px 0 rgba(30, 42, 120, 0.06);
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInGroup 0.7s forwards;
        }

        .form-group:nth-child(n) {
            animation-delay: 0.1s;
        }

        .form-group:nth-child(n+1) {
            animation-delay: 0.2s;
        }

        .form-group:nth-child(n+2) {
            animation-delay: 0.3s;
        }

        .form-group:nth-child(n+3) {
            animation-delay: 0.4s;
        }

        .form-group:nth-child(n+4) {
            animation-delay: 0.5s;
        }

        .form-group:nth-child(n+5) {
            animation-delay: 0.6s;
        }

        .form-group:nth-child(n+6) {
            animation-delay: 0.7s;
        }

        .form-group:nth-child(n+7) {
            animation-delay: 0.8s;
        }

        @keyframes fadeInGroup {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-label {
            font-weight: 500;
            color: #1e2a78;
        }

        .conditional-field {
            margin-left: 20px;
            margin-top: 10px;
            display: none;
            opacity: 0;
            max-height: 0;
            transition: opacity 0.4s, max-height 0.4s;
        }

        .conditional-field.show {
            display: block;
            opacity: 1;
            max-height: 200px;
            transition: opacity 0.4s, max-height 0.4s;
        }

        .form-control:focus,
        .form-select:focus {
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

        .btn-primary:hover,
        .btn-primary:focus {
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

            .form-group {
                padding: 10px 8px;
            }
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
        </div>
    </nav>

    <div class="main-container">
        <div class="card p-4 p-md-5">
            <h2 class="section-title mb-3">
                <i class="bi bi-gender-female"></i>
                Paso 5: Antecedentes Gineco-Obstétricos
            </h2>
            <p class="mb-4">Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

            <form action="../../php/registro/guardar_paso5.php" method="post">
                <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Edad que inició su regla (años)</label>
                            <input type="number" name="edad_inicio_regla" min="8" max="25" class="form-control"
                                value="<?php echo isset($antecedentes['edad_inicio_regla']) ? $antecedentes['edad_inicio_regla'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ritmo de ciclo menstrual (días)</label>
                            <input type="number" name="ritmo_ciclo_menstrual" min="15" max="45" class="form-control"
                                value="<?php echo isset($antecedentes['ritmo_ciclo_menstrual']) ? $antecedentes['ritmo_ciclo_menstrual'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha de última menstruación</label>
                            <input type="date" name="fecha_ultima_menstruacion" class="form-control"
                                value="<?php echo isset($antecedentes['fecha_ultima_menstruacion']) ? $antecedentes['fecha_ultima_menstruacion'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número de embarazos</label>
                            <input type="number" name="numero_gestas" min="0" class="form-control"
                                value="<?php echo isset($antecedentes['numero_gestas']) ? $antecedentes['numero_gestas'] : '0'; ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Número de partos</label>
                            <input type="number" name="numero_partos" min="0" class="form-control"
                                value="<?php echo isset($antecedentes['numero_partos']) ? $antecedentes['numero_partos'] : '0'; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número de abortos</label>
                            <input type="number" name="numero_abortos" min="0" class="form-control"
                                value="<?php echo isset($antecedentes['numero_abortos']) ? $antecedentes['numero_abortos'] : '0'; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número de cesáreas</label>
                            <input type="number" name="numero_cesareas" min="0" class="form-control"
                                value="<?php echo isset($antecedentes['numero_cesareas']) ? $antecedentes['numero_cesareas'] : '0'; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha de último embarazo</label>
                            <input type="date" name="fecha_ultimo_embarazo" class="form-control"
                                value="<?php echo isset($antecedentes['fecha_ultimo_embarazo']) ? $antecedentes['fecha_ultimo_embarazo'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha de última citología cervicovaginal (Papanicolau)</label>
                            <input type="date" name="fecha_ultima_citologia" class="form-control"
                                value="<?php echo isset($antecedentes['fecha_ultima_citologia']) ? $antecedentes['fecha_ultima_citologia'] : ''; ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">¿Complicaciones en la menstruación?</label>
                    <textarea name="complicaciones_menstruacion" rows="3" class="form-control"><?php echo isset($antecedentes['complicaciones_menstruacion']) ? $antecedentes['complicaciones_menstruacion'] : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="mastografia" id="mastografia"
                            <?php echo isset($antecedentes['mastografia']) && $antecedentes['mastografia'] == 1 ? 'checked' : ''; ?>>
                        ¿Se ha realizado mastografía?
                    </label>
                    <div id="mastografia_fields" class="conditional-field">
                        <label class="form-label">Fecha de última mastografía
                            <input type="date" name="fecha_ultima_mastografia" class="form-control"
                                value="<?php echo isset($antecedentes['fecha_ultima_mastografia']) ? $antecedentes['fecha_ultima_mastografia'] : ''; ?>">
                        </label>
                    </div>
                </div>

                 <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
                    <a href="../ver_pacientes.php" class="btn btn-danger btn-lg" onclick="return confirm('¿Estás seguro de que quieres salir sin guardar?');">
                        <i class="bi bi-box-arrow-left"></i> Salir sin guardar
                    </a>
                    <div class="ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" name="accion" value="guardar_salir">
                            <i class="bi bi-save2"></i> Guardar y Salir
                        </button>
                        <button type="submit" class="btn btn-success btn-lg" name="accion" value="guardar_continuar">
                            <i class="bi bi-arrow-right-circle"></i> Guardar y Continuar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleField(checkboxId, fieldId) {
            var cb = document.getElementById(checkboxId);
            var field = document.getElementById(fieldId);
            if (cb && field) {
                if (cb.checked) {
                    field.classList.add('show');
                } else {
                    field.classList.remove('show');
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            toggleField('mastografia', 'mastografia_fields');
            document.getElementById('mastografia').addEventListener('change', function() {
                toggleField('mastografia', 'mastografia_fields');
            });
        });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  const btnContinuar = form.querySelector('button[name="accion"][value="guardar_continuar"]');
  form.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
      e.preventDefault();
      btnContinuar.click();
    }
  });
});
</script>
</body>

</html>