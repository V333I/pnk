<?php
session_start();
include 'setup/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y validar todos los campos
    $rut = htmlspecialchars($_POST['rut'] ?? '', ENT_QUOTES, 'UTF-8');
    $nombre_completo = htmlspecialchars($_POST['nombre_completo'] ?? '', ENT_QUOTES, 'UTF-8');
    $fecha_nacimiento = htmlspecialchars($_POST['fecha_nacimiento'] ?? '', ENT_QUOTES, 'UTF-8');
    $correo = filter_var($_POST['correo'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8');
    $sexo = htmlspecialchars($_POST['sexo'] ?? '', ENT_QUOTES, 'UTF-8');
    $telefono = htmlspecialchars($_POST['telefono'] ?? '', ENT_QUOTES, 'UTF-8');

    // Log de los datos recibidos
    error_log("Datos POST recibidos: " . print_r($_POST, true));
    error_log("Datos procesados: rut=$rut, nombre_completo=$nombre_completo, fecha_nacimiento=$fecha_nacimiento");

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

    // Verificar si el RUT ya existe
    $sql = "SELECT id FROM gestores WHERE rut = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El RUT ya está registrado']);
        exit;
    }

    // Verificar si el correo ya existe
    $sql = "SELECT id FROM gestores WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado']);
        exit;
    }

    // Procesar el certificado
    $certificado_path = '';
    if (isset($_FILES['certificado']) && $_FILES['certificado']['error'] == 0) {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $filename = $_FILES['certificado']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $upload_dir = 'certificados/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['certificado']['tmp_name'], $upload_path)) {
                $certificado_path = $upload_path;
            }
        }
    }

    // Hash de la contraseña usando bcrypt
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insertar en la base de datos
    $sql = "INSERT INTO gestores (rut, nombre_completo, fecha_nacimiento, correo, password, sexo, telefono, certificado_path, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
    
    try {
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssssss", $rut, $nombre_completo, $fecha_nacimiento, $correo, $password_hash, $sexo, $telefono, $certificado_path);
        
        if ($stmt->execute()) {
            // Iniciar sesión
            $_SESSION['usuario'] = $correo;
            $_SESSION['nombre'] = $nombre_completo;
            $_SESSION['tipo'] = 'gestor';
            
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