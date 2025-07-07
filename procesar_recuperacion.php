<?php
session_start();
include 'setup/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Correo electrónico inválido']);
        exit;
    }

    // Verificar en la tabla usuarios (campo 'usuario')
    $sql_usuarios = "SELECT id, usuario FROM usuarios WHERE usuario = ? AND estado = 1";
    $stmt_usuarios = $conexion->prepare($sql_usuarios);
    $stmt_usuarios->bind_param("s", $correo);
    $stmt_usuarios->execute();
    $result_usuarios = $stmt_usuarios->get_result();

    // Verificar en la tabla gestores (campo 'correo')
    $sql_gestores = "SELECT id, correo FROM gestores WHERE correo = ? AND estado = 1";
    $stmt_gestores = $conexion->prepare($sql_gestores);
    $stmt_gestores->bind_param("s", $correo);
    $stmt_gestores->execute();
    $result_gestores = $stmt_gestores->get_result();

    // Verificar en la tabla propietarios (campo 'correo')
    $sql_propietarios = "SELECT id, correo FROM propietarios WHERE correo = ?";
    $stmt_propietarios = $conexion->prepare($sql_propietarios);
    $stmt_propietarios->bind_param("s", $correo);
    $stmt_propietarios->execute();
    $result_propietarios = $stmt_propietarios->get_result();

    if ($result_usuarios->num_rows > 0 || $result_gestores->num_rows > 0 || $result_propietarios->num_rows > 0) {
        // Generar token único
        $token = bin2hex(random_bytes(32));
        $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Determinar en qué tabla está el usuario
        $tipo_usuario = '';
        $id_usuario = 0;
        
        if ($result_usuarios->num_rows > 0) {
            $tipo_usuario = 'usuario';
            $row = $result_usuarios->fetch_assoc();
            $id_usuario = $row['id'];
        } elseif ($result_gestores->num_rows > 0) {
            $tipo_usuario = 'gestor';
            $row = $result_gestores->fetch_assoc();
            $id_usuario = $row['id'];
        } else {
            $tipo_usuario = 'propietario';
            $row = $result_propietarios->fetch_assoc();
            $id_usuario = $row['id'];
        }

        // Guardar token en la base de datos
        $sql_token = "INSERT INTO tokens_recuperacion (token, id_usuario, tipo_usuario, fecha_expiracion) VALUES (?, ?, ?, ?)";
        $stmt_token = $conexion->prepare($sql_token);
        $stmt_token->bind_param("siss", $token, $id_usuario, $tipo_usuario, $fecha_expiracion);
        
        if ($stmt_token->execute()) {
            // Enviar correo electrónico con el enlace de recuperación
            $enlace = "http://" . $_SERVER['HTTP_HOST'] . "/FrontEnd3/restablecer.php?token=" . $token;
            $asunto = "Recuperación de contraseña - PNK Inmobiliaria";
            $mensaje = "Hola,\n\nHas solicitado recuperar tu contraseña. Por favor, haz clic en el siguiente enlace para restablecer tu contraseña:\n\n" . $enlace . "\n\nEl enlace expirará en 1 hora.\n\nSi no solicitaste este cambio, por favor ignora este correo.\n\nSaludos,\nPNK Inmobiliaria";
            $headers = "From: noreply@pnk.cl";

            if (mail($correo, $asunto, $mensaje, $headers)) {
                echo json_encode(['status' => 'success', 'message' => 'Se ha enviado un correo con instrucciones para recuperar tu contraseña']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al enviar el correo electrónico']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al procesar la solicitud']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'El correo electrónico no está registrado en el sistema']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?> 