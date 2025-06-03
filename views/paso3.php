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

$sql = "SELECT enfermedad, parentesco FROM enfermedades_heredo WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();
$enfermedades = [];
while ($row = $resultado->fetch_assoc()) {
    $enfermedades[] = $row;
}
$stmt->close();

function getSelected($efnfermedad, $parentesco)
{
    global $enfermedades;
    foreach ($enfermedades as $enfermedad) {
        if ($enfermedad['enfermedad'] == $efnfermedad && $enfermedad['parentesco'] == $parentesco) {
            return 'selected';
        }
    }
}
function getChecked($efnfermedad)
{
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
    <title>Paso 3: Antecedentes Heredo-Familiares</title>
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

        .navbar-brand, .navbar-brand i {
            color: #fff !important;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .main-container {
            max-width: 1100px;
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

        .section-title {
            color: #2e3c81;
            font-weight: 700;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .contenedor-columnas {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }

        .columna {
            flex: 1 1 350px;
            min-width: 320px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #f8fafc;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px 0 rgba(30, 42, 120, 0.06);
        }

        th, td {
            border: 1px solid #e9ecef;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #2e3c81;
            color: white;
        }

        select:focus, input[type="checkbox"]:focus {
            outline: 2px solid #2e3c81;
            box-shadow: 0 0 0 2px #2e3c8133;
            transition: box-shadow 0.3s, outline 0.3s;
        }

        .btn-primary {
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
        .btn-primary:hover, .btn-primary:focus {
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

        @media (max-width: 991px) {
            .main-container {
                max-width: 98vw;
            }
            .contenedor-columnas {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
        @media (max-width: 767px) {
            .main-container {
                margin-top: 20px;
                padding: 0 2px;
            }
            .card {
                padding: 0.5rem;
            }
            th, td {
                font-size: 0.95rem;
                padding: 7px;
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
            <h2 class="section-title mb-3">
                <i class="bi bi-people-fill"></i>
                Paso 3: Antecedentes Heredo-Familiares
            </h2>
            <p class="mb-4">Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

            <form action="../php/guardar_paso3.php" method="post" id="formPaso3">
                <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

                <div class="contenedor-columnas mb-4">
                    <!-- Columna 1 -->
                    <div class="columna mb-3 mb-md-0">
                        <table>
                            <thead>
                                <tr>
                                    <th>Enfermedad</th>
                                    <th>¿Presente?</th>
                                    <th>¿Quién?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ...enfermedades columna 1... -->
                                <!-- Copia aquí el contenido de la columna 1 de tu tabla original -->
<?php /* --- Columna 1 --- */ ?>
<tr>
    <td>Presión alta/baja</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="presion" <?php echo getChecked("Presión alta/baja") ?>>
        <input type="hidden" name="nombre_enfermedad_presion" value="Presión alta/baja">
    </td>
    <td>
        <select name="presion_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Presión alta/baja", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Presión alta/baja", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Presión alta/baja", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Presión alta/baja", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Presión alta/baja", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Presión alta/baja", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Presión alta/baja", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Presión alta/baja", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Vértigos</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="vertigos" <?php echo getChecked("Vértigos") ?>>
        <input type="hidden" name="nombre_enfermedad_vertigos" value="Vértigos">
    </td>
    <td>
        <select name="vertigos_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Vértigos", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Vértigos", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Vértigos", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Vértigos", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Vértigos", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Vértigos", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Vértigos", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Vértigos", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Diabetes</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="diabetes" <?php echo getChecked("Diabetes") ?>>
        <input type="hidden" name="nombre_enfermedad_diabetes" value="Diabetes">
    </td>
    <td>
        <select name="diabetes_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Diabetes", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Diabetes", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Diabetes", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Diabetes", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Diabetes", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Diabetes", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Diabetes", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Diabetes", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Enfermedades del Corazón</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="corazon" <?php echo getChecked("Enfermedades del Corazón") ?>>
        <input type="hidden" name="nombre_enfermedad_corazon" value="Enfermedades del Corazón">
    </td>
    <td>
        <select name="corazon_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Enfermedades del Corazón", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Enfermedades del Corazón", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Enfermedades del Corazón", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Enfermedades del Corazón", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Enfermedades del Corazón", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Enfermedades del Corazón", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Enfermedades del Corazón", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Enfermedades del Corazón", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Enfermedades Pulmonares</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="pulmonares" <?php echo getChecked("Enfermedades Pulmonares") ?>>
        <input type="hidden" name="nombre_enfermedad_pulmonares" value="Enfermedades Pulmonares">
    </td>
    <td>
        <select name="pulmonares_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Enfermedades Pulmonares", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Enfermedades Pulmonares", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Enfermedades Pulmonares", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Enfermedades Pulmonares", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Enfermedades Pulmonares", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Enfermedades Pulmonares", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Enfermedades Pulmonares", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Enfermedades Pulmonares", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Enfermedades del Riñon</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="rinon" <?php echo getChecked("Enfermedades del Riñon") ?>>
        <input type="hidden" name="nombre_enfermedad_rinon" value="Enfermedades del Riñon">
    </td>
    <td>
        <select name="rinon_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Enfermedades del Riñon", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Enfermedades del Riñon", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Enfermedades del Riñon", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Enfermedades del Riñon", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Enfermedades del Riñon", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Enfermedades del Riñon", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Enfermedades del Riñon", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Enfermedades del Riñon", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Enfermedades del Higado</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="higado" <?php echo getChecked("Enfermedades del Higado") ?>>
        <input type="hidden" name="nombre_enfermedad_higado" value="Enfermedades del Higado">
    </td>
    <td>
        <select name="higado_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Enfermedades del Higado", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Enfermedades del Higado", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Enfermedades del Higado", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Enfermedades del Higado", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Enfermedades del Higado", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Enfermedades del Higado", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Enfermedades del Higado", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Enfermedades del Higado", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Alergias</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="alergias" <?php echo getChecked("Alergias") ?>>
        <input type="hidden" name="nombre_enfermedad_alergias" value="Alergias">
    </td>
    <td>
        <select name="alergias_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Alergias", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Alergias", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Alergias", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Alergias", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Alergias", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Alergias", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Alergias", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Alergias", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Tumores o cáncer</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="tumores" <?php echo getChecked("Tumores o cáncer") ?>>
        <input type="hidden" name="nombre_enfermedad_tumores" value="Tumores o cáncer">
    </td>
    <td>
        <select name="tumores_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Tumores o cáncer", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Tumores o cáncer", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Tumores o cáncer", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Tumores o cáncer", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Tumores o cáncer", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Tumores o cáncer", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Tumores o cáncer", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Tumores o cáncer", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Asma bronquial</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="asma" <?php echo getChecked("Asma bronquial") ?>>
        <input type="hidden" name="nombre_enfermedad_asma" value="Asma bronquial">
    </td>
    <td>
        <select name="asma_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Asma bronquial", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Asma bronquial", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Asma bronquial", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Asma bronquial", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Asma bronquial", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Asma bronquial", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Asma bronquial", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Asma bronquial", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Gastritis/Ulcera</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="gastritis" <?php echo getChecked("Gastritis/Ulcera") ?>>
        <input type="hidden" name="nombre_enfermedad_gastritis" value="Gastritis/Ulcera">
    </td>
    <td>
        <select name="gastritis_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Gastritis/Ulcera", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Gastritis/Ulcera", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Gastritis/Ulcera", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Gastritis/Ulcera", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Gastritis/Ulcera", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Gastritis/Ulcera", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Gastritis/Ulcera", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Gastritis/Ulcera", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<?php /* --- Fin columna 1 --- */ ?>
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
<?php /* --- Columna 2 --- */ ?>
<tr>
    <td>Flebitis/Várices</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="varices" <?php echo getChecked("Flebitis/Várices") ?>>
        <input type="hidden" name="nombre_enfermedad_varices" value="Flebitis/Várices">
    </td>
    <td>
        <select name="varices_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Flebitis/Várices", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Flebitis/Várices", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Flebitis/Várices", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Flebitis/Várices", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Flebitis/Várices", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Flebitis/Várices", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Flebitis/Várices", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Flebitis/Várices", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Artritis</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="artritis" <?php echo getChecked("Artritis") ?>>
        <input type="hidden" name="nombre_enfermedad_artritis" value="Artritis">
    </td>
    <td>
        <select name="artritis_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Artritis", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Artritis", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Artritis", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Artritis", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Artritis", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Artritis", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Artritis", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Artritis", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Alteraciones del sueño</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="sueno" <?php echo getChecked("Alteraciones del sueño") ?>>
        <input type="hidden" name="nombre_enfermedad_sueno" value="Alteraciones del sueño">
    </td>
    <td>
        <select name="sueno_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Alteraciones del sueño", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Alteraciones del sueño", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Alteraciones del sueño", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Alteraciones del sueño", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Alteraciones del sueño", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Alteraciones del sueño", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Alteraciones del sueño", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Alteraciones del sueño", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Acufeno/Tinitus</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="tinitus" <?php echo getChecked("Acufeno/Tinitus") ?>>
        <input type="hidden" name="nombre_enfermedad_tinitus" value="Acufeno/Tinitus">
    </td>
    <td>
        <select name="tinitus_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Acufeno/Tinitus", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Acufeno/Tinitus", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Acufeno/Tinitus", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Acufeno/Tinitus", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Acufeno/Tinitus", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Acufeno/Tinitus", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Acufeno/Tinitus", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Acufeno/Tinitus", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Problemas de espalda</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="espalda" <?php echo getChecked("Problemas de espalda") ?>>
        <input type="hidden" name="nombre_enfermedad_espalda" value="Problemas de espalda">
    </td>
    <td>
        <select name="espalda_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Problemas de espalda", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Problemas de espalda", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Problemas de espalda", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Problemas de espalda", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Problemas de espalda", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Problemas de espalda", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Problemas de espalda", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Problemas de espalda", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Sensación de hormigueo</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="hormigueo" <?php echo getChecked("Sensación de hormigueo") ?>>
        <input type="hidden" name="nombre_enfermedad_hormigueo" value="Sensación de hormigueo">
    </td>
    <td>
        <select name="hormigueo_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Sensación de hormigueo", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Sensación de hormigueo", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Sensación de hormigueo", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Sensación de hormigueo", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Sensación de hormigueo", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Sensación de hormigueo", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Sensación de hormigueo", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Sensación de hormigueo", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Convulsiones</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="convulsiones" <?php echo getChecked("Convulsiones") ?>>
        <input type="hidden" name="nombre_enfermedad_convulsiones" value="Convulsiones">
    </td>
    <td>
        <select name="convulsiones_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Convulsiones", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Convulsiones", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Convulsiones", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Convulsiones", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Convulsiones", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Convulsiones", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Convulsiones", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Convulsiones", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Debilidad Muscular</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="debilidad" <?php echo getChecked("Debilidad Muscular") ?>>
        <input type="hidden" name="nombre_enfermedad_debilidad" value="Debilidad Muscular">
    </td>
    <td>
        <select name="debilidad_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Debilidad Muscular", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Debilidad Muscular", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Debilidad Muscular", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Debilidad Muscular", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Debilidad Muscular", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Debilidad Muscular", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Debilidad Muscular", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Debilidad Muscular", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Osteoporosis</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="osteoporosis" <?php echo getChecked("Osteoporosis") ?>>
        <input type="hidden" name="nombre_enfermedad_osteoporosis" value="Osteoporosis">
    </td>
    <td>
        <select name="osteoporosis_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Osteoporosis", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Osteoporosis", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Osteoporosis", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Osteoporosis", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Osteoporosis", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Osteoporosis", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Osteoporosis", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Osteoporosis", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>Hernias</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="hernias" <?php echo getChecked("Hernias") ?>>
        <input type="hidden" name="nombre_enfermedad_hernias" value="Hernias">
    </td>
    <td>
        <select name="hernias_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("Hernias", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("Hernias", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("Hernias", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("Hernias", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("Hernias", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("Hernias", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("Hernias", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("Hernias", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<tr>
    <td>COVID 19</td>
    <td>
        <input type="checkbox" name="enfermedades[]" value="covid" <?php echo getChecked("COVID 19") ?>>
        <input type="hidden" name="nombre_enfermedad_covid" value="COVID 19">
    </td>
    <td>
        <select name="covid_quien">
            <option value="">Seleccionar</option>
            <option value="APO" <?php echo getSelected("COVID 19", 'Abuelo Paterno') ?>>Abuelo Paterno</option>
            <option value="APA" <?php echo getSelected("COVID 19", 'Abuela Paterna') ?>>Abuela Paterna</option>
            <option value="AMO" <?php echo getSelected("COVID 19", 'Abuelo Materno') ?>>Abuelo Materno</option>
            <option value="AMA" <?php echo getSelected("COVID 19", 'Abuela Materna') ?>>Abuela Materna</option>
            <option value="P" <?php echo getSelected("COVID 19", 'Padre') ?>>Padre</option>
            <option value="M" <?php echo getSelected("COVID 19", 'Madre') ?>>Madre</option>
            <option value="H" <?php echo getSelected("COVID 19", 'Hermanos') ?>>Hermanos</option>
            <option value="T" <?php echo getSelected("COVID 19", 'Tíos') ?>>Tíos</option>
        </select>
    </td>
</tr>
<?php /* --- Fin columna 2 --- */ ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
                    <button type="button" class="btn btn-danger btn-lg" onclick="window.location.href='../views/ver_pacientes.php'">
                        <i class="bi bi-box-arrow-left"></i> Salir sin guardar
                    </button>
                    <div class="ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" name="accion" value="guardar_salir">
                            <i class="bi bi-save2"></i> Guardar y Salir
                        </button>
                        <button type="submit" class="btn btn-success btn-lg" name="accion" value="guardar_continuar">
                            <i class="bi bi-arrow-right-circle"></i> Guardar y Continuar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para deshabilitar selects cuando el checkbox no está marcado
        function toggleSelect(checkbox) {
            const row = checkbox.closest('tr');
            const select = row.querySelector('select');
            select.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                select.value = '';
            }
        }

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
</body>

</html>