<?php
include("setup/conexion.php");
$conexion = conectar();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idpropiedad'])) {
    $idpropiedad = (int)$_POST['idpropiedad'];
    $sql_principal = "SELECT COUNT(*) as total FROM galeria WHERE idpropiedades = $idpropiedad AND principal = 1";
    $res_principal = mysqli_query($conexion, $sql_principal);
    $row_principal = mysqli_fetch_assoc($res_principal);
    $hay_principal = ($row_principal['total'] > 0);
    
    if (isset($_FILES['imagenes'])) {
        $total_imagenes = count($_FILES['imagenes']['name']);
        $errores = 0;
        for ($i = 0; $i < $total_imagenes; $i++) {
            if ($_FILES['imagenes']['error'][$i] == 0) {
                $nombre_archivo = time() . '_' . basename($_FILES['imagenes']['name'][$i]);
                $ruta_destino = 'img/propiedades/' . $nombre_archivo;
                if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $ruta_destino)) {
                    $es_principal = (!$hay_principal && $i == 0) ? 1 : 0;
                    $query = "INSERT INTO galeria (foto, estado, principal, idpropiedades) VALUES ('$ruta_destino', 1, $es_principal, $idpropiedad)";
                    if (!mysqli_query($conexion, $query)) {
                        $errores++;
                    }
                } else {
                    $errores++;
                }
            } else {
                $errores++;
            }
        }
        if ($errores == 0) {
            echo json_encode(['status' => 'success', 'message' => 'Fotos subidas correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Algunas fotos no se pudieron subir']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se recibieron imágenes']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
}
?> 