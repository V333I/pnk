<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'setup/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

if (!isset($_POST['num_propiedad'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de propiedad no proporcionado']);
    exit;
}

$num_propiedad = (int)$_POST['num_propiedad'];

// Obtener la propiedad con todos sus campos
$sql = "SELECT p.*, t.tipo, s.nombre_sector 
        FROM propiedades p
        LEFT JOIN tipo_propiedad t ON p.idtipo_propiedad = t.idtipo_propiedad
        LEFT JOIN sectores s ON p.idsectores = s.idsectores
        WHERE p.num_propiedad = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $num_propiedad);
$stmt->execute();
$result = $stmt->get_result();
$propiedad = $result->fetch_assoc();

if ($propiedad) {
    echo json_encode([
        'status' => 'success',
        'propiedad' => $propiedad
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Propiedad no encontrada']);
}

$stmt->close();
$conexion->close();
?> 