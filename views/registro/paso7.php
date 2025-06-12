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

// Validar ID del empleado
$id_empleado = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_empleado === 0) {
    die("<h2>Error: ID de empleado no válido</h2>");
}

// Obtener datos del paciente
$sql = "SELECT nombre_completo, puesto FROM pacientes WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("<h2>Error: Empleado no encontrado</h2>");
}

$paciente = $resultado->fetch_assoc();

$sql = "SELECT * FROM antecedentes_laborales WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado2 = $stmt->get_result();
$antecedentes = $resultado2->fetch_assoc();

$stmt->close();

function getChecked($valor)
{
    global $antecedentes;
    return isset($antecedentes[$valor]) && $antecedentes[$valor] == 1 ? 'checked' : '';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Paso 7: Antecedentes Médico-Laborales</title>
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
            max-width: 950px;
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

        .form-section:nth-child(1) {
            animation-delay: 0.1s;
        }

        .form-section:nth-child(2) {
            animation-delay: 0.2s;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        @keyframes sectionFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .form-control:focus,
        .form-select:focus {
            border-color: #2e3c81;
            box-shadow: 0 0 0 2px #2e3c8133;
        }

        .btn-primary,
        .btn-success {
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
        .btn-primary:focus,
        .btn-success:hover,
        .btn-success:focus {
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

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .conditional-field {
            display: none;
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: opacity 0.4s, max-height 0.4s;
        }

        .conditional-field.show {
            display: block;
            opacity: 1;
            max-height: 500px;
            transition: opacity 0.4s, max-height 0.4s;
        }

        @media (max-width: 991px) {
            .main-container {
                padding: 0 5px;
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
            <h2 class="text-center mb-4 fw-bold">
                <i class="bi bi-briefcase-medical me-2"></i>
                Paso 7: Antecedentes Médico-Laborales
            </h2>
            <p class="mb-4 text-center">Empleado: <strong><?php echo htmlspecialchars($paciente['nombre_completo']); ?></strong>
            </p>

            <form action="../../php/registro/guardar_paso7.php" method="post" autocomplete="off">
                <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

                <div class="form-section">
                    <div class="mb-3">
                        <label for="edad_inicio_trabajo" class="form-label">¿A qué edad comenzó a trabajar?</label>
                        <select name="edad_inicio_trabajo" id="edad_inicio_trabajo" class="form-select">
                            <option value="">Seleccione una edad</option>
                            <?php
                            $edad_actual = isset($antecedentes['edad_inicio_trabajo']) ? $antecedentes['edad_inicio_trabajo'] : '';
                            for ($i = 10; $i <= 40; $i++) {
                                $selected = ($i == $edad_actual) ? 'selected' : '';
                                echo "<option value='$i' $selected>$i años</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="empresa" class="form-label">Empresa:</label>
                        <input type="text" name="empresa" id="empresa"
                            value="<?php echo isset($antecedentes['empresa']) ? $antecedentes['empresa'] : '' ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="antiguedad" class="form-label">Antigüedad (años):</label>
                        <select name="antiguedad" id="antiguedad" class="form-select">
                            <option value="">Seleccione una edad</option>
                            <?php
                            $edad_actual = isset($antecedentes['antiguedad']) ? $antecedentes['antiguedad'] : '';
                            for ($i = 0; $i <= 20; $i++) {
                                $selected = ($i == $edad_actual) ? 'selected' : '';
                                echo "<option value='$i' $selected>$i años</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="puesto" class="form-label">Puesto:</label>
                        <input type="text" name="puesto" id="puesto"
                            value="<?php echo isset($antecedentes['puesto']) ? $antecedentes['puesto'] : '' ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">¿Tuvo exposición a?</label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="polvo" id="polvo"
                                    <?php echo getChecked('polvo') ?>>
                                <label for="polvo">Polvo</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="ruido" id="ruido"
                                    <?php echo getChecked('ruido') ?>>
                                <label for="ruido">Ruido</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="humo" id="humo"
                                    <?php echo getChecked('humo') ?>>
                                <label for="humo">Humo</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="radiacion" id="radiacion"
                                    <?php echo getChecked('radiacion') ?>>
                                <label for="radiacion">Radiación</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="quimicos" id="quimicos"
                                    <?php echo getChecked('quimicos') ?>>
                                <label for="quimicos">Químicos/Solventes</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="calor_frio" id="calor_frio"
                                    <?php echo getChecked('calor_frio') ?>>
                                <label for="calor_frio">Calor/Frío</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="vibracion" id="vibracion"
                                    <?php echo getChecked('vibracion') ?>>
                                <label for="vibracion">Vibración</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="movimiento_repetitivo" id="movimiento_repetitivo"
                                    <?php echo getChecked('movimiento_repetitivo') ?>>
                                <label for="movimiento_repetitivo">Movimiento repetitivo</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="cargas" id="cargas"
                                    <?php echo getChecked('cargas') ?>>
                                <label for="cargas">Cargas</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="exposicion[]" value="riesgos_psicosociales" id="riesgos_psicosociales"
                                    <?php echo getChecked('riesgos_psicosociales') ?>>
                                <label for="riesgos_psicosociales">Riesgos psicosociales</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="equipo_proteccion" class="form-label">Equipo de protección personal utilizado:</label>
                        <textarea name="equipo_proteccion" id="equipo_proteccion" rows="3"
                            class="form-control"><?php echo isset($antecedentes['equipo_proteccion']) ? $antecedentes['equipo_proteccion'] : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="bi bi-exclamation-triangle"></i> ACCIDENTES</h3>
                    <div class="mb-3">
                        <label class="form-label">¿Ha sufrido accidentes de trabajo?</label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="radio" name="accidentes" value="1" id="accidentes_si"
                                    <?php echo isset($antecedentes['accidentes']) && $antecedentes['accidentes'] == 1 ? 'checked' : ''; ?>
                                    onchange="toggleAccidentesFields()">
                                <label for="accidentes_si">Sí</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="radio" name="accidentes" value="0" id="accidentes_no"
                                    <?php echo isset($antecedentes['accidentes']) && $antecedentes['accidentes'] == 0 ? 'checked' : ''; ?>
                                    onchange="toggleAccidentesFields()">
                                <label for="accidentes_no">No</label>
                            </div>
                        </div>
                    </div>

                    <div id="accidentesFields" class="conditional-field<?php echo (isset($antecedentes['accidentes']) && $antecedentes['accidentes'] == 1) ? ' show' : ''; ?>">
                        <div class="mb-3">
                            <label for="fecha_accidente" class="form-label">Fecha del accidente:</label>
                            <input type="date" name="fecha_accidente" id="fecha_accidente"
                                class="form-control"
                                value="<?php echo isset($antecedentes['fecha_accidente']) ? $antecedentes['fecha_accidente'] : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="lesion" class="form-label">Lesión:</label>
                            <input type="text" name="lesion" id="lesion"
                                class="form-control"
                                value="<?php echo isset($antecedentes['lesion']) ? htmlspecialchars($antecedentes['lesion']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">¿Ha recibido pagos por accidente o enfermedad de trabajo?</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="radio" name="pagos_accidente" value="1" id="pagos_si"
                                        <?php echo isset($antecedentes['pagos_accidente']) && $antecedentes['pagos_accidente'] == 1 ? 'checked' : ''; ?>
                                        onchange="togglePagosFields()">
                                    <label for="pagos_si">Sí</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="radio" name="pagos_accidente" value="0" id="pagos_no"
                                        <?php echo isset($antecedentes['pagos_accidente']) && $antecedentes['pagos_accidente'] == 0 ? 'checked' : ''; ?>
                                        onchange="togglePagosFields()">
                                    <label for="pagos_no">No</label>
                                </div>
                            </div>
                        </div>
                        <div id="pagosFields" class="conditional-field<?php echo (isset($antecedentes['pagos_accidente']) && $antecedentes['pagos_accidente'] == 1) ? ' show' : ''; ?>">
                            <div class="mb-3">
                                <label class="form-label">Pagado por:</label>
                                <div class="checkbox-group">
                                    <div class="checkbox-item">
                                        <input type="radio" name="pagado_por" value="imss" id="pagado_imss"
                                            <?php echo isset($antecedentes['pagado_por']) && $antecedentes['pagado_por'] == 'imss' ? 'checked' : ''; ?>>
                                        <label for="pagado_imss">IMSS</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="radio" name="pagado_por" value="empresa" id="pagado_empresa"
                                            <?php echo isset($antecedentes['pagado_por']) && $antecedentes['pagado_por'] == 'empresa' ? 'checked' : ''; ?>>
                                        <label for="pagado_empresa">Empresa</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">¿Tiene secuelas de accidentes de trabajo?</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="radio" name="secuelas" value="1" id="secuelas_si"
                                        <?php echo isset($antecedentes['secuelas']) && $antecedentes['secuelas'] == 1 ? 'checked' : ''; ?>
                                        onchange="toggleSecuelasFields()">
                                    <label for="secuelas_si">Sí</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="radio" name="secuelas" value="0" id="secuelas_no"
                                        <?php echo isset($antecedentes['secuelas']) && $antecedentes['secuelas'] == 0 ? 'checked' : ''; ?>
                                        onchange="toggleSecuelasFields()">
                                    <label for="secuelas_no">No</label>
                                </div>
                            </div>
                        </div>
                        <div id="secuelasFields" class="conditional-field<?php echo (isset($antecedentes['secuelas']) && $antecedentes['secuelas'] == 1) ? ' show' : ''; ?>">
                            <div class="mb-3">
                                <label for="fecha_secuela" class="form-label">Fecha de secuela:</label>
                                <input type="date" name="fecha_secuela" id="fecha_secuela"
                                    class="form-control"
                                    value="<?php echo isset($antecedentes['fecha_secuela']) ? $antecedentes['fecha_secuela'] : ''; ?>">
                            </div>
                            <div class="mb-3">
                            <label for="secuela" class="form-label">Lesión:</label>
                            <input type="text" name="secuela" id="secuela"
                                class="form-control"
                                value="<?php echo isset($antecedentes['secuela']) ? htmlspecialchars($antecedentes['secuela']) : ''; ?>">
                        </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
                    <a href="../ver_pacientes.php" class="btn btn-danger btn-lg" onclick="return confirm('¿Estás seguro de que quieres salir sin guardar?');">
                        <i class="bi bi-box-arrow-left"></i> Salir sin guardar
                    </a>
                    <div class="ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" name="accion" value="guardar_salir">
                            <i class="bi bi-save"></i> Guardar y Salir
                        </button>
                        <button type="submit" class="btn btn-success btn-lg" name="accion" value="guardar_continuar" style="background-color: #198754; border-color: #198754;">
                            <i class="bi bi-arrow-right-circle"></i> Guardar y Continuar
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS (opcional, para componentes interactivos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar/ocultar campos de accidentes
        function toggleAccidentesFields() {
            const accidentesSi = document.getElementById('accidentes_si').checked;
            const accidentesFields = document.getElementById('accidentesFields');

            if (accidentesSi) {
                accidentesFields.classList.add('show');
            } else {
                accidentesFields.classList.remove('show');
                // Resetear campos si se ocultan
                document.getElementById('fecha_accidente').value = '';
                document.getElementById('lesion').value = '';
                document.getElementById('pagos_si').checked = false;
                document.getElementById('pagos_no').checked = true;
                document.getElementById('secuelas_si').checked = false;
                document.getElementById('secuelas_no').checked = true;
                document.getElementById('fecha_secuela').value = '';
                document.getElementById('secuela').value = '';
                // Ocultar también los subcampos solo quitando la clase show
                document.getElementById('pagosFields').classList.remove('show');
                document.getElementById('secuelasFields').classList.remove('show');
            }
        }

        // Función para mostrar/ocultar campos de pagos
        function togglePagosFields() {
            const pagosSi = document.getElementById('pagos_si').checked;
            const pagosFields = document.getElementById('pagosFields');
            if (pagosSi) {
                pagosFields.classList.add('show');
            } else {
                pagosFields.classList.remove('show');
                document.getElementById('pagado_imss').checked = false;
                document.getElementById('pagado_empresa').checked = false;
            }
        }

        // Función para mostrar/ocultar campos de secuelas
        function toggleSecuelasFields() {
            const secuelasSi = document.getElementById('secuelas_si').checked;
            const secuelasFields = document.getElementById('secuelasFields');
            if (secuelasSi) {
                secuelasFields.classList.add('show');
            } else {
                secuelasFields.classList.remove('show');
                document.getElementById('fecha_secuela').value = '';
                document.getElementById('secuela').value = '';
            }
        }

        // Inicializar campos al cargar la página
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.form-section').forEach(function (section, i) {
                setTimeout(() => {
                    section.style.opacity = 1;
                    section.style.transform = 'translateY(0)';
                }, 150 + i * 120);
            });
            toggleAccidentesFields();
            togglePagosFields();
            toggleSecuelasFields();
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