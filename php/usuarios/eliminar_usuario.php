<?php
session_start();
if (
    !isset($_SESSION['usuario_rol']) ||
    !in_array($_SESSION['usuario_rol'], ['doctor', 'admin'])
) {
    header('Location: login.php');
    exit();
}
require_once "../conexion.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
        header("Location: ../../views/usuarios/lista_usuarios.php?error=No puedes eliminar tu propio usuario");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../../views/usuarios/lista_usuarios.php");
exit;
?>