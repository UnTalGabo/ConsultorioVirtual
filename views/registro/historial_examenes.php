<?php
require_once "../../php/conexion.php";

// Obtener el id del paciente
$id_empleado = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del paciente
$paciente = null;
if ($id_empleado > 0) {
    $stmt = $conn->prepare("SELECT nombre_completo FROM pacientes WHERE id_empleado = ?");
    $stmt->bind_param("i", $id_empleado);
    $stmt->execute();
    $result = $stmt->get_result();
    $paciente = $result->fetch_assoc();
    $stmt->close();
}

// Obtener historial de examenes
$examenes = [];
if ($id_empleado > 0) {
    $stmt = $conn->prepare("SELECT * FROM pdf WHERE id_empleado = ? AND tipo_pdf = 'examen' ORDER BY fecha_creacion DESC, id DESC");
    $stmt->bind_param("i", $id_empleado);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $examenes[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de examenes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .main-container {
            max-width: 900px;
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

        .volver-btn {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table thead th {
            background: #2e3c81 !important;
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-top: none;
        }

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #f4f6fa;
        }

        .table-bordered {
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
                <i class="bi bi-hospital-fill fs-3"></i>
                Consultorio Virtual
            </a>
            <div class="ms-auto d-flex gap-3">
                <a href="../index.php" class="nav-link text-white">Inicio</a>
                <a href="../ver_pacientes.php" class="nav-link text-white">Pacientes</a>
                <a href="../../php/logout.php" class="nav-link text-white">Cerrar sesión</a>
            </div>
        </div>
    </nav>
    <div class="main-container">
        <div class="card p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="../ver_pacientes.php" class="btn btn-outline-primary volver-btn">
                    <i class="bi bi-arrow-left"></i> Volver a Pacientes
                </a>
                <?php if ($paciente): ?>
                    <a href="../consulta/historial.php?id=<?php echo $id_empleado; ?>" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-plus-circle"></i> Ver consultas
                    </a>
                <?php endif; ?>
                <?php if ($paciente): ?>
                    <a href="paso1.php?id=<?php echo $id_empleado; ?>" class="btn btn-success btn-lg">
                        <i class="bi bi-plus-circle"></i> Nuevo examen
                    </a>
                <?php endif; ?>
            </div>
            <h2 class="text-center mb-4 fw-bold text-primary">
                <i class="bi bi-journal-medical me-2"></i>
                Historial de examenes
            </h2>
            <?php if ($paciente): ?>
                <h5 class="text-center mb-4">
                    Paciente: <span class="fw-bold"><?php echo htmlspecialchars($paciente['nombre_completo']); ?></span>
                </h5>


            <?php endif; ?>

            <?php if (count($examenes) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examenes as $examen): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($examen['fecha_creacion'])); ?></td>
                                    <td class="text-center">
                                        <a href="../../../<?php echo $examen['ruta_pdf'] ?>" class="btn btn-primary btn-sm" target="_blank">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        <a href="../../php/eliminar_pdf.php?ruta=<?php echo $examen['ruta_pdf'];?>&accion=examen"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Seguro que deseas eliminar esta examen?');">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    No hay examenes registradas para este paciente.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php $conn->close(); ?>