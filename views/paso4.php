<?php
require_once "../php/conexion.php";

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

$sql = "SELECT * FROM antecedentes_no_patologicos WHERE id_empleado = ?";
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
    <title>Paso 4: Antecedentes No Patológicos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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

        .form-group {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInGroup 0.7s forwards;
        }

        .form-group:nth-child(1) {
            animation-delay: 0.1s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.2s;
        }

        .form-group:nth-child(3) {
            animation-delay: 0.3s;
        }

        .form-group:nth-child(4) {
            animation-delay: 0.4s;
        }

        .form-group:nth-child(5) {
            animation-delay: 0.5s;
        }

        .form-group:nth-child(6) {
            animation-delay: 0.6s;
        }

        .form-group:nth-child(7) {
            animation-delay: 0.7s;
        }

        .form-group:nth-child(8) {
            animation-delay: 0.8s;
        }

        @keyframes fadeInGroup {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
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
            transition: background-color 0.3s, transform 0.1s;
        }

        .button-next:active {
            transform: scale(0.97);
        }

        .btn-salir {
            background-color: rgb(150, 38, 38);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s, transform 0.1s;
        }

        .btn-salir:active {
            transform: scale(0.97);
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

        input:focus,
        select:focus {
            outline: none;
            border-color: #2e3c81;
            box-shadow: 0 0 0 2px #2e3c8133;
            transition: box-shadow 0.3s, border-color 0.3s;
        }
    </style>
</head>

<body>

    <h2>Paso 4: Antecedentes Personales No Patológicos</h2>
    <p>Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

    <form action="../php/guardar_paso4.php" method="post">
        <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

        <!-- Tabaquismo -->
        <div class="form-group">
            <label>
                <input type="checkbox" name="fuma" id="fuma" <?php echo isset($antecedentes['fuma']) && $antecedentes['fuma'] == 1 ? 'checked' : ''; ?>>
                ¿Fuma?
            </label>
            <div id="fuma_fields" class="conditional-field">
                <label>Cigarros por día:
                    <input type="number" name="cigarros_dia" min="0" value="<?php echo $antecedentes['cigarros_dia']; ?>">
                </label>
                <label>Años fumando:
                    <input type="number" name="anos_fumando" min="0" value="<?php echo $antecedentes['anos_fumando']; ?>">
                </label>
            </div>
        </div>

        <!-- Consumo de alcohol -->
        <div class="form-group">
            <label>
                <input type="checkbox" name="bebe" id="bebe" <?php echo isset($antecedentes['bebe']) && $antecedentes['bebe'] == 1 ? 'checked' : ''; ?>>
                ¿Consume alcohol?
            </label>
            <div id="bebe_fields" class="conditional-field">
                <label>Años bebiendo:
                    <input type="number" name="anos_bebiendo" min="0" value="<?php echo $antecedentes['anos_bebiendo']; ?>">
                </label>
                <label>Frecuencia:
                    <select name="frecuencia_alcohol">
                        <option value="<?php echo isset($antecedentes['frecuencia_alcohol']) ? $antecedentes['frecuencia_alcohol'] : '' ?> ">
                            <?php echo isset($antecedentes['frecuencia_alcohol']) ?
                                $antecedentes['frecuencia_alcohol'] : "Seleciona" ?> </option>
                        <option value="Ocasional">Ocasional</option>
                        <option value="Semanal">Semanal</option>
                        <option value="Diario">Diario</option>
                    </select>
                </label>
            </div>
        </div>

        <!-- Medicamentos controlados -->
        <div class="form-group">
            <label>
                <input type="checkbox" name="medicamentos_controlados"
                    <?php echo isset($antecedentes['medicamentos_controlados']) && $antecedentes['medicamentos_controlados'] == 1 ? 'checked' : ''; ?>>
                ¿Usa medicamentos controlados?
            </label>
        </div>

        <!-- Otras preguntas -->
        <div class="form-group">
            <label>
                <input type="checkbox" name="usa_drogas" id="drogas"
                    <?php echo isset($antecedentes['drogas']) && $antecedentes['drogas'] == 1 ? 'checked' : ''; ?>>
                ¿Ha usado drogas?
            </label>
            <div id="drogas_fields" class="conditional-field">
                <label>Tipo de droga:
                    <input type="text" name="tipo_droga"
                        value="<?php echo isset($antecedentes['tipo_droga']) ? $antecedentes['tipo_droga'] : ''; ?>">
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="practica_deporte" id="deporte"
                    <?php echo isset($antecedentes['deporte']) && $antecedentes['deporte'] == 1 ? 'checked' : ''; ?>>
                ¿Practica deporte?
            </label>
            <div id="deporte_fields" class="conditional-field">
                <label>¿Cual deporte?
                    <input type="text" name="tipo_deporte"
                        value="<?php echo isset($antecedentes['tipo_deporte']) ? $antecedentes['tipo_deporte'] : ''; ?>">
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="tatuajes"
                    <?php echo isset($antecedentes['tatuajes']) &&
                        $antecedentes['tatuajes'] == 1 ? 'checked' : ''; ?>>
                ¿Tiene algun tatuaje?
            </label>
        </div>



        <div class="form-group">
            <label>
                <input type="checkbox" name="transfusiones" id="transfusiones"
                    <?php echo isset($antecedentes['transfusiones']) &&
                        $antecedentes['transfusiones'] == 1 ? 'checked' : ''; ?>>
                ¿Acepta transfuciones de sangre?
            </label>
            <div id="transfusiones_fields" class="conditional-field">
                <label>¿Ha recibido transfusiones?
                    <input type="checkbox" name="transfusiones_recibidas"
                        <?php echo isset($antecedentes['transfuciones_recibidas']) &&
                            $antecedentes['transfuciones_recibidas'] == 1 ? 'checked' : ''; ?>>
                </label>
            </div>
        </div>

        <button type="submit" class="button-next">Guardar y Continuar</button>
        <button type="button" class="btn-salir" onclick="window.location.href='../views/ver_pacientes.php'">Salir</button>
    </form>

    <script>
        // Mostrar campos condicionales con animación
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

        document.getElementById('fuma').addEventListener('change', function() {
            toggleField('fuma', 'fuma_fields');
        });
        document.getElementById('bebe').addEventListener('change', function() {
            toggleField('bebe', 'bebe_fields');
        });
        document.getElementById('drogas').addEventListener('change', function() {
            toggleField('drogas', 'drogas_fields');
        });
        document.getElementById('deporte').addEventListener('change', function() {
            toggleField('deporte', 'deporte_fields');
        });
        document.getElementById('transfusiones').addEventListener('change', function() {
            toggleField('transfusiones', 'transfusiones_fields');
        });

        // Inicializar estado al cargar
        document.addEventListener('DOMContentLoaded', function() {
            toggleField('fuma', 'fuma_fields');
            toggleField('bebe', 'bebe_fields');
            toggleField('drogas', 'drogas_fields');
            toggleField('deporte', 'deporte_fields');
            toggleField('transfusiones', 'transfusiones_fields');
        });
    </script>

</body>

</html>