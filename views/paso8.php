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
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f4f6fa;
            color: #1e2a78;
            /* Animación de fade-in para el contenido principal */
            animation: fadeInBody 0.7s cubic-bezier(.39, .575, .565, 1.000);
        }

        @keyframes fadeInBody {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInForm 0.8s 0.2s forwards;
        }

        @keyframes fadeInForm {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInSection 0.7s forwards;
        }

        .form-section:nth-child(1) {
            animation-delay: 0.3s;
        }

        .form-section:nth-child(2) {
            animation-delay: 0.4s;
        }

        .form-section:nth-child(3) {
            animation-delay: 0.5s;
        }

        @keyframes fadeInSection {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 15px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInGroup 0.6s forwards;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            transition: box-shadow 0.3s, border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2e3c81;
            box-shadow: 0 0 0 2px #2e3c8133;
        }

        .button-next,
        .btn-salir {
            transition: background-color 0.3s, transform 0.1s;
        }

        .button-next:active,
        .btn-salir:active {
            transform: scale(0.97);
        }

        @keyframes fadeInGroup {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .form-section h3 {
            color: #2e3c81;
            margin-top: 0;
            text-align: center;
            background-color: #e9ecef;
            padding: 8px;
            border-radius: 4px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-next {
            background-color: #2e3c81;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-salir {
            background-color: #dc3545;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .signature-box {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .result-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-top: 20px;
        }

        .imc-display {
            font-weight: bold;
            color: #2e3c81;
            padding: 5px;
            background-color: #e9ecef;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>Paso 8: Examen Médico</h2>
    <p>Paciente: <strong><?php echo htmlspecialchars($paciente['nombre_completo']); ?></strong></p>

    <form action="../php/guardar_paso8.php" method="post" class="form-container">
        <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

        <div class="form-section">
            <h3>SOMATOMETRÍA / SIGNOS VITALES</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label for="talla">Talla (cm):</label>
                    <input type="number" name="talla" id="talla" step="0.01"
                        value="<?php echo isset($examen_medico['talla']) ? $examen_medico['talla'] : '' ?>" oninput="calcularIMC()">
                </div>

                <div class="form-group">
                    <label for="peso">Peso (kg):</label>
                    <input type="number" name="peso" id="peso" step="0.1"
                        value="<?php echo isset($examen_medico['peso']) ? $examen_medico['peso'] : '' ?>" oninput="calcularIMC()">
                </div>

                <div class="form-group">
                    <label>IMC:</label>
                    <div class="imc-display" id="imc_display"><?php
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
                                                                ?></div>
                    <input type="hidden" name="imc" id="imc">
                </div>

                <div class="form-group">
                    <label for="fc">FC (x'):</label>
                    <input type="number" name="fc" id="fc"
                        value="<?php echo isset($examen_medico['fc']) ? $examen_medico['fc'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="fr">FR (x'):</label>
                    <input type="number" name="fr" id="fr"
                        value="<?php echo isset($examen_medico['fr']) ? $examen_medico['fr'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="temp">Temp (°C):</label>
                    <input type="number" name="temp" id="temp" step="0.1"
                        value="<?php echo isset($examen_medico['temp']) ? $examen_medico['temp'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="perimetro_abdominal">Perímetro Abd. (cm):</label>
                    <input type="number" name="perimetro_abdominal" id="perimetro_abdominal"
                        value="<?php echo isset($examen_medico['perimetro_abdominal']) ? $examen_medico['perimetro_abdominal'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="presion_arterial">Presión arterial (mm/Hg):</label>
                    <input type="text" name="presion_arterial" id="presion_arterial" placeholder="120/80"
                        value="<?php echo isset($examen_medico['presion_arterial']) ? $examen_medico['presion_arterial'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="spo2">SpO2 (%):</label>
                    <input type="number" name="spo2" id="spo2" min="0" max="100"
                        value="<?php echo isset($examen_medico['spo2']) ? $examen_medico['spo2'] : '' ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>EVALUACIÓN FÍSICA</h3>

            <div class="evaluation-grid">
                <div class="evaluation-column">
                    <div class="evaluation-item">
                        <h4>CABEZA</h4>
                        <textarea name="cabeza" rows="3" style="width: 100%;"
                            value="<?php echo isset($examen_medico['cabeza']) ? $examen_medico['cabeza'] : '' ?>"><?php echo isset($examen_medico['cabeza']) ? $examen_medico['cabeza'] : '' ?></textarea>
                    </div>

                    <div class="evaluation-item">
                        <h4>OÍDO</h4>
                        <textarea name="oido" rows="3" style="width: 100%;"
                            value="<?php echo isset($examen_medico['oido']) ? $examen_medico['oido'] : '' ?>"><?php echo isset($examen_medico['oido']) ? $examen_medico['oido'] : '' ?></textarea>
                    </div>

                    <div class="evaluation-item">
                        <h4>CAVIDAD ORAL</h4>
                        <textarea name="cavidad_oral" rows="3" style="width: 100%;"
                            value="<?php echo isset($examen_medico['cavidad_oral']) ? $examen_medico['cavidad_oral'] : '' ?>"><?php echo isset($examen_medico['cavidad_oral']) ? $examen_medico['cavidad_oral'] : '' ?></textarea>
                    </div>

                    <div class="evaluation-item">
                        <h4>CUELLO</h4>
                        <textarea name="cuello" rows="3" style="width: 100%;"
                            value="<?php echo isset($examen_medico['cuello']) ? $examen_medico['cuello'] : '' ?>"><?php echo isset($examen_medico['cuello']) ? $examen_medico['cuello'] : '' ?></textarea>
                    </div>

                    <div class="evaluation-item">
                        <h4>TÓRAX</h4>
                        <textarea name="torax" rows="3" style="width: 100%;"
                            value="<?php echo isset($examen_medico['torax']) ? $examen_medico['torax'] : '' ?>"><?php echo isset($examen_medico['torax']) ? $examen_medico['torax'] : '' ?></textarea>
                    </div>
                </div>

                <div class="evaluation-column">

                    <div class="evaluation-item">
                        <h4>ABDOMEN</h4>
                        <textarea name="abdomen" rows="3" style="width: 100%;"
                            value="<?php echo isset($examen_medico['abdomen']) ? $examen_medico['abdomen'] : '' ?>"><?php echo isset($examen_medico['abdomen']) ? $examen_medico['abdomen'] : '' ?></textarea>
                    </div>

                    <div class="evaluation-item">
                        <h4>COLUMNA VERTEBRAL</h4>
                        <textarea name="columna" rows="3" style="width: 100%;"
                            value="<?php echo isset($examen_medico['columna']) ? $examen_medico['columna'] : '' ?>"><?php echo isset($examen_medico['columna']) ? $examen_medico['columna'] : null ?></textarea>
                    </div>

                    <div class="evaluation-item">
                        <h4>EXTREMIDADES SUPERIORES</h4>
                        <textarea name="extremidades_superiores" rows="3" style="width: 100%;"
                            value="<?php echo isset($examen_medico['extremidades_superiores']) ? $examen_medico['extremidades_superiores'] : '' ?>"><?php echo isset($examen_medico['extremidades_superiores']) ? $examen_medico['extremidades_superiores'] : '' ?></textarea>
                    </div>

                    <div class="evaluation-item">
                        <h4>EXTREMIDADES INFERIORES</h4>
                        <textarea name="extremidades_inferiores" rows="3" style="width: 100%;"
                            value="<?php echo isset($examen_medico['extremidades_inferiores']) ? $examen_medico['extremidades_inferiores'] : '' ?>"><?php echo isset($examen_medico['extremidades_inferiores']) ? $examen_medico['extremidades_inferiores'] : '' ?></textarea>
                    </div>


                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>RESULTADO DE EVALUACIÓN MÉDICA</h3>

            <div class="result-grid">
                <div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="resultado" value="Recomendable"
                                <?php echo getChecked('resultado', 'Recomendable'); ?>> Recomendable
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="resultado" value="Recomendable con restricción"
                                <?php echo getChecked('resultado', '') ?>> Recomendable con restricción
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="resultado" value="No recomendable"
                                <?php echo getChecked('resultado', '') ?>> No recomendable
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="resultado" value="Se reubica (examen periódico)"
                                <?php echo getChecked('resultado', '') ?>> Se reubica (examen periódico)
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Recomendaciones / restricciones:</label>
                    <textarea name="recomendaciones" rows="5" style="width: 100%;"
                        value="<?php echo isset($examen_medico['recomendaciones']) ? $examen_medico['recomendaciones'] : '' ?>"><?php echo isset($examen_medico['recomendaciones']) ? $examen_medico['recomendaciones'] : '' ?></textarea>
                </div>
            </div>
        </div>

        <div class="signature-box">
            <div class="form-group">
                <label>
                    <input type="checkbox" name="confirmacion_paciente" required>
                    Hago constar que las respuestas suministradas en este cuestionario son verídicas y proporcionan la información requerida acerca de mis antecedentes de salud.
                </label>
            </div>
        </div>

        <div class="button-container">
            <button type="submit" class="button-next">Guardar Resultados</button>
            <a href="../views/ver_pacientes.php" class="btn-salir">Salir</a>
        </div>
    </form>

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

        // Inicializar cualquier otra funcionalidad necesaria
        document.addEventListener('DOMContentLoaded', function() {
            // Puedes agregar inicializaciones adicionales aquí si es necesario
        });
    </script>
</body>

</html>