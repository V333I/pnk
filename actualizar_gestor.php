<?php
include("setup/conexion.php");

header('Content-Type: application/json');

try {
    // Validar que todos los campos necesarios estén presentes
    $campos_requeridos = ['id', 'rut', 'nombre_completo', 'fecha_nacimiento', 'correo', 'sexo', 'telefono'];
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            throw new Exception("El campo $campo es obligatorio");
        }
    }

    // Validar estado específicamente
    if (!isset($_POST['estado']) || $_POST['estado'] === '') {
        throw new Exception("El campo estado es obligatorio");
    }

    // Sanitizar datos
    $id = mysqli_real_escape_string(conectar(), $_POST['id']);
    $rut = mysqli_real_escape_string(conectar(), $_POST['rut']);
    $nombre = mysqli_real_escape_string(conectar(), $_POST['nombre_completo']);
    $fecha = mysqli_real_escape_string(conectar(), $_POST['fecha_nacimiento']);
    $correo = mysqli_real_escape_string(conectar(), $_POST['correo']);
    $sexo = mysqli_real_escape_string(conectar(), $_POST['sexo']);
    $telefono = mysqli_real_escape_string(conectar(), $_POST['telefono']);
    $estado = (int)$_POST['estado']; // Convertir a entero para asegurar que sea 0 o 1

    // Verificar si el RUT ya existe para otro gestor
    $sql_check = "SELECT id FROM gestores WHERE rut = '$rut' AND id != $id";
    $result_check = mysqli_query(conectar(), $sql_check);
    if (mysqli_num_rows($result_check) > 0) {
        throw new Exception("El RUT ingresado ya está registrado para otro gestor");
    }

    // Verificar si el correo ya existe para otro gestor
    $sql_check = "SELECT id FROM gestores WHERE correo = '$correo' AND id != $id";
    $result_check = mysqli_query(conectar(), $sql_check);
    if (mysqli_num_rows($result_check) > 0) {
        throw new Exception("El correo electrónico ya está registrado para otro gestor");
    }

    // Actualizar en la base de datos
    $sql = "UPDATE gestores SET 
            rut = '$rut',
            nombre_completo = '$nombre',
            fecha_nacimiento = '$fecha',
            correo = '$correo',
            sexo = '$sexo',
            telefono = '$telefono',
            estado = $estado
            WHERE id = $id";

    if (mysqli_query(conectar(), $sql)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Gestor actualizado exitosamente'
        ]);
    } else {
        throw new Exception("Error al actualizar el gestor: " . mysqli_error(conectar()));
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 