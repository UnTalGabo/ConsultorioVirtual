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
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paso 6: Antecedentes Patológicos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f4f6fa;
            color: #1e2a78;
        }
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-column {
            flex: 1;
            min-width: 250px;
        }
        .form-group {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="checkbox"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        .full-width {
            width: 100%;
        }
        .button-container {
            width: 100%;
            text-align: center;
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
            background-color:rgb(173, 52, 52);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .enfermedad-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<h2>Paso 6: Antecedentes Patológicos</h2>
<p>Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

<form action="../php/guardar_paso6.php" method="post">
    <input type="hidden" name="id_paciente" value="<?php echo $id_empleado; ?>">

    <h3>Enfermedades (marque las que apliquen)</h3>
    <div class="form-container">
        <!-- Columna 1 -->
        <div class="form-column">
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Varicela/Rubeola/Sarampion"> Varicela/Rubeola/Sarampión</label>
            </div>
            <!-- Agregar más enfermedades aquí -->
            <div class="form-group">
                <label>Otra enfermedad:</label>
                <input type="text" name="otra_enfermedad_1" placeholder="Especifique otra enfermedad">
            </div>
        </div>

        <!-- Columna 2 -->
        <div class="form-column">
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Varices/Hemorroides"> Varices/Hemorroides</label>
            </div>
            <!-- Agregar más enfermedades aquí -->
            <div class="form-group">
                <label>Otra enfermedad:</label>
                <input type="text" name="otra_enfermedad_2" placeholder="Especifique otra enfermedad">
            </div>
        </div>

        <!-- Columna 3 -->
        <div class="form-column">
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Sensacion de hormigueo"> Sensación de hormigueo</label>
            </div>
            <!-- Agregar más enfermedades aquí -->
            <div class="form-group">
                <label>Otra enfermedad:</label>
                <input type="text" name="otra_enfermedad_3" placeholder="Especifique otra enfermedad">
            </div>
        </div>

        <!-- Columna 4 -->
        <div class="form-column">
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Transtornos de la piel"> Trastornos de la piel</label>
            </div>
            <!-- Agregar más enfermedades aquí -->
            <div class="form-group">
                <label>Otra enfermedad:</label>
                <input type="text" name="otra_enfermedad_4" placeholder="Especifique otra enfermedad">
            </div>
        </div>
    </div>

    <!-- Sección de ancho completo -->
    <div class="form-group full-width">
        <label>Fracturas o esguinces</label>
        <textarea name="fracturas_esguinces" rows="3"></textarea>
    </div>

    <div class="form-group full-width">
        <label>Cirugías</label>
        <textarea name="cirugias" rows="3"></textarea>
    </div>

    <div class="form-group">
        <label>Tipo de sangre</label>
        <select name="tipo_sangre">
            <option value="">Seleccione</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
        </select>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="enfermedad_actual" id="enfermedad_actual">
            ¿Tiene alguna enfermedad actualmente?
        </label>
        <textarea name="enfermedad_actual_desc" rows="3"></textarea>   
    </div>

    <div class="form-group full-width">
        <label>Medicamentos que toma</label>
        <textarea name="medicamentos" rows="3"></textarea>
    </div>

    <div class="form-group full-width">
        <label>Observaciones</label>
        <textarea name="observaciones" rows="3"></textarea>
    </div>

    <div class="button-container">
        <button type="submit" class="button-next">Guardar y Continuar</button>
        <button type="button" class="btn-salir" onclick="window.location.href='../views/ver_pacientes.php'">Salir</button>
    </div>
</form>



</body>
</html>