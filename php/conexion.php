<?php
$host = "localhost";        // Servidor (usualmente localhost en XAMPP)
$usuario = "root";          // Usuario por defecto en XAMPP
$contrasena = "";           // Contraseña (vacía por defecto)
$basedatos = "consultoriovirtual"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $basedatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


// echo "Conexión exitosa a la base de datos.";
?>
