<?php
include("conexion.php");

// Leer el contenido del archivo SQL
$sql = file_get_contents('gestores.sql');

// Ejecutar las consultas SQL
if (mysqli_multi_query(conectar(), $sql)) {
    echo "Tabla 'gestores' creada exitosamente\n";
} else {
    echo "Error al crear la tabla 'gestores': " . mysqli_error(conectar()) . "\n";
}
?> 