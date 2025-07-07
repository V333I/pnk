<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'penka';
$username = 'root';
$password = '';

// Función para conectar usando mysqli_connect (para compatibilidad con config.php)
function conectar() 
{
    global $host, $username, $password, $dbname;
    $con = mysqli_connect($host, $username, $password, $dbname);
    if (!$con) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    mysqli_set_charset($con, "utf8");
    return $con;
}

try {
    // Crear conexión usando mysqli (para compatibilidad con conexion.php)
    $conexion = new mysqli($host, $username, $password, $dbname);

    // Verificar conexión
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }

    // Establecer el conjunto de caracteres
    $conexion->set_charset("utf8");
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}
?> 