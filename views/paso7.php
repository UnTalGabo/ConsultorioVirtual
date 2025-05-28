<?php
require_once "../php/conexion.php";

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
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-next {
            background-color: #2e3c81;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-salir {
            background-color: rgb(172, 45, 45);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .button-next:active,
        .btn-salir:active {
            transform: scale(0.97);
        }

        /* Animación para campos condicionales */
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

        @keyframes fadeInGroup {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <h2>Paso 7: Antecedentes Médico-Laborales</h2>
    <p>Empleado: <strong><?php echo htmlspecialchars($paciente['nombre_completo']); ?></strong></p>

    <form action="../php/guardar_paso7.php" method="post" class="form-container">
        <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

        <div class="form-group">
            <label for="edad_inicio_trabajo">¿A qué edad comenzó a trabajar?</label>
            <select name="edad_inicio_trabajo" id="edad_inicio_trabajo" required>
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

        <div class="form-section">

            <div class="form-group">
                <label for="empresa">Empresa:</label>
                <input type="text" name="empresa" id="empresa"
                    value="Hospital Angeles Morelia" readonly
                    style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label for="antiguedad">Antigüedad (años):</label>
                <select name="antiguedad" id="antiguedad" required>
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

            <div class="form-group">
                <label for="puesto">puesto:</label>
                <input type="text" name="puesto" id="puesto"
                    value="<?php echo isset($paciente['puesto']) ? $paciente['puesto'] : '' ?>" readonly
                    style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>¿Tuvo exposición a?</label>
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



            <div class="form-group">
                <label for="equipo_proteccion">Equipo de protección personal utilizado:</label>
                <textarea name="equipo_proteccion" id="equipo_proteccion" rows="3"
                    value="<?php echo isset($antecedentes['equipo_proteccion']) ? $antecedentes['equipo_proteccion'] : ''; ?>"></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3>ACCIDENTES</h3>

            <div class="form-group">
                <label>¿Ha sufrido accidentes de trabajo?</label>
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

            <div id="accidentesFields" class="conditional-field">
                <div class="form-group">
                    <label for="fecha_accidente">Fecha del accidente:</label>
                    <input type="date" name="fecha_accidente" id="fecha_accidente"
                        value="<?php echo isset($antecedentes['fecha_accidente']) ? $antecedentes['fecha_accidente'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="lesion">Lesión:</label>
                    <input type="text" name="lesion" id="lesion">
                </div>

                <div class="form-group">
                    <label>¿Ha recibido pagos por accidente o enfermedad de trabajo?</label>
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

                <div id="pagosFields" class="conditional-field">
                    <div class="form-group">
                        <label>Pagado por:</label>
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

                <div class="form-group">
                    <label>¿Tiene secuelas de accidentes de trabajo?</label>
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

                <div id="secuelasFields" class="conditional-field">
                    <div class="form-group">
                        <label for="fecha_secuela">Fecha de secuela:</label>
                        <input type="date" name="fecha_secuela" id="fecha_secuela">
                    </div>

                    <div class="form-group">
                        <label for="secuela">Secuela:</label>
                        <textarea name="secuela" id="secuela" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="button-container">
            <button type="submit" class="button-next">Guardar y Salir</button>
            <a href="../views/ver_pacientes.php" class="btn-salir">Salir</a>
        </div>
    </form>

    <script>
        // Función para mostrar/ocultar campos de accidentes
        function toggleAccidentesFields() {
            const accidentesSi = document.getElementById('accidentes_si').checked;
            const accidentesFields = document.getElementById('accidentesFields');

            accidentesFields.style.display = accidentesSi ? 'block' : 'none';

            // Resetear campos si se ocultan
            if (!accidentesSi) {
                document.getElementById('fecha_accidente').value = '';
                document.getElementById('lesion').value = '';
                document.getElementById('pagos_si').checked = false;
                document.getElementById('pagos_no').checked = true;
                document.getElementById('secuelas_si').checked = false;
                document.getElementById('secuelas_no').checked = true;
                document.getElementById('fecha_secuela').value = '';
                document.getElementById('secuela').value = '';

                // Ocultar también los subcampos
                document.getElementById('pagosFields').style.display = 'none';
                document.getElementById('secuelasFields').style.display = 'none';
            }
        }

        // Función para mostrar/ocultar campos de pagos
        function togglePagosFields() {
            const pagosSi = document.getElementById('pagos_si').checked;
            document.getElementById('pagosFields').style.display = pagosSi ? 'block' : 'none';

            if (!pagosSi) {
                document.getElementById('pagado_imss').checked = false;
                document.getElementById('pagado_empresa').checked = false;
            }
        }

        // Función para mostrar/ocultar campos de secuelas
        function toggleSecuelasFields() {
            const secuelasSi = document.getElementById('secuelas_si').checked;
            document.getElementById('secuelasFields').style.display = secuelasSi ? 'block' : 'none';

            if (!secuelasSi) {
                document.getElementById('fecha_secuela').value = '';
                document.getElementById('secuela').value = '';
            }
        }

        // Inicializar campos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            toggleAccidentesFields();
            togglePagosFields();
            toggleSecuelasFields();
        });
    </script>

</body>

</html>