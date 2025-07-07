<?php
session_start();
require_once 'setup/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y validar el correo electrónico
    $correo = filter_var($_POST['usuario'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: error.html");
        exit;
    }

    // Sanitizar la contraseña (usando htmlspecialchars en lugar de FILTER_SANITIZE_STRING que está deprecado)
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    
    // Verificar en la tabla usuarios (incluyendo administradores)
    $sql = "SELECT id, usuario, clave, tipo_usuario, estado FROM usuarios WHERE usuario = ? AND (estado = 1 OR estado = 'admin')";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['clave'])) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['tipo_usuario'] = $row['tipo_usuario'];
            $_SESSION['tipo'] = $row['tipo_usuario'];
            $_SESSION['estado'] = $row['estado']; // Guardamos el estado en la sesión
            if ($row['tipo_usuario'] === 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }

    // Verificar en la tabla gestores
    $sql = "SELECT id, correo, password, estado FROM gestores WHERE correo = ? AND estado = 1";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['usuario'] = $row['correo'];
            $_SESSION['tipo_usuario'] = 'gestor';
            $_SESSION['tipo'] = 'gestor';
            $_SESSION['estado'] = $row['estado'];
            header("Location: index.php");
            exit;
        }
    }

    // Verificar en la tabla propietarios
    $sql = "SELECT id, correo, password FROM propietarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['usuario'] = $row['correo'];
            $_SESSION['tipo_usuario'] = 'propietario';
            $_SESSION['tipo'] = 'propietario';
            $_SESSION['estado'] = 'activo';
            header("Location: index.php");
            exit;
        }
    }

    // Si no se encuentra el usuario o la contraseña es incorrecta
    header("Location: error.html");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>