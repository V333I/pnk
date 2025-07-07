<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'setup/conexion.php';

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
$stmt->bind_param("i", $_POST['num_propiedad']);
$stmt->execute();
$result = $stmt->get_result();
$propiedad = $result->fetch_assoc();

if (!$propiedad) {
    echo json_encode(['status' => 'error', 'message' => 'La propiedad no existe']);
    exit;
}

if ($tipo_usuario === 'propietario' && $propiedad['rut_propietario'] !== $rut_usuario) {
    echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para modificar esta propiedad']);
    exit;
}

// Si llegamos aquí, el usuario es el propietario o admin y puede actualizar
$stmt = $conexion->prepare("UPDATE propiedades SET 
    titulopropiedad = ?,
    descripcion = ?,
    cant_banos = ?,
    cant_domitorios = ?,
    area_total = ?,
    area_construida = ?,
    precio_pesos = ?,
    precio_uf = ?,
    estado = ?,
    idtipo_propiedad = ?,
    idsectores = ?,
    bodega = ?,
    estacionamiento = ?,
    logia = ?,
    cocinaamoblada = ?,
    antejardin = ?,
    patiotrasero = ?,
    piscina = ?
    WHERE num_propiedad = ?");

$stmt->bind_param("ssiiiiiiiiiiiiiiiii", 
    $_POST['titulo'],
    $_POST['descripcion'],
    $_POST['cant_banos'],
    $_POST['cant_domitorios'],
    $_POST['area_total'],
    $_POST['area_construida'],
    $_POST['precio_pesos'],
    $_POST['precio_uf'],
    $_POST['estado'],
    $_POST['tipo_propiedad'],
    $_POST['sector'],
    $_POST['bodega'],
    $_POST['estacionamiento'],
    $_POST['logia'],
    $_POST['cocina_amoblada'],
    $_POST['antejardin'],
    $_POST['patio_trasero'],
    $_POST['piscina'],
    $_POST['num_propiedad']
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Propiedad actualizada exitosamente']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la propiedad: ' . $stmt->error]);
}

$stmt->close();
$conexion->close();
?> 