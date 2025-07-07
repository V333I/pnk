<?php
// Archivo de test para verificar la conexión a la base de datos
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'setup/conexion.php';

if (isset($conexion) && $conexion instanceof mysqli && !$conexion->connect_errno) {
    echo '<h2 style="color:green;">Conexión exitosa a la base de datos.</h2>';
} else {
    echo '<h2 style="color:red;">Error de conexión a la base de datos.</h2>';
    if (isset($conexion) && $conexion instanceof mysqli) {
        echo '<p>Error: ' . $conexion->connect_error . '</p>';
    }
}
?> 