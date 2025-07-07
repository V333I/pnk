<?php
session_start();
include('setup/conexion.php');
header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'propietario') {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

// Obtener el rut del propietario autenticado
$correo = $_SESSION['usuario'];
$rut = '';
$sql = "SELECT rut FROM propietarios WHERE correo = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->bind_result($rut);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y obtener datos
    $titulo = mysqli_real_escape_string($conexion, $_POST['titulo']);
    $tipo_propiedad = (int)$_POST['tipo_propiedad'];
    $region = mysqli_real_escape_string($conexion, $_POST['region']);
    $provincia = mysqli_real_escape_string($conexion, $_POST['provincia']);
    $comuna = mysqli_real_escape_string($conexion, $_POST['comuna']);
    $sector = (int)$_POST['sector'];
    $precio_pesos = (int)$_POST['precio_pesos'];
    $precio_uf = (int)$_POST['precio_uf'];
    $area_total = (int)$_POST['area_total'];
    $area_construida = (int)$_POST['area_construida'];
    $cant_domitorios = (int)$_POST['cant_domitorios'];
    $cant_banos = (int)$_POST['cant_banos'];
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $bodega = isset($_POST['bodega']) ? 1 : 0;
    $estacionamiento = isset($_POST['estacionamiento']) ? 1 : 0;
    $logia = isset($_POST['logia']) ? 1 : 0;
    $cocinaamoblada = isset($_POST['cocina_amoblada']) ? 1 : 0;
    $antejardin = isset($_POST['antejardin']) ? 1 : 0;
    $patiotrasero = isset($_POST['patio_trasero']) ? 1 : 0;
    $piscina = isset($_POST['piscina']) ? 1 : 0;

    $sql = "INSERT INTO propiedades (
        rut_propietario, titulopropiedad, idtipo_propiedad, idsectores, descripcion, cant_banos, cant_domitorios, area_total, area_construida, precio_pesos, precio_uf, fecha_publicacion, estado, bodega, estacionamiento, logia, cocinaamoblada, antejardin, patiotrasero, piscina
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 1, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        'ssiisiiiiiiiiiiiii',
        $rut,
        $titulo,
        $tipo_propiedad,
        $sector,
        $descripcion,
        $cant_banos,
        $cant_domitorios,
        $area_total,
        $area_construida,
        $precio_pesos,
        $precio_uf,
        $bodega,
        $estacionamiento,
        $logia,
        $cocinaamoblada,
        $antejardin,
        $patiotrasero,
        $piscina
    );
    if ($stmt->execute()) {
        $id_propiedad = $stmt->insert_id;
        // Procesar imágenes si se subieron
        if (isset($_FILES['imagenes'])) {
            $total_imagenes = count($_FILES['imagenes']['name']);
            for ($i = 0; $i < $total_imagenes; $i++) {
                if ($_FILES['imagenes']['error'][$i] == 0) {
                    $nombre_archivo = time() . '_' . $_FILES['imagenes']['name'][$i];
                    $ruta_destino = 'img/propiedades/' . $nombre_archivo;
                    if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $ruta_destino)) {
                        $es_principal = ($i == 0) ? 1 : 0;
                        $query_imagen = "INSERT INTO galeria (foto, estado, principal, idpropiedades) VALUES ('$ruta_destino', 1, $es_principal, $id_propiedad)";
                        mysqli_query($conexion, $query_imagen);
                    }
                }
            }
        }
        echo json_encode(['status' => 'success', 'message' => 'Propiedad registrada exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar la propiedad: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
} 