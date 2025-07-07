<?php
include("setup/conexion.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validar que todos los campos necesarios estén presentes
        $campos_requeridos = ['id', 'rut', 'nombre_completo', 'fecha_nacimiento', 'correo', 'telefono', 'sexo'];
        foreach ($campos_requeridos as $campo) {
            if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
                throw new Exception("El campo $campo es requerido");
            }
        }

        $id = $_POST['id'];
        $rut = $_POST['rut'];
        $nombre_completo = $_POST['nombre_completo'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $sexo = $_POST['sexo'];

        $conexion = conectar();
        if (!$conexion) {
            throw new Exception("Error de conexión a la base de datos");
        }

        // Verificar si el RUT ya existe (excluyendo el registro actual)
        $check_sql = "SELECT * FROM propietarios WHERE rut = ? AND id != ?";
        $check_stmt = mysqli_prepare($conexion, $check_sql);
        if (!$check_stmt) {
            throw new Exception("Error al preparar la consulta de verificación");
        }

        mysqli_stmt_bind_param($check_stmt, "si", $rut, $id);
        if (!mysqli_stmt_execute($check_stmt)) {
            throw new Exception("Error al ejecutar la verificación de RUT");
        }

        $result = mysqli_stmt_get_result($check_stmt);
        if (mysqli_num_rows($result) > 0) {
            throw new Exception("El RUT ya está registrado para otro propietario");
        }

        // Actualizar propietario
        $sql = "UPDATE propietarios SET 
                rut = ?, 
                nombre_completo = ?, 
                fecha_nacimiento = ?,
                correo = ?,
                sexo = ?,
                telefono = ?
                WHERE id = ?";
        
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta de actualización");
        }

        mysqli_stmt_bind_param($stmt, "ssssssi", $rut, $nombre_completo, $fecha_nacimiento, $correo, $sexo, $telefono, $id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al actualizar: " . mysqli_error($conexion));
        }

        if (mysqli_affected_rows($conexion) == 0) {
            throw new Exception("No se encontró el propietario a actualizar");
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Propietario actualizado correctamente'
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