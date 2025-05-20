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
    <title>Paso 4: Antecedentes No Patológicos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f4f6fa;
            color: #1e2a78;
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
        .button-next {
            background-color: #2e3c81;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .conditional-field {
            margin-left: 20px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>

<h2>Paso 4: Antecedentes Personales No Patológicos</h2>
<p>Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

<form action="../php/guardar_paso4.php" method="post">
    <input type="hidden" name="id_paciente" value="<?php echo $id_empleado; ?>">

    <!-- Tabaquismo -->
    <div class="form-group">
        <label>
            <input type="checkbox" name="fuma" id="fuma">
            ¿Fuma?
        </label>
        <div id="fuma_fields" class="conditional-field">
            <label>Cigarros por día:
                <input type="number" name="cigarros_dia" min="0">
            </label>
            <label>Años fumando:
                <input type="number" name="anos_fumando" min="0">
            </label>
        </div>
    </div>

    <!-- Consumo de alcohol -->
    <div class="form-group">
        <label>
            <input type="checkbox" name="bebe" id="bebe">
            ¿Consume alcohol?
        </label>
        <div id="bebe_fields" class="conditional-field">
            <label>Años bebiendo:
                <input type="number" name="anos_bebiendo" min="0">
            </label>
            <label>Frecuencia:
                <select name="frecuencia_alcohol">
                    <option value="">Seleccione</option>
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
            <input type="checkbox" name="medicamentos_controlados">
            ¿Usa medicamentos controlados?
        </label>
    </div>

    <!-- Otras preguntas -->
    <div class="form-group">
        <label>
            <input type="checkbox" name="usa_drogas" id="drogas">
            ¿Ha usado drogas?
        </label>
        <div id="drogas_fields" class="conditional-field">
            <label>Tipo de droga:
                <input type="text" name="tipo_droga">
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="practica_deporte" id="deporte">
            ¿Practica deporte?
        </label>
        <div id="deporte_fields" class="conditional-field">
            <label>¿Cual deporte?
                <input type="text" name="tipo_deporte">
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="tatuajes">
            ¿Tiene algun tatuaje?
        </label>
    </div>

    

    <div class="form-group">
        <label>
            <input type="checkbox" name="transfusiones" id="transfusiones">
            ¿Acepta transfuciones de sangre?
        </label>
        <div id="transfusiones_fields" class="conditional-field">
            <label>¿Ha recibido transfusiones?
                <input type="checkbox" name="transfusiones_recibidas">
            </label>
        </div>
    </div>

    <button type="submit" class="button-next">Guardar y Continuar</button>
</form>

<script>
// Mostrar campos condicionales
document.getElementById('fuma').addEventListener('change', function() {
    document.getElementById('fuma_fields').style.display = this.checked ? 'block' : 'none';
});

document.getElementById('bebe').addEventListener('change', function() {
    document.getElementById('bebe_fields').style.display = this.checked ? 'block' : 'none';
});

document.getElementById('drogas').addEventListener('change', function() {
    document.getElementById('drogas_fields').style.display = this.checked ? 'block' : 'none';
});

document.getElementById('deporte').addEventListener('change', function() {
    document.getElementById('deporte_fields').style.display = this.checked ? 'block' : 'none';
});

document.getElementById('transfusiones').addEventListener('change', function() {
    document.getElementById('transfusiones_fields').style.display = this.checked ? 'block' : 'none';
});

// Inicializar estado
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('fuma').dispatchEvent(new Event('change'));
    document.getElementById('bebe').dispatchEvent(new Event('change'));
    document.getElementById('drogas').dispatchEvent(new Event('change'));
    document.getElementById('deportes').dispatchEvent(new Event('change'));
});
</script>

</body>
</html>