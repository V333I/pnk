<?php
include("setup/conexion.php");
$conexion = conectar();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idfoto'])) {
    $idfoto = (int)$_POST['idfoto'];
    $sql = "SELECT foto FROM galeria WHERE id = $idfoto";
    $res = mysqli_query($conexion, $sql);
    if ($row = mysqli_fetch_assoc($res)) {
        $foto = $row['foto'];
        if (file_exists($foto)) {
            unlink($foto);
        }
        $sql_del = "DELETE FROM galeria WHERE id = $idfoto";
        if (mysqli_query($conexion, $sql_del)) {
            echo json_encode(['status' => 'success', 'message' => 'Foto eliminada correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar de la base de datos']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Foto no encontrada']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
}
?> 