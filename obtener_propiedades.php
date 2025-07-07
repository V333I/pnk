<?php
include("setup/conexion.php");

$tipo = isset($_POST['tipo']) ? intval($_POST['tipo']) : 0;
$region = isset($_POST['region']) ? intval($_POST['region']) : 0;
$provincia = isset($_POST['provincia']) ? intval($_POST['provincia']) : 0;
$comuna = isset($_POST['comuna']) ? intval($_POST['comuna']) : 0;
$sector = isset($_POST['sector']) ? intval($_POST['sector']) : 0;

$con = conectar();

// Construir la consulta dinámica
$sql = "SELECT p.num_propiedad, p.estado, p.titulopropiedad AS titulo, p.precio_pesos, p.precio_uf, g.foto
        FROM propiedades p
        JOIN galeria g ON p.num_propiedad = g.idpropiedades
        JOIN sectores s ON p.idsectores = s.idsectores
        JOIN comunas c ON s.idcomunas = c.idcomunas
        JOIN provincias pr ON c.idprovincias = pr.idprovincias
        JOIN regiones r ON pr.idregion = r.idregion
        WHERE g.principal = 1 AND p.estado = 1";

if ($tipo) {
    $sql .= " AND p.idtipo_propiedad = $tipo";
}
if ($region) {
    $sql .= " AND r.idregion = $region";
}
if ($provincia) {
    $sql .= " AND pr.idprovincias = $provincia";
}
if ($comuna) {
    $sql .= " AND c.idcomunas = $comuna";
}
if ($sector) {
    $sql .= " AND s.idsectores = $sector";
}

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    while($datos = mysqli_fetch_array($result)) {
        $fmt = numfmt_create('es_CL', NumberFormatter::CURRENCY);
        $precio = numfmt_format_currency($fmt, $datos['precio_pesos'], "CLP");
        echo '<div class="propiedad">';
        echo '<img src="' . $datos['foto'] . '" alt="' . htmlspecialchars($datos['titulo']) . '">';
        echo '<div class="info-propiedad">';
        echo '<h3>' . htmlspecialchars($datos['titulo']) . '</h3>';
        echo '<div class="precios">';
        echo '<span class="uf">UF ' . number_format($datos['precio_uf'], 0, ',', '.') . '</span>';
        echo '<span class="clp">' . $precio . '</span>';
        echo '<a href="vermas.php?idpro=' . $datos['num_propiedad'] . '" class="btn-ver-mas">Ver más</a>';
        echo '</div></div></div>';
    }
} else {
    echo '<div class="alert alert-warning">No se encontraron propiedades con los filtros seleccionados.</div>';
} 