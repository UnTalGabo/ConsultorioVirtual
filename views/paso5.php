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
        .form-group:nth-child(1) { animation-delay: 0.2s; }
        .form-group:nth-child(2) { animation-delay: 0.3s; }
        .form-group:nth-child(3) { animation-delay: 0.4s; }
        .form-group:nth-child(4) { animation-delay: 0.5s; }
        .form-group:nth-child(5) { animation-delay: 0.6s; }
        .form-group:nth-child(6) { animation-delay: 0.7s; }
        .form-group:nth-child(7) { animation-delay: 0.8s; }
        .form-group:nth-child(8) { animation-delay: 0.9s; }
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
            transition: box-shadow 0.3s, border-color 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2e3c81;
            box-shadow: 0 0 0 2px #2e3c8133;
        }
        .button-container {
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }
        .button-next, .btn-salir {
            transition: background-color 0.3s, transform 0.1s;
        }
        .button-next:active, .btn-salir:active {
            transform: scale(0.97);
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
    </style>
</head>
<body>

<h2>Paso 5: Antecedentes Gineco-Obstétricos</h2>
<p>Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

<form action="../php/guardar_paso5.php" method="post">
    <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

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
        <button type="button" class="btn-salir" onclick="window.location.href='../views/ver_pacientes.php'">Salir</button>
    </div>
</form>

</body>
</html>