<?php
session_start();
if (
    !isset($_SESSION['usuario_rol']) ||
    !in_array($_SESSION['usuario_rol'], ['doctor', 'admin'])
) {
    header('Location: login.php');
    exit();
}
require_once "../../php/conexion.php";

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

$sql = "SELECT enfermedad, parentesco, tipo FROM enfermedades_heredo WHERE id_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empleado);
$stmt->execute();
$resultado = $stmt->get_result();
$enfermedades = [];
while ($row = $resultado->fetch_assoc()) {
    $enfermedades[] = $row;
}
$corazon_tipo = '';
foreach ($enfermedades as $enfermedad) {
    if ($enfermedad['enfermedad'] == 'Enfermedades del Corazón' && !empty($enfermedad['tipo'])) {
        $corazon_tipo = $enfermedad['tipo'];
        break;
    }
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

        .navbar-brand,
        .navbar-brand i {
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

        th,
        td {
            border: 1px solid #e9ecef;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #2e3c81;
            color: white;
        }

        select:focus,
        input[type="checkbox"]:focus {
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

        .btn-primary:hover,
        .btn-primary:focus {
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

            th,
            td {
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
            <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
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
            <h3 class="mb-4">Seleccione las enfermedades presentes en la familia del paciente y quiénes las padecen.</h3>

            <form action="../../php/registro/guardar_paso3.php" method="post" id="formPaso3">
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
                                            <option value="ABOP" <?php echo getSelected("Presión alta/baja", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Presión alta/baja", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Presión alta/baja", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Presión alta/baja", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Presión alta/baja", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Presión alta/baja", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Presión alta/baja", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Presión alta/baja", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Vértigos", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Vértigos", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Vértigos", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Vértigos", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Vértigos", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Vértigos", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Vértigos", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Vértigos", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Diabetes", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Diabetes", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Diabetes", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Diabetes", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Diabetes", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Diabetes", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Diabetes", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Diabetes", 'T') ?>>Tíos</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Enfermedades del Corazón
                                        <input type="text" name="corazon_tipo" id="corazon_tipo_input" class="form-control mt-2" placeholder="¿Qué tipo?" style="display:none;" maxlength="20"
                                        value="<?php echo ($corazon_tipo); ?>">
                                    </td>
                                    <td>
                                        <input type="checkbox" id="check_corazon" name="enfermedades[]" value="corazon" <?php echo getChecked("Enfermedades del Corazón") ?>>
                                        <input type="hidden" name="nombre_enfermedad_corazon" value="Enfermedades del Corazón">
                                    </td>
                                    <td>
                                        <select name="corazon_quien">
                                            <option value="">Seleccionar</option>
                                            <option value="ABOP" <?php echo getSelected("Enfermedades del Corazón", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Enfermedades del Corazón", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Enfermedades del Corazón", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Enfermedades del Corazón", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Enfermedades del Corazón", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Enfermedades del Corazón", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Enfermedades del Corazón", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Enfermedades del Corazón", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Enfermedades Pulmonares", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Enfermedades Pulmonares", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Enfermedades Pulmonares", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Enfermedades Pulmonares", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Enfermedades Pulmonares", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Enfermedades Pulmonares", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Enfermedades Pulmonares", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Enfermedades Pulmonares", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Enfermedades del Riñon", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Enfermedades del Riñon", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Enfermedades del Riñon", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Enfermedades del Riñon", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Enfermedades del Riñon", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Enfermedades del Riñon", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Enfermedades del Riñon", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Enfermedades del Riñon", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Enfermedades del Higado", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Enfermedades del Higado", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Enfermedades del Higado", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Enfermedades del Higado", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Enfermedades del Higado", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Enfermedades del Higado", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Enfermedades del Higado", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Enfermedades del Higado", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Alergias", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Alergias", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Alergias", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Alergias", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Alergias", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Alergias", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Alergias", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Alergias", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Tumores o cáncer", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Tumores o cáncer", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Tumores o cáncer", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Tumores o cáncer", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Tumores o cáncer", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Tumores o cáncer", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Tumores o cáncer", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Tumores o cáncer", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Asma bronquial", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Asma bronquial", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Asma bronquial", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Asma bronquial", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Asma bronquial", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Asma bronquial", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Asma bronquial", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Asma bronquial", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Gastritis/Ulcera", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Gastritis/Ulcera", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Gastritis/Ulcera", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Gastritis/Ulcera", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Gastritis/Ulcera", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Gastritis/Ulcera", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Gastritis/Ulcera", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Gastritis/Ulcera", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Flebitis/Várices", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Flebitis/Várices", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Flebitis/Várices", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Flebitis/Várices", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Flebitis/Várices", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Flebitis/Várices", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Flebitis/Várices", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Flebitis/Várices", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Artritis", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Artritis", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Artritis", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Artritis", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Artritis", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Artritis", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Artritis", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Artritis", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Alteraciones del sueño", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Alteraciones del sueño", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Alteraciones del sueño", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Alteraciones del sueño", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Alteraciones del sueño", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Alteraciones del sueño", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Alteraciones del sueño", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Alteraciones del sueño", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Acufeno/Tinitus", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Acufeno/Tinitus", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Acufeno/Tinitus", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Acufeno/Tinitus", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Acufeno/Tinitus", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Acufeno/Tinitus", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Acufeno/Tinitus", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Acufeno/Tinitus", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Problemas de espalda", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Problemas de espalda", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Problemas de espalda", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Problemas de espalda", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Problemas de espalda", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Problemas de espalda", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Problemas de espalda", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Problemas de espalda", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Sensación de hormigueo", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Sensación de hormigueo", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Sensación de hormigueo", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Sensación de hormigueo", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Sensación de hormigueo", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Sensación de hormigueo", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Sensación de hormigueo", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Sensación de hormigueo", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Convulsiones", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Convulsiones", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Convulsiones", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Convulsiones", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Convulsiones", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Convulsiones", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Convulsiones", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Convulsiones", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Debilidad Muscular", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Debilidad Muscular", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Debilidad Muscular", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Debilidad Muscular", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Debilidad Muscular", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Debilidad Muscular", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Debilidad Muscular", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Debilidad Muscular", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Osteoporosis", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Osteoporosis", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Osteoporosis", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Osteoporosis", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Osteoporosis", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Osteoporosis", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Osteoporosis", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Osteoporosis", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("Hernias", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("Hernias", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("Hernias", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("Hernias", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("Hernias", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("Hernias", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("Hernias", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("Hernias", 'T') ?>>Tíos</option>
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
                                            <option value="ABOP" <?php echo getSelected("COVID 19", 'ABOP') ?>>Abuelo Paterno</option>
                                            <option value="ABAP" <?php echo getSelected("COVID 19", 'ABAP') ?>>Abuela Paterna</option>
                                            <option value="ABOM" <?php echo getSelected("COVID 19", 'ABOM') ?>>Abuelo Materno</option>
                                            <option value="ABAM" <?php echo getSelected("COVID 19", 'ABAM') ?>>Abuela Materna</option>
                                            <option value="P" <?php echo getSelected("COVID 19", 'P') ?>>Padre</option>
                                            <option value="M" <?php echo getSelected("COVID 19", 'M') ?>>Madre</option>
                                            <option value="H" <?php echo getSelected("COVID 19", 'H') ?>>Hermanos</option>
                                            <option value="T" <?php echo getSelected("COVID 19", 'T') ?>>Tíos</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php /* --- Fin columna 2 --- */ ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
                    <a href="../ver_pacientes.php" class="btn btn-danger btn-lg" onclick="return confirm('¿Estás seguro de que quieres salir sin guardar?');">
                        <i class="bi bi-box-arrow-left"></i> Salir sin guardar
                    </a>
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
    <script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  const btnContinuar = form.querySelector('button[name="accion"][value="guardar_continuar"]');
  form.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
      e.preventDefault();
      btnContinuar.click();
    }
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkCorazon = document.getElementById('check_corazon');
    const inputTipo = document.getElementById('corazon_tipo_input');
    function toggleTipoInput() {
        inputTipo.style.display = checkCorazon.checked ? 'block' : 'none';
        if (!checkCorazon.checked) inputTipo.value = '';
    }
    if (checkCorazon && inputTipo) {
        toggleTipoInput();
        checkCorazon.addEventListener('change', toggleTipoInput);
    }
});
</script>
</body>

</html>