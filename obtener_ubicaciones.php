<?php
ob_clean();
header('Content-Type: application/json');
include("setup/conexion.php");
$conexion = conectar();

$tipo = $_GET['tipo'] ?? '';
$id = $_GET['id'] ?? '';

$response = ['success' => false, 'data' => [], 'error' => null];

try {
    if (!$conexion) {
        throw new Exception("Error de conexión a la base de datos");
    }

    switch($tipo) {
        case 'regiones':
            $sql = "SELECT idregion, nombre_region FROM regiones WHERE estado = 1 ORDER BY nombre_region";
            $result = mysqli_query($conexion, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $response['data'][] = [
                    'idregion' => $row['idregion'],
                    'nombre_region' => $row['nombre_region']
                ];
            }
            break;

        case 'provincias':
            if (empty($id)) {
                throw new Exception("ID de región no proporcionado");
            }
            $sql = "SELECT idprovincias, nombre_provincia FROM provincias WHERE estado = 1 AND idregion = ? ORDER BY nombre_provincia";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $response['data'][] = [
                    'idprovincias' => $row['idprovincias'],
                    'nombre_provincia' => $row['nombre_provincia']
                ];
            }
            break;

        case 'comunas':
            if (empty($id)) {
                throw new Exception("ID de provincia no proporcionado");
            }
            $sql = "SELECT idcomunas, nombre_comuna FROM comunas WHERE estado = 1 AND idprovincias = ? ORDER BY nombre_comuna";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $response['data'][] = [
                    'idcomunas' => $row['idcomunas'],
                    'nombre_comuna' => $row['nombre_comuna']
                ];
            }
            break;

        case 'sectores':
            if (empty($id)) {
                throw new Exception("ID de comuna no proporcionado");
            }
            $sql = "SELECT idsectores, nombre_sector FROM sectores WHERE estado = 1 AND idcomunas = ? ORDER BY nombre_sector";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $response['data'][] = [
                    'idsectores' => $row['idsectores'],
                    'nombre_sector' => $row['nombre_sector']
                ];
            }
            break;

        default:
            throw new Exception('Tipo de consulta no válido');
    }
    
    $response['success'] = true;
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log("Error en obtener_ubicaciones.php: " . $e->getMessage());
}

echo json_encode($response);
exit;
// No cerrar con ?> para evitar espacios en blanco 