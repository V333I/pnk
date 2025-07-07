<?php
session_start();
include 'setup/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y validar todos los campos
    $rut = htmlspecialchars($_POST['rut']);
    $nombre_completo = htmlspecialchars($_POST['nombre_completo']);
    $fecha_nacimiento = htmlspecialchars($_POST['fecha_nacimiento']);
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password']);
    $sexo = substr(htmlspecialchars($_POST['genero']), 0, 1); // Tomar solo el primer carácter
    $telefono = htmlspecialchars($_POST['telefono']);

    // Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Formato de correo electrónico inválido']);
        exit;
    }

    // Validar RUT
    if (!validarRut($rut)) {
        echo json_encode(['status' => 'error', 'message' => 'RUT inválido']);
        exit;
    }

    // Validar sexo
    if (!in_array($sexo, ['M', 'F', 'O'])) {
        echo json_encode(['status' => 'error', 'message' => 'Valor de sexo inválido']);
        exit;
    }

    // Verificar si el RUT ya existe
    $sql = "SELECT id FROM propietarios WHERE rut = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El RUT ya está registrado']);
        exit;
    }

    // Verificar si el correo ya existe
    $sql = "SELECT id FROM propietarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado']);
        exit;
    }

    // Hash de la contraseña usando bcrypt
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insertar en la base de datos
    $sql = "INSERT INTO propietarios (rut, nombre_completo, fecha_nacimiento, correo, password, sexo, telefono) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssssss", $rut, $nombre_completo, $fecha_nacimiento, $correo, $password_hash, $sexo, $telefono);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Registro exitoso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar: ' . $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en el registro: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}

// Función para validar RUT chileno
function validarRut($rut) {
    $rut = preg_replace('/[^k0-9]/i', '', $rut);
    $dv = substr($rut, -1);
    $numero = substr($rut, 0, strlen($rut)-1);
    $i = 2;
    $suma = 0;
    foreach(array_reverse(str_split($numero)) as $v) {
        if($i == 8) $i = 2;
        $suma += $v * $i;
        $i++;
    }
    $dvr = 11 - ($suma % 11);
    if($dvr == 11) $dvr = 0;
    if($dvr == 10) $dvr = 'K';
    return $dvr == strtoupper($dv);
}
?> 