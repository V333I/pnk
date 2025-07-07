<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_clean();
header('Content-Type: application/json');
session_start();
require_once 'setup/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

if (!isset($_POST['idsectores'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de sector no proporcionado']);
    exit;
}

$idsectores = (int)$_POST['idsectores'];
$con = conectar();
// Obtener la ubicación completa
$sql = "SELECT s.idsectores, s.nombre_sector, c.idcomunas, c.nombre_comuna, 
        p.idprovincias, p.nombre_provincia, r.idregion, r.nombre_region
        FROM sectores s
        JOIN comunas c ON s.idcomunas = c.idcomunas
        JOIN provincias p ON c.idprovincias = p.idprovincias
        JOIN regiones r ON p.idregion = r.idregion
        WHERE s.idsectores = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $idsectores);
$stmt->execute();
$result = $stmt->get_result();
$ubicacion = $result->fetch_assoc();

if ($ubicacion) {
    echo json_encode([
        'status' => 'success',
        'region' => $ubicacion['idregion'],
        'provincia' => $ubicacion['idprovincias'],
        'comuna' => $ubicacion['idcomunas'],
        'sector' => $ubicacion['idsectores']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ubicación no encontrada']);
}

$stmt->close();
$con->close();
exit; 