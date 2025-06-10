<?php
require_once "../../php/conexion.php";

date_default_timezone_set('America/Mexico_City');

// Validar ID de la consulta
$id_consulta = isset($_GET['id_consulta']) ? intval($_GET['id_consulta']) : 0;
$consulta = null;
$paciente = null;

if ($id_consulta > 0) {
    // Obtener datos de la consulta y del paciente
    $sql = "SELECT c.*, p.nombre_completo 
            FROM consultas c 
            INNER JOIN pacientes p ON c.id_empleado = p.id_empleado 
            WHERE c.id_consulta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_consulta);
    $stmt->execute();
    $result = $stmt->get_result();
    $consulta = $result->fetch_assoc();
    $stmt->close();

    if ($consulta) {
        $paciente = ['nombre_completo' => $consulta['nombre_completo']];
    }
}

$hora = $consulta ? $consulta['hora_entrada'] : date('H:i');
$fecha = $consulta ? $consulta['fecha'] : date('Y-m-d');
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
        /* ... estilos igual que antes ... */
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
            from { opacity: 0; transform: translateY(40px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
            animation: sectionFadeIn 0.7s forwards;
            opacity: 0;
            transform: translateY(30px);
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
        .form-control[readonly], .form-control[disabled], textarea[readonly], textarea[disabled] {
            background-color: #e9ecef !important;
            color: #495057 !important;
            opacity: 1;
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
        @media (max-width: 991px) {
            .main-container { padding: 0 5px; }
            .evaluation-grid { grid-template-columns: 1fr; }
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
            <?php if ($paciente && $paciente['nombre_completo']): ?>
                <div class="alert alert-info text-center mb-4">
                    <strong>Paciente:</strong> <?php echo htmlspecialchars($paciente['nombre_completo']); ?>
                </div>
            <?php endif; ?>

            <?php if ($consulta): ?>
            <form autocomplete="off">
                <input type="hidden" name="id_empleado" value="<?php echo $consulta['id_empleado']; ?>">
                <!-- Hora de entrada y salida -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Fecha:</label>
                        <input type="date" class="form-control" value="<?php echo htmlspecialchars($consulta['fecha']); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hora de entrada:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($consulta['hora_entrada']); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hora de salida:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($consulta['hora_salida']); ?>" readonly>
                    </div>
                </div>
                <!-- Fin hora de entrada y salida -->

                <div class="form-section">
                    <h3><i class="bi bi-heart-pulse"></i> SOMATOMETRÍA / SIGNOS VITALES</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Talla (cm):</label>
                            <input type="number" step="0.01" class="form-control" value="<?php echo htmlspecialchars($consulta['talla']); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Peso (kg):</label>
                            <input type="number" step="0.1" class="form-control" value="<?php echo htmlspecialchars($consulta['peso']); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IMC:</label>
                            <div class="imc-display"><?php echo htmlspecialchars($consulta['imc']); ?></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">FC (x'):</label>
                            <input type="number" class="form-control" value="<?php echo htmlspecialchars($consulta['fc']); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">FR (x'):</label>
                            <input type="number" class="form-control" value="<?php echo htmlspecialchars($consulta['fr']); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Temp (°C):</label>
                            <input type="number" step="0.1" class="form-control" value="<?php echo htmlspecialchars($consulta['temp']); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Perímetro Abd. (cm):</label>
                            <input type="number" class="form-control" value="<?php echo htmlspecialchars($consulta['perimetro_abdominal']); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Presión arterial (mm/Hg):</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($consulta['presion_arterial']); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SpO2 (%):</label>
                            <input type="number" class="form-control" value="<?php echo htmlspecialchars($consulta['spo2']); ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="evaluation-grid">
                        <div class="evaluation-column" style="grid-column: 1 / -1;">
                            <div class="evaluation-item">
                                <h4>Motivo</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['motivo']); ?></textarea>
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
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['cabeza']); ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>OÍDO</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['oido']); ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>CAVIDAD ORAL</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['cavidad_oral']); ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>CUELLO</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['cuello']); ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>TÓRAX</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['torax']); ?></textarea>
                            </div>
                        </div>
                        <div class="evaluation-column">
                            <div class="evaluation-item">
                                <h4>ABDOMEN</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['abdomen']); ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>COLUMNA VERTEBRAL</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['columna']); ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>EXTREMIDADES SUPERIORES</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['extremidades_superiores']); ?></textarea>
                            </div>
                            <div class="evaluation-item">
                                <h4>EXTREMIDADES INFERIORES</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['extremidades_inferiores']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="evaluation-grid">
                        <div class="evaluation-column">
                            <div class="evaluation-item">
                                <h4>Botiquin</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['botiquin']); ?></textarea>
                            </div>
                        </div>
                        <div class="evaluation-column">
                            <div class="evaluation-item">
                                <h4>Destino</h4>
                                <textarea rows="3" class="form-control" readonly><?php echo htmlspecialchars($consulta['destino']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-4">
                    <a href="javascript:history.back()" class="btn btn-danger btn-lg">
                        <i class="bi bi-box-arrow-left"></i> Volver
                    </a>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    Consulta no encontrada.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>