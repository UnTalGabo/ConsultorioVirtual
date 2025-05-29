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
            max-width: 800px;
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

        .form-group {
            margin-bottom: 18px;
            padding: 18px 20px;
            background: #f8fafc;
            border-radius: 10px;
            box-shadow: 0 2px 8px 0 rgba(30, 42, 120, 0.06);
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInGroup 0.7s forwards;
        }

        .form-group:nth-child(n) { animation-delay: 0.1s; }
        .form-group:nth-child(n+1) { animation-delay: 0.2s; }
        .form-group:nth-child(n+2) { animation-delay: 0.3s; }
        .form-group:nth-child(n+3) { animation-delay: 0.4s; }
        .form-group:nth-child(n+4) { animation-delay: 0.5s; }
        .form-group:nth-child(n+5) { animation-delay: 0.6s; }
        .form-group:nth-child(n+6) { animation-delay: 0.7s; }
        .form-group:nth-child(n+7) { animation-delay: 0.8s; }

        @keyframes fadeInGroup {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-label {
            font-weight: 500;
            color: #1e2a78;
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

        .form-control:focus, .form-select:focus {
            border-color: #2e3c81;
            box-shadow: 0 0 0 2px #2e3c8133;
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

        @media (max-width: 767px) {
            .main-container {
                margin-top: 20px;
            }
            .card {
                padding: 0.5rem;
            }
            .form-group {
                padding: 10px 8px;
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
                <i class="bi bi-person-badge-fill"></i>
                Paso 4: Antecedentes Personales No Patológicos
            </h2>
            <p class="mb-4">Paciente: <strong><?php echo $paciente['nombre_completo']; ?></strong></p>

            <form action="../php/guardar_paso4.php" method="post">
                <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

                <!-- Tabaquismo -->
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="fuma" id="fuma" <?php echo isset($antecedentes['fuma']) && $antecedentes['fuma'] == 1 ? 'checked' : ''; ?>>
                        ¿Fuma?
                    </label>
                    <div id="fuma_fields" class="conditional-field">
                        <label class="form-label">Cigarros por día:
                            <input type="number" name="cigarros_dia" min="0" class="form-control"
                                value="<?php echo $antecedentes['cigarros_dia']; ?>">
                        </label>
                        <label class="form-label">Años fumando:
                            <input type="number" name="anos_fumando" min="0" class="form-control"
                                value="<?php echo $antecedentes['anos_fumando']; ?>">
                        </label>
                    </div>
                </div>

                <!-- Consumo de alcohol -->
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="bebe" id="bebe" <?php echo isset($antecedentes['bebe']) && $antecedentes['bebe'] == 1 ? 'checked' : ''; ?>>
                        ¿Consume alcohol?
                    </label>
                    <div id="bebe_fields" class="conditional-field">
                        <label class="form-label">Años bebiendo:
                            <input type="number" name="anos_bebiendo" min="0" class="form-control"
                                value="<?php echo $antecedentes['anos_bebiendo']; ?>">
                        </label>
                        <label class="form-label">Frecuencia:
                            <select name="frecuencia_alcohol" class="form-select">
                                <option value="">Selecciona</option>
                                <option value="Ocasional" <?php echo (isset($antecedentes['frecuencia_alcohol']) && $antecedentes['frecuencia_alcohol'] == 'Ocasional') ? 'selected' : ''; ?>>Ocasional</option>
                                <option value="Semanal" <?php echo (isset($antecedentes['frecuencia_alcohol']) && $antecedentes['frecuencia_alcohol'] == 'Semanal') ? 'selected' : ''; ?>>Semanal</option>
                                <option value="Diario" <?php echo (isset($antecedentes['frecuencia_alcohol']) && $antecedentes['frecuencia_alcohol'] == 'Diario') ? 'selected' : ''; ?>>Diario</option>
                            </select>
                        </label>
                    </div>
                </div>

                <!-- Medicamentos controlados -->
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="medicamentos_controlados"
                            <?php echo isset($antecedentes['medicamentos_controlados']) && $antecedentes['medicamentos_controlados'] == 1 ? 'checked' : ''; ?>>
                        ¿Usa medicamentos controlados?
                    </label>
                </div>

                <!-- Otras preguntas -->
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="usa_drogas" id="drogas"
                            <?php echo isset($antecedentes['usa_drogas']) && $antecedentes['usa_drogas'] == 1 ? 'checked' : ''; ?>>
                        ¿Ha usado drogas?
                    </label>
                    <div id="drogas_fields" class="conditional-field">
                        <label class="form-label">Tipo de droga:
                            <input type="text" name="tipo_droga" class="form-control"
                                value="<?php echo isset($antecedentes['tipo_droga']) ? $antecedentes['tipo_droga'] : ''; ?>">
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="practica_deporte" id="deporte"
                            <?php echo isset($antecedentes['practica_deporte']) && $antecedentes['practica_deporte'] == 1 ? 'checked' : ''; ?>>
                        ¿Practica deporte?
                    </label>
                    <div id="deporte_fields" class="conditional-field">
                        <label class="form-label">¿Cuál deporte?
                            <input type="text" name="tipo_deporte" class="form-control"
                                value="<?php echo isset($antecedentes['tipo_deporte']) ? $antecedentes['tipo_deporte'] : ''; ?>">
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="tatuajes"
                            <?php echo isset($antecedentes['tatuajes']) &&
                                $antecedentes['tatuajes'] == 1 ? 'checked' : ''; ?>>
                        ¿Tiene algún tatuaje?
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="transfusiones" id="transfusiones"
                            <?php echo isset($antecedentes['transfusiones']) &&
                                $antecedentes['transfusiones'] == 1 ? 'checked' : ''; ?>>
                        ¿Acepta transfusiones de sangre?
                    </label>
                    <div id="transfusiones_fields" class="conditional-field">
                        <label class="form-label">¿Ha recibido transfusiones?
                            <input type="checkbox" name="transfusiones_recibidas"
                                <?php echo isset($antecedentes['transfuciones_recibidas']) &&
                                    $antecedentes['transfuciones_recibidas'] == 1 ? 'checked' : ''; ?>>
                        </label>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" name="accion" value="guardar_salir">
                        <i class="bi bi-save2"></i> Guardar y Salir
                    </button>
                    <button type="submit" class="btn btn-success btn-lg" name="accion" value="guardar_continuar">
                        <i class="bi bi-arrow-right-circle"></i> Guardar y Continuar &raquo;
                    </button>
                    <a href="../views/ver_pacientes.php" class="btn btn-danger btn-lg" name="accion" value="salir_sin_guardar">
                        <i class="bi bi-box-arrow-left"></i> Salir sin guardar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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