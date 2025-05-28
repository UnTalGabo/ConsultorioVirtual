<?php
require_once "../php/conexion.php";

$id_empleado = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Confirmación de Aviso de Conformidad</title>
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
            max-width: 600px;
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

        .aviso-box {
            border: 1px solid #e9ecef;
            padding: 18px 20px;
            margin-bottom: 22px;
            background: #f8fafc;
            border-radius: 10px;
            animation: fadeInAviso 1s cubic-bezier(.39, .575, .565, 1.000);
        }

        @keyframes fadeInAviso {
            from {
                opacity: 0;
                transform: scale(0.97);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .form-check-label {
            color: #1e2a78;
            font-weight: 500;
            margin-left: 8px;
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

        .btn-primary:disabled {
            background-color: #cccccc;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #1e2a78;
            transform: scale(0.98);
        }

        @media (max-width: 767px) {
            .main-container {
                margin-top: 20px;
            }

            .card {
                padding: 0.5rem;
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
                <i class="bi bi-shield-check me-2"></i>
                Aviso de Conformidad
            </h2>

            <div class="aviso-box">
                <p><strong>CONFORMIDAD:</strong></p>
                <p>Por este medio confirmo que el responsable del servicio médico en turno me ha explicado con detalle los tipos de evaluaciones médicas,
                    las preguntas informativas para mi expediente médico, las preguntas evaluativas,
                    los motivos de la evaluación médica y las consideraciones que se aplicarán durante la misma;
                    todo ello ha quedado aclarado y comprendido.
                    En esta evaluación médica se identifican las preguntas informativas y las preguntas evaluativas. </p>
                <p>Toda la información proporcionada estará protegida bajo el aviso de privacidad de la organización. </p>
            </div>

            <form action="../php/guardar_paso2.php" method="post">
                <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="acepta_terminos" id="check_terminos" required>
                    <label class="form-check-label" for="check_terminos">
                        Acepto los términos y condiciones
                    </label>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary btn-lg" id="btn_continuar" disabled>
                        <i class="bi bi-arrow-right-circle"></i>
                        Continuar a Antecedentes Heredo-Familiares
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Habilitar botón solo si el checkbox está marcado
        document.getElementById('check_terminos').addEventListener('change', function() {
            document.getElementById('btn_continuar').disabled = !this.checked;
        });
    </script>
</body>

</html>