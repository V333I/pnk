<?php
include("setup/conexion.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            throw new Exception("ID del propietario no proporcionado");
        }

        $id = $_POST['id'];
        
        $conexion = conectar();
        if (!$conexion) {
            throw new Exception("Error de conexión a la base de datos");
        }

        // Verificar si el propietario existe
        $check_sql = "SELECT id FROM propietarios WHERE id = ?";
        $check_stmt = mysqli_prepare($conexion, $check_sql);
        if (!$check_stmt) {
            throw new Exception("Error al preparar la consulta de verificación");
        }

        mysqli_stmt_bind_param($check_stmt, "i", $id);
        if (!mysqli_stmt_execute($check_stmt)) {
            throw new Exception("Error al verificar el propietario");
        }

        $result = mysqli_stmt_get_result($check_stmt);
        if (mysqli_num_rows($result) == 0) {
            throw new Exception("El propietario no existe");
        }

        // Eliminar propietario
        $sql = "DELETE FROM propietarios WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta de eliminación");
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al eliminar: " . mysqli_error($conexion));
        }

        if (mysqli_affected_rows($conexion) == 0) {
            throw new Exception("No se pudo eliminar el propietario");
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Propietario eliminado correctamente'
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) mysqli_stmt_close($stmt);
        if (isset($check_stmt)) mysqli_stmt_close($check_stmt);
        if (isset($conexion)) mysqli_close($conexion);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido'
    ]);
}
?> 