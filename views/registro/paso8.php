<?php
require_once "../php/conexion.php";

// Validar ID del empleado
$id_empleado = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_empleado === 0) {
    die("<h2>Error: ID de empleado no válido</h2>");
}

// Obtener datos del paciente
$sql = "SELECT nombre_completo FROM pacientes WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("<h2>Error: Empleado no encontrado</h2>");
}

$paciente = $resultado->fetch_assoc();

// Obtener datos del examen médico si existen
$sql = "SELECT * FROM examenes_medicos WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();
$examen_medico = $resultado->fetch_assoc();

$stmt->close();

function getChecked($valor, $comparar)
{
    global $examen_medico;
    return isset($examen_medico[$valor]) && $examen_medico[$valor] == $comparar ? 'checked' : '';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Paso 8: Examen Médico</title>
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
            max-width: 1000px;
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
        .form-select:focus,
        textarea:focus {
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

        .imc-display {
            font-weight: bold;
            color: #2e3c81;
            padding: 5px;
            background-color: #e9ecef;
            border-radius: 4px;
            text-align: center;
        }

        .evaluation-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .evaluation-column {
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            background: #f8f9fa;
        }

        .evaluation-item {
            margin-bottom: 20px;
        }

        .evaluation-item h4 {
            margin-top: 0;
            color: #2e3c81;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .result-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-top: 20px;
        }

        .signature-box {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        @media (max-width: 991px) {
            .main-container {
                padding: 0 5px;
            }

            .evaluation-grid,
            .result-grid {
                grid-template-columns: 1fr;
            }

            .button-container {
                flex-direction: column;
                gap: 10px;
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
                Paso 8: Examen Médico
            </h2>
            <p class="mb-4 text-center">Paciente: <strong><?php echo htmlspecialchars($paciente['nombre_completo']); ?></strong>
            </p>

            <form action="../../php/registro/guardar_paso8.php" method="post" autocomplete="off">
                <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

                <div class="form-section">
                    <h3><i class="bi bi-heart-pulse"></i> SOMATOMETRÍA / SIGNOS VITALES</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="talla" class="form-label">Talla (cm):</label>
                            <input type="number" name="talla" id="talla" step="0.01" class="form-control"
                                value="<?php echo isset($examen_medico['talla']) ? $examen_medico['talla'] : '' ?>"
                                oninput="calcularIMC()">
                        </div>
                        <div class="col-md-4">
                            <label for="peso" class="form-label">Peso (kg):</label>
                            <input type="number" name="peso" id="peso" step="0.1" class="form-control"
                                value="<?php echo isset($examen_medico['peso']) ? $examen_medico['peso'] : '' ?>"
                                oninput="calcularIMC()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IMC:</label>
                            <div class="imc-display" id="imc_display">
                                <?php
                                if (isset($examen_medico['imc'])) {
                                    $imc = floatval($examen_medico['imc']);
                                    if ($imc < 18.5) $cat = 'Insuficiencia ponderal';
                                    else if ($imc < 25) $cat = 'Intervalo normal';
                                    else if ($imc < 30) $cat = 'Preobesidad';
                                    else if ($imc < 35) $cat = 'Obesidad clase 1';
                                    else if ($imc < 40) $cat = 'Obesidad clase 2';
                                    else $cat = 'Obesidad clase 3';
                                    echo "IMC " . number_format($imc, 2) . " $cat";
                                } else {
                                    echo '--';
                                }
                                ?>
                            </div>
                            <input type="hidden" name="imc" id="imc">
                            <div id="imc_info" class="small text-secondary"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="fc" class="form-label">FC (x'):</label>
                            <input type="number" name="fc" id="fc" class="form-control"
                                value="<?php echo isset($examen_medico['fc']) ? $examen_medico['fc'] : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="fr" class="form-label">FR (x'):</label>
                            <input type="number" name="fr" id="fr" class="form-control"
                                value="<?php echo isset($examen_medico['fr']) ? $examen_medico['fr'] : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="temp" class="form-label">Temp (°C):</label>
                            <input type="number" name="temp" id="temp" step="0.1" class="form-control"
                                value="<?php echo isset($examen_medico['temp']) ? $examen_medico['temp'] : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="perimetro_abdominal" class="form-label">Perímetro Abd. (cm):</label>
                            <input type="number" name="perimetro_abdominal" id="perimetro_abdominal" class="form-control"
                                value="<?php echo isset($examen_medico['perimetro_abdominal']) ? $examen_medico['perimetro_abdominal'] : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="presion_arterial" class="form-label">Presión arterial (mm/Hg):</label>
                            <input type="text" name="presion_arterial" id="presion_arterial" class="form-control"
                                placeholder="120/80"
                                value="<?php echo isset($examen_medico['presion_arterial']) ? $examen_medico['presion_arterial'] : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="spo2" class="form-label">SpO2 (%):</label>
                            <input type="number" name="spo2" id="spo2" min="0" max="100" class="form-control"
                                value="<?php echo isset($examen_medico['spo2']) ? $examen_medico['spo2'] : '' ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="bi bi-person-vcard"></i> EVALUACIÓN FÍSICA</h3>
                    <div class="evaluation-grid">
                        <div class="evaluation-column">
                            <div class="evaluation-item">
                                <h4>CABEZA</h4>
                                <textarea name="cabeza" rows="3" class="form-control"><?php echo isset($examen_medico['cabeza']) ? $examen_medico['cabeza'] : '' ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>OÍDO</h4>
                                <textarea name="oido" rows="3" class="form-control"><?php echo isset($examen_medico['oido']) ? $examen_medico['oido'] : '' ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>CAVIDAD ORAL</h4>
                                <textarea name="cavidad_oral" rows="3" class="form-control"><?php echo isset($examen_medico['cavidad_oral']) ? $examen_medico['cavidad_oral'] : '' ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>CUELLO</h4>
                                <textarea name="cuello" rows="3" class="form-control"><?php echo isset($examen_medico['cuello']) ? $examen_medico['cuello'] : '' ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>TÓRAX</h4>
                                <textarea name="torax" rows="3" class="form-control"><?php echo isset($examen_medico['torax']) ? $examen_medico['torax'] : '' ?></textarea>
                            </div>
                        </div>
                        <div class="evaluation-column">
                            <div class="evaluation-item">
                                <h4>ABDOMEN</h4>
                                <textarea name="abdomen" rows="3" class="form-control"><?php echo isset($examen_medico['abdomen']) ? $examen_medico['abdomen'] : '' ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>COLUMNA VERTEBRAL</h4>
                                <textarea name="columna" rows="3" class="form-control"><?php echo isset($examen_medico['columna']) ? $examen_medico['columna'] : '' ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>EXTREMIDADES SUPERIORES</h4>
                                <textarea name="extremidades_superiores" rows="3" class="form-control"><?php echo isset($examen_medico['extremidades_superiores']) ? $examen_medico['extremidades_superiores'] : '' ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>EXTREMIDADES INFERIORES</h4>
                                <textarea name="extremidades_inferiores" rows="3" class="form-control"><?php echo isset($examen_medico['extremidades_inferiores']) ? $examen_medico['extremidades_inferiores'] : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="bi bi-clipboard-check"></i> RESULTADO DE EVALUACIÓN MÉDICA</h3>
                    <div class="result-grid">
                        <div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="resultado" value="Recomendable"
                                    id="resultado1" <?php echo getChecked('resultado', 'Recomendable'); ?>>
                                <label class="form-check-label" for="resultado1">Recomendable</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="resultado" value="Recomendable con restricción"
                                    id="resultado2" <?php echo getChecked('resultado', 'Recomendable con restricción'); ?>>
                                <label class="form-check-label" for="resultado2">Recomendable con restricción</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="resultado" value="No recomendable"
                                    id="resultado3" <?php echo getChecked('resultado', 'No recomendable'); ?>>
                                <label class="form-check-label" for="resultado3">No recomendable</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="resultado" value="Se reubica (examen periódico)"
                                    id="resultado4" <?php echo getChecked('resultado', 'Se reubica (examen periódico)'); ?>>
                                <label class="form-check-label" for="resultado4">Se reubica (examen periódico)</label>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Recomendaciones / restricciones:</label>
                            <textarea name="recomendaciones" rows="5" class="form-control"><?php echo isset($examen_medico['recomendaciones']) ? $examen_medico['recomendaciones'] : '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="signature-box">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="confirmacion_paciente" id="confirmacion_paciente" required>
                        <label class="form-check-label" for="confirmacion_paciente">
                            Hago constar que las respuestas suministradas en este cuestionario son verídicas y proporcionan la información requerida acerca de mis antecedentes de salud.
                        </label>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
                    <a href="../views/ver_pacientes.php" class="btn btn-danger btn-lg">
                        <i class="bi bi-box-arrow-left"></i> Salir
                    </a>
                    <div class="ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-save"></i> Guardar Resultados
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS (opcional, para componentes interactivos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para calcular el IMC automáticamente
        function calcularIMC() {
            const talla = parseFloat(document.getElementById('talla').value) / 100; // Convertir cm a m
            const peso = parseFloat(document.getElementById('peso').value);
            const imcInfo = document.getElementById('imc_info');

            if (talla > 0 && peso > 0) {
                const imc = peso / (talla * talla);
                document.getElementById('imc_display').textContent = imc.toFixed(2);
                document.getElementById('imc').value = imc.toFixed(2);

                let categoria = '';
                if (imc < 18.5) {
                    categoria = 'Insuficiencia ponderal (<18.5)';
                } else if (imc < 25) {
                    categoria = 'Intervalo normal (18.5 - 24.9)';
                } else if (imc < 30) {
                    categoria = 'Preobesidad (25 - 29.9)';
                } else if (imc < 35) {
                    categoria = 'Obesidad clase 1 (30 - 34.9)';
                } else if (imc < 40) {
                    categoria = 'Obesidad clase 2 (35 - 39.9)';
                } else {
                    categoria = 'Obesidad clase 3 (≥40)';
                }
                imcInfo.textContent = categoria;
            } else {
                document.getElementById('imc_display').textContent = '--';
                document.getElementById('imc').value = '';
                imcInfo.textContent = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
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