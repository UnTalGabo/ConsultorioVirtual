<?php
require_once "../php/conexion.php";

// 1. Validar ID del paciente
$id_empleado = $_GET['id'];
$sql_verifica = "SELECT id_empleado, nombre_completo FROM pacientes WHERE id_empleado = ?";
$stmt_verifica = $conn->prepare($sql_verifica);
$stmt_verifica->bind_param("i", $id_empleado);
$stmt_verifica->execute();
$resultado = $stmt_verifica->get_result();

if ($resultado->num_rows == 0) {
    die("<h2>Error: Paciente no encontrado</h2>");
}
$paciente = $resultado->fetch_assoc();
$stmt_verifica->close();
?>

<script>
// Función para deshabilitar selects cuando el checkbox no está marcado
function toggleSelect(checkbox) {
    const row = checkbox.closest('tr');
    const select = row.querySelector('select');
    select.disabled = !checkbox.checked;
    
    // Resetear el valor si se desmarca
    if (!checkbox.checked) {
        select.value = '';
    }
}

// Aplicar a todos los checkboxes al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        toggleSelect(checkbox); // Estado inicial
        checkbox.addEventListener('change', function() {
            toggleSelect(this);
        });
    });
});
</script>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paso 3: Antecedentes Heredo-Familiares</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px; /* Aumentado para 2 columnas */
            margin: 20px auto;
            padding: 20px;
            background-color: #f4f6fa;
            color: #1e2a78;
        }
        .contenedor-columnas {
            display: flex;
            gap: 20px;
        }
        .columna {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #2e3c81;
            color: white;
        }
        .button-next {
            background-color: #2e3c81;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

<h2>Paso 3: Antecedentes Heredo-Familiares</h2>
<p>Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

<form action="../php/guardar_paso3.php" method="post">
    <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

    <div class="contenedor-columnas">
        <!-- Columna 1 -->
        <div class="columna">
            <table>
                <thead>
                    <tr>
                        <th>Enfermedad</th>
                        <th>¿Presente?</th>
                        <th>¿Quién?</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Presión alta/baja</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="presion">
                            <input type="hidden" name="nombre_enfermedad_presion" value="Presión alta/baja">
                        </td>
                        <td>
                            <select name="presion_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Vértigos</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="vertigos">
                            <input type="hidden" name="nombre_enfermedad_vertigos" value="Vértigos">
                        </td>
                        <td>
                            <select name="vertigos_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Diabetes</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="diabetes">
                            <input type="hidden" name="nombre_enfermedad_diabetes" value="Diabetes">
                        </td>
                        <td>
                            <select name="diabetes_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Enfermedades del Corazón</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="corazon">
                            <input type="hidden" name="nombre_enfermedad_corazon" value="Enfermedades del Corazón">
                        </td>
                        <td>
                            <select name="corazon_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Enfermedades Pulmonares</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="pulmonares">
                            <input type="hidden" name="nombre_enfermedad_pulmonares" value="Enfermedades Pulmonares">
                        </td>
                        <td>
                            <select name="pulmonares_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Enfermedades del Riñon</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="rinon">
                            <input type="hidden" name="nombre_enfermedad_rinon" value="Enfermedades del Riñon">
                        </td>
                        <td>
                            <select name="rinon_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Enfermedades del Higado</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="higado">
                            <input type="hidden" name="nombre_enfermedad_higado" value="Enfermedades del Higado">
                        </td>
                        <td>
                            <select name="higado_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Alergias</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="alergias">
                            <input type="hidden" name="nombre_enfermedad_alergias" value="Alergias">
                        </td>
                        <td>
                            <select name="alergias_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Tumores o cáncer</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="tumores">
                            <input type="hidden" name="nombre_enfermedad_tumores" value="Tumores o cáncer">
                        </td>
                        <td>
                            <select name="tumores_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Asma bronquial</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="asma">
                            <input type="hidden" name="nombre_enfermedad_asma" value="Asma bronquial">
                        </td>
                        <td>
                            <select name="asma_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Gastritis/Ulcera</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="gastritis">
                            <input type="hidden" name="nombre_enfermedad_gastritis" value="Gastritis/Ulcera">
                        </td>
                        <td>
                            <select name="gastritis_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Columna 2 -->
        <div class="columna">
            <table>
                <thead>
                    <tr>
                        <th>Enfermedad</th>
                        <th>¿Presente?</th>
                        <th>¿Quién?</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Flebitis/Várices</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="varices">
                            <input type="hidden" name="nombre_enfermedad_varices" value="Flebitis/Várices">
                        </td>
                        <td>
                            <select name="varices_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Artritis</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="artritis">
                            <input type="hidden" name="nombre_enfermedad_artritis" value="Artritis">
                        </td>
                        <td>
                            <select name="artritis_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Alteraciones del sueño</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="sueno">
                            <input type="hidden" name="nombre_enfermedad_sueno" value="Alteraciones del sueño">
                        </td>
                        <td>
                            <select name="sueno_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Acufeno/Tinitus</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="tinitus">
                            <input type="hidden" name="nombre_enfermedad_tinitus" value="Acufeno/Tinitus">
                        </td>
                        <td>
                            <select name="tinitus_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Problemas de espalda</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="espalda">
                            <input type="hidden" name="nombre_enfermedad_espalda" value="Problemas de espalda">
                        </td>
                        <td>
                            <select name="espalda_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Sensación de hormigueo</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="hormigueo">
                            <input type="hidden" name="nombre_enfermedad_hormigueo" value="Sensación de hormigueo">
                        </td>
                        <td>
                            <select name="hormigueo_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Convulsiones</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="convulsiones">
                            <input type="hidden" name="nombre_enfermedad_convulsiones" value="Convulsiones">
                        </td>
                        <td>
                            <select name="convulsiones_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Debilidad Muscular</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="debilidad">
                            <input type="hidden" name="nombre_enfermedad_debilidad" value="Debilidad Muscular">
                        </td>
                        <td>
                            <select name="debilidad_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Osteoporosis</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="osteoporosis">
                            <input type="hidden" name="nombre_enfermedad_osteoporosis" value="Osteoporosis">
                        </td>
                        <td>
                            <select name="osteoporosis_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Hernias</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="hernias">
                            <input type="hidden" name="nombre_enfermedad_hernias" value="Hernias">
                        </td>
                        <td>
                            <select name="hernias_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>COVID 19</td>
                        <td>
                            <input type="checkbox" name="enfermedades[]" value="covid">
                            <input type="hidden" name="nombre_enfermedad_covid" value="COVID 19">
                        </td>
                        <td>
                            <select name="covid_quien">
                                <option value="">Seleccionar</option>
                                <option value="A">Abuelos (A)</option>
                                <option value="P">Padre (P)</option>
                                <option value="M">Madre (M)</option>
                                <option value="H">Hermanos (H)</option>
                                <option value="T">Tíos (T)</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <button type="submit" class="button-next">Guardar y Continuar &raquo;</button>
</form>

</body>
</html>