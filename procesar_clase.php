<?php
include("setup/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut = $_POST['run'];
    $clase = $_POST['clase'];
    $fecha_avance = $_POST['fecha_avance'];

    $conn = conectar();
    
    // Verificar si el RUT existe
    $sql_check = "SELECT * FROM propietarios WHERE rut='".$rut."'";
    $result_check = mysqli_query($conn, $sql_check);
    $contador = mysqli_num_rows($result_check);
    
    if ($contador == 0) {
        echo json_encode(['status' => 'error', 'message' => 'El RUT no está registrado']);
        exit;
    }

    // Actualizar la clase del propietario
    $sql = "UPDATE propietarios SET clase = ?, fecha_avance = ? WHERE rut = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $clase, $fecha_avance, $rut);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Clase actualizada exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la clase: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
