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

function getChecked($efnfermedad){
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
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f4f6fa;
            color: #1e2a78;
            /* Animación de fade-in para el contenido principal */
            animation: fadeInBody 0.7s cubic-bezier(.39,.575,.565,1.000);
        }
        @keyframes fadeInBody {
            from { opacity: 0; transform: translateY(30px);}
            to   { opacity: 1; transform: translateY(0);}
        }
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
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
        .form-column {
            flex: 1;
            min-width: 250px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInColumn 0.7s forwards;
        }
        .form-column:nth-child(1) { animation-delay: 0.3s; }
        .form-column:nth-child(2) { animation-delay: 0.4s; }
        .form-column:nth-child(3) { animation-delay: 0.5s; }
        .form-column:nth-child(4) { animation-delay: 0.6s; }
        @keyframes fadeInColumn {
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInGroup 0.7s forwards;
        }
        .form-group:nth-child(1) { animation-delay: 0.7s; }
        .form-group:nth-child(2) { animation-delay: 0.8s; }
        .form-group:nth-child(3) { animation-delay: 0.9s; }
        .form-group:nth-child(4) { animation-delay: 1s; }
        .form-group:nth-child(5) { animation-delay: 1.1s; }
        .form-group:nth-child(6) { animation-delay: 1.2s; }
        .form-group:nth-child(7) { animation-delay: 1.3s; }
        .form-group:nth-child(8) { animation-delay: 1.4s; }
        .form-group.full-width { animation-delay: 1.5s; }
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
            transition: box-shadow 0.3s, border-color 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2e3c81;
            box-shadow: 0 0 0 2px #2e3c8133;
        }
        .button-next, .btn-salir {
            transition: background-color 0.3s, transform 0.1s;
        }
        .button-next:active, .btn-salir:active {
            transform: scale(0.97);
        }
        .form-group.full-width {
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
            background-color:rgb(172, 45, 45);
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
    <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

    <h3>Enfermedades (marque las que apliquen)</h3>
    <div class="form-container">
        <!-- Columna 1 -->
        <div class="form-column">
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Varicela/Rubeola/Sarampión" 
                <?php echo getChecked('Varicela/Rubeola/Sarampión') ?> > Varicela/Rubeola/Sarampión</label>
            </div>
            
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades respiratorias" 
                <?php echo getChecked('Enfermedades respiratorias') ?> > Enfermedades respiratorias </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades pulmonares" <?php echo getChecked('Enfermedades pulmonares') ?> > Enfermedades pulmonares </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Asma bronquial" 
                <?php echo getChecked('Asma bronquial') ?> > Asma bronquial </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades del corazón" 
                <?php echo getChecked('Enfermedades del corazón') ?> > Enfermedades del corazón </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Presión alta o baja" 
                <?php echo getChecked('Presión alta o baja') ?> > Presión alta o baja </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Vértigos" 
                <?php echo getChecked('Vértigos') ?> > Vértigos </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Anemia/Sangrado anormal" 
                <?php echo getChecked('Anemia/Sangrado anormal') ?> > Anemia/Sangrado anormal </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Tuberculosos" 
                <?php echo getChecked('Tuberculosos') ?> > Tuberculosos </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Varices/Hemorroides" 
                <?php echo getChecked('Varices/Hemorroides') ?> > Varices/Hemorroides </label>
            </div>

            


            <!-- Agregar más enfermedades aquí -->
            
        </div>

        <!-- Columna 2 -->
        <div class="form-column">
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Cefalea" 
                <?php echo getChecked('Cefalea') ?> > Cefalea </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Hernias" 
                <?php echo getChecked('Hernias') ?> > Hernias </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Problemas en la espalda" 
                <?php echo getChecked('Problemas en la espalda') ?> > Problemas en la espalda </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Golpes en la columna" 
                <?php echo getChecked('Golpes en la columna') ?> > Golpes en la columna </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Golpes en la cabeza" 
                <?php echo getChecked('Golpes en la cabeza') ?> > Golpes en la cabeza </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Artritis o Reumatismo" 
                <?php echo getChecked('Artritis o Reumatismo') ?> > Artritis o Reumatismo </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Depresión/Ansiedad" 
                <?php echo getChecked('Depresión/Ansiedad') ?> > Depresión/Ansiedad </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Paludismo" 
                <?php echo getChecked('Paludismo') ?> > Paludismo </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Sensación de Hormigueo" 
                <?php echo getChecked('Sensación de Hormigueo') ?> > Sensación de Hormigueo </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades Gastrointestinales" 
                <?php echo getChecked('Enfermedades Gastrointestinales') ?> > Enfermedades Gastrointestinales </label>
            </div>
            <!-- Agregar más enfermedades aquí -->
            
        </div>

        <!-- Columna 3 -->
        <div class="form-column">
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Gastritis/Ulcera/Colitis" 
                <?php echo getChecked('Gastritis/Ulcera/Colitis') ?> > Gastritis/Ulcera/Colitis </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades del higado" 
                <?php echo getChecked('Enfermedades del higado') ?> > Enfermedades del higado </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Diabetes" 
                <?php echo getChecked('Diabetes') ?> > Diabetes </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades del riñon" 
                <?php echo getChecked('Enfermedades del riñon') ?> > Enfermedades del riñon </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades de genitales" 
                <?php echo getChecked('Enfermedades de genitales') ?> > Enfermedades de genitales </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Convulsiones (Epilepsia)" 
                <?php echo getChecked('Convulsiones (Epilepsia)') ?> > Convulsiones (Epilepsia) </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Paroditis" 
                <?php echo getChecked('Paroditis') ?> > Paroditis </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Transtornos de la pies" 
                <?php echo getChecked('Transtornos de la pies') ?> > Transtornos de la pies </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Heridas/Quemaduras" 
                <?php echo getChecked('Heridas/Quemaduras') ?> > Heridas/Quemaduras </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades Oculares" 
                <?php echo getChecked('Enfermedades Oculares') ?> > Enfermedades Oculares </label>
            </div>
            <!-- Agregar más enfermedades aquí -->
            
        </div>

        <!-- Columna 4 -->
        <div class="form-column">
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Enfermedades dentales" 
                <?php echo getChecked('Enfermedades dentales') ?> > Enfermedades dentales </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Problemas de audicion" 
                <?php echo getChecked('Problemas de audicion') ?> > Problemas de audicion </label>
            </div>
            
            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Acufeno/Tinitus" 
                <?php echo getChecked('Acufeno/Tinitus') ?> > Acufeno/Tinitus </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Usa prótesis" 
                <?php echo getChecked('Usa prótesis') ?> > Usa prótesis </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="Tumores o cáncer" 
                <?php echo getChecked('Tumores o cáncer') ?> > Tumores o cáncer </label>
            </div>

            <div class="enfermedad-item">
                <label><input type="checkbox" name="enfermedades[]" value="COVID 19" 
                <?php echo getChecked('COVID 19') ?> > COVID 19 </label>
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
        <textarea name="fracturas_esguinces" rows="3" value="<?php echo isset($antecedentes['']) ? $antecedentes[''] : '' ?>"></textarea>
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