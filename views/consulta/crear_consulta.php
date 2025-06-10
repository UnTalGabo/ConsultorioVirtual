<?php
require_once "../../php/conexion.php";

date_default_timezone_set('America/Mexico_City');

// Validar ID del empleado
$id_empleado = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener nombre del paciente
if ($id_empleado > 0) {
  $sql = "SELECT * FROM pacientes WHERE id_empleado = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id_empleado);
  $stmt->execute();
  $result = $stmt->get_result();
  $paciente = $result->fetch_assoc();
  $stmt->close();
}

$hora = date('H:i');
$fecha = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consulta</title>
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
            max-width: 1000px;
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

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
            animation: sectionFadeIn 0.7s forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .form-section:nth-child(1) {
            animation-delay: 0.1s;
        }

        .form-section:nth-child(2) {
            animation-delay: 0.2s;
        }

        .form-section:nth-child(3) {
            animation-delay: 0.3s;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        @keyframes sectionFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section h3 {
            color: #2e3c81;
            font-weight: 600;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            color: #1e2a78;
            font-weight: 500;
        }

        .form-control:focus,
        .form-select:focus,
        textarea:focus {
            border-color: #2e3c81;
            box-shadow: 0 0 0 2px #2e3c8133;
        }

        .btn-primary,
        .btn-success {
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
        .btn-primary:focus,
        .btn-success:hover,
        .btn-success:focus {
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

        .imc-display {
            font-weight: bold;
            color: #2e3c81;
            padding: 5px;
            background-color: #e9ecef;
            border-radius: 4px;
            text-align: center;
        }

        .evaluation-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .evaluation-column {
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            background: #f8f9fa;
        }

        .evaluation-item {
            margin-bottom: 20px;
        }

        .evaluation-item h4 {
            margin-top: 0;
            color: #2e3c81;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .result-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-top: 20px;
        }

        .signature-box {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        @media (max-width: 991px) {
            .main-container {
                padding: 0 5px;
            }

            .evaluation-grid,
            .result-grid {
                grid-template-columns: 1fr;
            }

            .button-container {
                flex-direction: column;
                gap: 10px;
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
            <h2 class="text-center mb-4 fw-bold">
                <i class="bi bi-clipboard2-pulse me-2"></i>
                Consulta
            </h2>
            <?php if ($paciente['nombre_completo']): ?>
                <div class="alert alert-info text-center mb-4">
                    <strong>Paciente:</strong> <?php echo $paciente['nombre_completo']?>
                </div>
            <?php endif; ?>

            <form action="../../php/consulta/guardar_consulta.php" method="post" autocomplete="off" id="formConsulta">
                <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">
                <!-- Hora de entrada y salida -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="hora_salida" class="form-label">Fecha:</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" 
                        value="<?php echo $fecha ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="hora_entrada" class="form-label">Hora de entrada:</label>
                        <input type="text" class="form-control" id="hora_entrada" name="hora_entrada" 
                            value="<?php echo $hora; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="hora_salida" class="form-label">Hora de salida:</label>
                        <input type="text" class="form-control" id="hora_salida" name="hora_salida" readonly>
                    </div>
                </div>
                <!-- Fin hora de entrada y salida -->

                <div class="form-section">
                    <h3><i class="bi bi-heart-pulse"></i> SOMATOMETRÍA / SIGNOS VITALES</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="talla" class="form-label">Talla (cm):</label>
                            <input type="number" name="talla" id="talla" step="0.01" class="form-control"
                                oninput="calcularIMC()">
                        </div>
                        <div class="col-md-4">
                            <label for="peso" class="form-label">Peso (kg):</label>
                            <input type="number" name="peso" id="peso" step="0.1" class="form-control"
                                oninput="calcularIMC()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IMC:</label>
                            <div class="imc-display" id="imc_display">--</div>
                            <input type="hidden" name="imc" id="imc">
                            <div id="imc_info" class="small text-secondary"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="fc" class="form-label">FC (x'):</label>
                            <input type="number" name="fc" id="fc" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="fr" class="form-label">FR (x'):</label>
                            <input type="number" name="fr" id="fr" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="temp" class="form-label">Temp (°C):</label>
                            <input type="number" name="temp" id="temp" step="0.1" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="perimetro_abdominal" class="form-label">Perímetro Abd. (cm):</label>
                            <input type="number" name="perimetro_abdominal" id="perimetro_abdominal" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="presion_arterial" class="form-label">Presión arterial (mm/Hg):</label>
                            <input type="text" name="presion_arterial" id="presion_arterial" class="form-control" placeholder="120/80">
                        </div>
                        <div class="col-md-4">
                            <label for="spo2" class="form-label">SpO2 (%):</label>
                            <input type="number" name="spo2" id="spo2" min="0" max="100" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="evaluation-grid">
                        <div class="evaluation-column" style="grid-column: 1 / -1;">
                            <div class="evaluation-item">
                                <h4>Motivo</h4>
                                <textarea name="motivo" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="bi bi-person-vcard"></i> EVALUACIÓN FÍSICA</h3>
                    <div class="evaluation-grid">
                        <div class="evaluation-column">
                            <div class="evaluation-item">
                                <h4>CABEZA</h4>
                                <textarea name="cabeza" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>OÍDO</h4>
                                <textarea name="oido" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>CAVIDAD ORAL</h4>
                                <textarea name="cavidad_oral" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>CUELLO</h4>
                                <textarea name="cuello" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>TÓRAX</h4>
                                <textarea name="torax" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="evaluation-column">
                            <div class="evaluation-item">
                                <h4>ABDOMEN</h4>
                                <textarea name="abdomen" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>COLUMNA VERTEBRAL</h4>
                                <textarea name="columna" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>EXTREMIDADES SUPERIORES</h4>
                                <textarea name="extremidades_superiores" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>EXTREMIDADES INFERIORES</h4>
                                <textarea name="extremidades_inferiores" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="evaluation-grid">
                        <div class="evaluation-column">
                            <div class="evaluation-item">
                                <h4>Botiquin</h4>
                                <textarea name="botiquin" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="evaluation-column">
                            <div class="evaluation-item">
                                <h4>Destino</h4>
                                <textarea name="destino" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
                    <a href="../../views/index.php" class="btn btn-danger btn-lg">
                        <i class="bi bi-box-arrow-left"></i> Salir
                    </a>
                    <div class="ms-auto d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-save"></i> Guardar Resultados
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS (opcional, para componentes interactivos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para calcular el IMC automáticamente
        function calcularIMC() {
            const talla = parseFloat(document.getElementById('talla').value) / 100; // Convertir cm a m
            const peso = parseFloat(document.getElementById('peso').value);
            const imcInfo = document.getElementById('imc_info');

            if (talla > 0 && peso > 0) {
                const imc = peso / (talla * talla);
                document.getElementById('imc_display').textContent = imc.toFixed(2);
                document.getElementById('imc').value = imc.toFixed(2);

                let categoria = '';
                if (imc < 18.5) {
                    categoria = 'Insuficiencia ponderal (<18.5)';
                } else if (imc < 25) {
                    categoria = 'Intervalo normal (18.5 - 24.9)';
                } else if (imc < 30) {
                    categoria = 'Preobesidad (25 - 29.9)';
                } else if (imc < 35) {
                    categoria = 'Obesidad clase 1 (30 - 34.9)';
                } else if (imc < 40) {
                    categoria = 'Obesidad clase 2 (35 - 39.9)';
                } else {
                    categoria = 'Obesidad clase 3 (≥40)';
                }
                imcInfo.textContent = categoria;
            } else {
                document.getElementById('imc_display').textContent = '--';
                document.getElementById('imc').value = '';
                imcInfo.textContent = '';
            }
        }

        // Mostrar la hora de salida en tiempo real
        function actualizarHoraSalida() {
            const now = new Date();
            const hora = now.toLocaleTimeString('es-MX', { hour12: false }).slice(0,5);
            document.getElementById('hora_salida').value = hora;
        }
        setInterval(actualizarHoraSalida, 1000);
        actualizarHoraSalida();

        document.getElementById('formConsulta').addEventListener('submit', function() {
            actualizarHoraSalida(); // Asegura que esté actualizada justo al enviar
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.form-section').forEach(function (section, i) {
                setTimeout(() => {
                    section.style.opacity = 1;
                    section.style.transform = 'translateY(0)';
                }, 150 + i * 120);
            });
        });
    </script>
</body>

</html>