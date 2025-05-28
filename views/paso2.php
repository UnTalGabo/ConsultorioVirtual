<?php
require_once "../php/conexion.php";

$id_empleado = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Confirmación de Aviso de Conformidad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f4f6fa;
            color: #1e2a78;
            /* Animación de fade-in para el contenido principal */
            animation: fadeInBody 0.7s cubic-bezier(.39, .575, .565, 1.000);
        }

        @keyframes fadeInBody {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .aviso-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            background: white;
            border-radius: 5px;
            /* Animación de entrada para la caja de aviso */
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

        .button-next {
            background-color: #2e3c81;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.1s;
        }

        .button-next:disabled {
            background-color: #cccccc;
        }

        /* Animación al enfocar el checkbox */
        input[type="checkbox"]:focus {
            outline: 2px solid #2e3c81;
            box-shadow: 0 0 0 2px #2e3c8133;
            transition: box-shadow 0.3s, outline 0.3s;
        }

        /* Animación de botón al hacer clic */
        .button-next:active {
            transform: scale(0.97);
            transition: transform 0.1s;
        }
    </style>
</head>

<body>

    <h2>Aviso de Conformidad</h2>

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

        <label>
            <input type="checkbox" name="acepta_terminos" id="check_terminos" required>
            Acepto los términos y condiciones
        </label>

        <div style="margin-top: 20px;">
            <button type="submit" class="button-next" id="btn_continuar" disabled>Continuar a Antecedentes Heredo-Familiares &raquo;</button>
        </div>
    </form>

    <script>
        // Habilitar botón solo si el checkbox está marcado
        document.getElementById('check_terminos').addEventListener('change', function() {
            document.getElementById('btn_continuar').disabled = !this.checked;
        });
    </script>

</body>

</html>