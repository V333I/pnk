<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("setup/conexion.php");
$conexion = conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que el usuario esté logueado y sea admin
    if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
        exit;
    }

    // Obtener y sanitizar los datos del formulario
    $rut_propietario = mysqli_real_escape_string($conexion, $_POST['rut_propietario']);
    $titulo = mysqli_real_escape_string($conexion, $_POST['titulo']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $cant_banos = (int)$_POST['cant_banos'];
    $cant_domitorios = (int)$_POST['cant_domitorios'];
    $area_total = (int)$_POST['area_total'];
    $area_construida = (int)$_POST['area_construida'];
    $precio_pesos = (int)$_POST['precio_pesos'];
    $precio_uf = (int)$_POST['precio_uf'];
    $tipo_propiedad = (int)$_POST['tipo_propiedad'];
    $sector = (int)$_POST['sector'];
    
    // Características booleanas
    $bodega = isset($_POST['bodega']) ? 1 : 0;
    $estacionamiento = isset($_POST['estacionamiento']) ? 1 : 0;
    $logia = isset($_POST['logia']) ? 1 : 0;
    $cocinaamoblada = isset($_POST['cocina_amoblada']) ? 1 : 0;
    $antejardin = isset($_POST['antejardin']) ? 1 : 0;
    $patiotrasero = isset($_POST['patio_trasero']) ? 1 : 0;
    $piscina = isset($_POST['piscina']) ? 1 : 0;

    // Insertar la propiedad
    $query = "INSERT INTO propiedades (
        rut_propietario, titulopropiedad, descripcion, cant_banos, cant_domitorios, 
        area_total, area_construida, precio_pesos, precio_uf, 
        fecha_publicacion, estado, idtipo_propiedad, 
        bodega, estacionamiento, logia, cocinaamoblada, 
        antejardin, patiotrasero, piscina, idsectores
    ) VALUES (
        '$rut_propietario', '$titulo', '$descripcion', $cant_banos, $cant_domitorios,
        $area_total, $area_construida, $precio_pesos, $precio_uf,
        CURDATE(), 1, $tipo_propiedad,
        $bodega, $estacionamiento, $logia, $cocinaamoblada,
        $antejardin, $patiotrasero, $piscina, $sector
    )";

    if (mysqli_query($conexion, $query)) {
        $id_propiedad = mysqli_insert_id($conexion);
        
        // Procesar imágenes si se subieron
        if (isset($_FILES['imagenes'])) {
            $total_imagenes = count($_FILES['imagenes']['name']);
            
            for ($i = 0; $i < $total_imagenes; $i++) {
                if ($_FILES['imagenes']['error'][$i] == 0) {
                    $nombre_archivo = time() . '_' . $_FILES['imagenes']['name'][$i];
                    $ruta_destino = 'img/propiedades/' . $nombre_archivo;
                    
                    if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $ruta_destino)) {
                        $es_principal = ($i == 0) ? 1 : 0; // La primera imagen es la principal
                        
                        $query_imagen = "INSERT INTO galeria (foto, estado, principal, idpropiedades) 
                                       VALUES ('$ruta_destino', 1, $es_principal, $id_propiedad)";
                        mysqli_query($conexion, $query_imagen);
                    }
                }
            }
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Propiedad registrada exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar la propiedad: ' . mysqli_error($conexion), 'query' => $query]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?> 