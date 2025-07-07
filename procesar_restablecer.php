<?php
session_start();
include 'setup/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];

    // Verificar token válido
    $sql = "SELECT * FROM tokens_recuperacion WHERE token = ? AND usado = 0 AND fecha_expiracion > NOW()";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Token inválido o expirado']);
        exit;
    }

    $token_data = $result->fetch_assoc();
    $id_usuario = $token_data['id_usuario'];
    $tipo_usuario = $token_data['tipo_usuario'];

    // Actualizar contraseña según el tipo de usuario
    switch ($tipo_usuario) {
        case 'usuario':
            $sql = "UPDATE usuarios SET clave = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $hashed_password = md5($password); // Usando MD5 como en el sistema actual
            $stmt->bind_param("si", $hashed_password, $id_usuario);
            break;

        case 'gestor':
            $sql = "UPDATE gestores SET password = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $hashed_password = md5($password); // Usando MD5 como en el sistema actual
            $stmt->bind_param("si", $hashed_password, $id_usuario);
            break;

        case 'propietario':
            $sql = "UPDATE propietarios SET password = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Usando password_hash para propietarios
            $stmt->bind_param("si", $hashed_password, $id_usuario);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Tipo de usuario inválido']);
            exit;
    }

    if ($stmt->execute()) {
        // Marcar token como usado
        $sql = "UPDATE tokens_recuperacion SET usado = 1 WHERE token = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Contraseña actualizada correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la contraseña']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?> 