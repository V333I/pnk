<?php
include("setup/conexion.php");
$conexion = conectar();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idfoto']) && isset($_POST['idpropiedad'])) {
    $idfoto = (int)$_POST['idfoto'];
    $idpropiedad = (int)$_POST['idpropiedad'];
    // Poner todas las fotos como no principal
    $sql1 = "UPDATE galeria SET principal = 0 WHERE idpropiedades = $idpropiedad";
    // Poner la seleccionada como principal
    $sql2 = "UPDATE galeria SET principal = 1 WHERE id = $idfoto AND idpropiedades = $idpropiedad";
    if (mysqli_query($conexion, $sql1) && mysqli_query($conexion, $sql2)) {
        echo json_encode(['status' => 'success', 'message' => 'Foto marcada como principal']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la foto principal']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
}
?> 