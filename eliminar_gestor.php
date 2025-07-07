<?php
include("setup/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $sql = "DELETE FROM gestores WHERE id = '$id'";
    
    if(mysqli_query(conectar(), $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar: ' . mysqli_error(conectar())]);
    }
}
?>
