<?php
require_once "../php/conexion.php";

// Validar ID del paciente
$id_empleado = $_GET['id'];
$sql = "SELECT id_empleado, nombre_completo FROM pacientes WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("<h2>Error: Paciente no encontrado</h2>");
}
$paciente = $resultado->fetch_assoc();

$sql = "SELECT * FROM antecedentes_patologicos WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado2 = $stmt->get_result();
$antecedentes = $resultado2->fetch_assoc();

$sql = "SELECT enfermedad FROM enfermedades_patologicas WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado3 = $stmt->get_result();
$enfermedades = [];
while ($row = $resultado3->fetch_assoc()) {
    $enfermedades[] = $row;
}

$stmt->close();

function getChecked($efnfermedad)
{
    global $enfermedades;
    foreach ($enfermedades as $enfermedad) {
        if ($enfermedad['enfermedad'] == $efnfermedad) {
            return 'checked';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Paso 6: Antecedentes Patológicos</title>
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

        .form-section:nth-child(3) {
            animation-delay: 0.3s;
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

        .enfermedad-item {
            margin-bottom: 10px;
        }

        .form-column {
            min-width: 220px;
        }

        @media (max-width: 991px) {
            .form-columns {
                flex-direction: column;
            }

            .form-column {
                min-width: 100%;
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
                <i class="bi bi-clipboard2-pulse me-2"></i>
                Paso 6: Antecedentes Patológicos
            </h2>
            <p class="mb-4 text-center">Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

            <form action="../php/guardar_paso6.php" method="post" autocomplete="off">
                <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

                <div class="form-section">
                    <h3><i class="bi bi-activity"></i> Enfermedades que padeces o hayas padecido</h3>
                    <div class="row form-columns g-3">
                        <!-- 3 columnas de enfermedades, balanceadas -->
                        <div class="col-md-4">
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Varicela/Rubeola/Sarampión" <?php echo getChecked('Varicela/Rubeola/Sarampión') ?>> Varicela/Rubeola/Sarampión</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades respiratorias" <?php echo getChecked('Enfermedades respiratorias') ?>> Enfermedades respiratorias</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades pulmonares" <?php echo getChecked('Enfermedades pulmonares') ?>> Enfermedades pulmonares</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Asma bronquial" <?php echo getChecked('Asma bronquial') ?>> Asma bronquial</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades del corazón" <?php echo getChecked('Enfermedades del corazón') ?>> Enfermedades del corazón</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Presión alta o baja" <?php echo getChecked('Presión alta o baja') ?>> Presión alta o baja</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Vértigos" <?php echo getChecked('Vértigos') ?>> Vértigos</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Anemia/Sangrado anormal" <?php echo getChecked('Anemia/Sangrado anormal') ?>> Anemia/Sangrado anormal</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Tuberculosos" <?php echo getChecked('Tuberculosos') ?>> Tuberculosos</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Transtornos de la pies" <?php echo getChecked('Transtornos de la pies') ?>> Transtornos de la pies</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Heridas/Quemaduras" <?php echo getChecked('Heridas/Quemaduras') ?>> Heridas/Quemaduras</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Tumores o cáncer" <?php echo getChecked('Tumores o cáncer') ?>> Tumores o cáncer</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Varices/Hemorroides" <?php echo getChecked('Varices/Hemorroides') ?>> Varices/Hemorroides</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Cefalea" <?php echo getChecked('Cefalea') ?>> Cefalea</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Hernias" <?php echo getChecked('Hernias') ?>> Hernias</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Problemas en la espalda" <?php echo getChecked('Problemas en la espalda') ?>> Problemas en la espalda</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Golpes en la columna" <?php echo getChecked('Golpes en la columna') ?>> Golpes en la columna</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Golpes en la cabeza" <?php echo getChecked('Golpes en la cabeza') ?>> Golpes en la cabeza</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Artritis o Reumatismo" <?php echo getChecked('Artritis o Reumatismo') ?>> Artritis o Reumatismo</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Depresión/Ansiedad" <?php echo getChecked('Depresión/Ansiedad') ?>> Depresión/Ansiedad</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Paludismo" <?php echo getChecked('Paludismo') ?>> Paludismo</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Sensación de Hormigueo" <?php echo getChecked('Sensación de Hormigueo') ?>> Sensación de Hormigueo</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades Gastrointestinales" <?php echo getChecked('Enfermedades Gastrointestinales') ?>> Enfermedades Gastrointestinales</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="COVID 19" <?php echo getChecked('COVID 19') ?>> COVID 19</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Gastritis/Ulcera/Colitis" <?php echo getChecked('Gastritis/Ulcera/Colitis') ?>> Gastritis/Ulcera/Colitis</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades del higado" <?php echo getChecked('Enfermedades del higado') ?>> Enfermedades del higado</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Diabetes" <?php echo getChecked('Diabetes') ?>> Diabetes</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades del riñon" <?php echo getChecked('Enfermedades del riñon') ?>> Enfermedades del riñon</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades de genitales" <?php echo getChecked('Enfermedades de genitales') ?>> Enfermedades de genitales</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Convulsiones (Epilepsia)" <?php echo getChecked('Convulsiones (Epilepsia)') ?>> Convulsiones (Epilepsia)</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Paroditis" <?php echo getChecked('Paroditis') ?>> Paroditis</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades Oculares" <?php echo getChecked('Enfermedades Oculares') ?>> Enfermedades Oculares</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades dentales" <?php echo getChecked('Enfermedades dentales') ?>> Enfermedades dentales</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Problemas de audicion" <?php echo getChecked('Problemas de audicion') ?>> Problemas de audicion</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Acufeno/Tinitus" <?php echo getChecked('Acufeno/Tinitus') ?>> Acufeno/Tinitus</label>
                            </div>
                            <div class="enfermedad-item">
                                <label><input type="checkbox" name="enfermedades[]" value="Usa prótesis" <?php echo getChecked('Usa prótesis') ?>> Usa prótesis</label>
                            </div>
                            <div class="mb-3 mt-4">
                                <label class="form-label">Otra enfermedad:</label>
                                <input type="text" name="otra_enfermedad_4" class="form-control" placeholder="Especifique otra enfermedad">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="mb-3">
                        <label class="form-label">Fracturas o esguinces</label>
                        <textarea name="fracturas_esguinces" rows="3" class="form-control"><?php echo isset($antecedentes['fracturas_esguinces']) ? $antecedentes['fracturas_esguinces'] : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cirugías</label>
                        <textarea name="cirugias" rows="3" class="form-control"><?php echo isset($antecedentes['cirugias']) ? $antecedentes['cirugias'] : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">¿Tiene alguna enfermedad actualmente?</label>
                        <textarea name="enfermedad_actual_desc" rows="3" class="form-control"><?php echo isset($antecedentes['enfermedad_actual_desc']) ? $antecedentes['enfermedad_actual_desc'] : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Medicamentos que toma</label>
                        <textarea name="medicamentos" rows="3" class="form-control"><?php echo isset($antecedentes['medicamentos']) ? $antecedentes['medicamentos'] : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="form-control"><?php echo isset($antecedentes['observaciones']) ? $antecedentes['observaciones'] : '' ?></textarea>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
                    <a href="ver_pacientes.php" class="btn btn-danger btn-lg" name="accion" value="salir_sin_guardar">
                        <i class="bi bi-box-arrow-left"></i> Salir sin guardar
                    </a>
                    <div class="ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg" name="accion" value="guardar_continuar">
                            <i class="bi bi-arrow-right-circle"></i> Guardar y Continuar 
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg" name="accion" value="guardar_salir">
                            <i class="bi bi-save"></i> Guardar y Salir
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