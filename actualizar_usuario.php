<?php
include("setup/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $rut = $_POST['rut'];
    $nombres = $_POST['nombres'];
    $ap_paterno = $_POST['ap_paterno'];
    $ap_materno = $_POST['ap_materno'];
    $usuario = $_POST['usuario'];
    $estado = $_POST['estado'];

    // Verificar si el RUT ya existe para otro usuario
    $sql_check = "SELECT * FROM usuarios WHERE rut = '$rut' AND id != '$id'";
    $result_check = mysqli_query(conectar(), $sql_check);
    
    if(mysqli_num_rows($result_check) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El RUT ya está registrado para otro usuario']);
        exit;
    }

    // Actualizar usuario
    $sql = "UPDATE usuarios SET 
            rut = '$rut',
            nombres = '$nombres',
            ap_paterno = '$ap_paterno',
            ap_materno = '$ap_materno',
            usuario = '$usuario',
            estado = '$estado'
            WHERE id = '$id'";

    if(mysqli_query(conectar(), $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: ' . mysqli_error(conectar())]);
    }
}
?> 