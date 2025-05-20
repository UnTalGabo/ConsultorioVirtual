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

$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paso 5: Antecedentes Gineco-Obstétricos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f4f6fa;
            color: #1e2a78;
        }
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .form-column {
            flex: 1;
            min-width: 300px;
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
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
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
    </style>
</head>
<body>

<h2>Paso 5: Antecedentes Gineco-Obstétricos</h2>
<p>Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

<form action="../php/guardar_paso5.php" method="post">
    <input type="hidden" name="id_paciente" value="<?php echo $id_empleado; ?>">

    <div class="form-container">
        <!-- Columna 1 -->
        <div class="form-column">
            <div class="form-group">
                <label>Edad que inició su regla (años)</label>
                <input type="number" name="edad_inicio_regla" min="8" max="25">
            </div>

            <div class="form-group">
                <label>Ritmo de ciclo menstrual (días)</label>
                <input type="number" name="ritmo_ciclo_menstrual" min="15" max="45">
            </div>

            <div class="form-group">
                <label>Fecha de última menstruación</label>
                <input type="date" name="fecha_ultima_menstruacion">
            </div>

            <div class="form-group">
                <label>Número de gestas</label>
                <input type="number" name="numero_gestas" min="0" value="0">
            </div>
        </div>

        <!-- Columna 2 -->
        <div class="form-column">
            <div class="form-group">
                <label>Número de partos</label>
                <input type="number" name="numero_partos" min="0" value="0">
            </div>

            <div class="form-group">
                <label>Número de abortos</label>
                <input type="number" name="numero_abortos" min="0" value="0">
            </div>

            <div class="form-group">
                <label>Número de cesáreas</label>
                <input type="number" name="numero_cesareas" min="0" value="0">
            </div>

            <div class="form-group">
                <label>Fecha de última citología cervicovaginal (Papanicolau)</label>
                <input type="date" name="fecha_ultima_citologia">
            </div>
        </div>
    </div>

    <!-- Campos de ancho completo -->
    <div class="form-group">
        <label>¿Complicaciones en la menstruación?</label>
        <textarea name="complicaciones_menstruacion" rows="3"></textarea>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="mastografia">
            ¿Se ha realizado mastografía?
        </label>
    </div>

    <div class="button-container">
        <button type="submit" class="button-next">Guardar y Continuar</button>
    </div>
</form>

</body>
</html>