<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('setup/conexion.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$tipo_usuario = $_SESSION['tipo'];
$correo = $_SESSION['usuario'];
$rut_usuario = '';

if ($tipo_usuario === 'propietario') {
    // Obtener el RUT del propietario
    $sql = "SELECT rut FROM propietarios WHERE correo = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $correo);
    $stmt->execute();
    $stmt->bind_result($rut_usuario);
    $stmt->fetch();
    $stmt->close();
}

// Verificar que la propiedad existe y obtener su rut_propietario
$stmt = $conexion->prepare("SELECT rut_propietario FROM propiedades WHERE num_propiedad = ?");
$stmt->bind_param("i", $_POST['id']);
$stmt->execute();
$result = $stmt->get_result();
$propiedad = $result->fetch_assoc();

if (!$propiedad) {
    echo json_encode(['status' => 'error', 'message' => 'La propiedad no existe']);
    exit;
}

// Solo restringir si es propietario y no es dueño. Si es admin, permitir siempre.
if ($tipo_usuario === 'propietario' && $propiedad['rut_propietario'] !== $rut_usuario) {
    echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para eliminar esta propiedad']);
    exit;
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    // Obtener las imágenes de la propiedad
    $stmt = $conexion->prepare("SELECT foto FROM galeria WHERE idpropiedades = ?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Eliminar físicamente las imágenes
    while ($imagen = $result->fetch_assoc()) {
        if (file_exists($imagen['foto'])) {
            unlink($imagen['foto']);
        }
    }
    
    // Eliminar registros de la galería
    $stmt = $conexion->prepare("DELETE FROM galeria WHERE idpropiedades = ?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    
    // Eliminar la propiedad
    $stmt = $conexion->prepare("DELETE FROM propiedades WHERE num_propiedad = ?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    
    // Confirmar transacción
    $conexion->commit();
    echo json_encode(['status' => 'success', 'message' => 'Propiedad eliminada exitosamente']);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar la propiedad: ' . $e->getMessage()]);
}

$stmt->close();
$conexion->close();
?> 