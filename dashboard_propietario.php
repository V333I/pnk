<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'propietario') {
    header('Location: index.php');
    exit();
}
include 'setup/conexion.php';

// Obtener el rut del propietario
$correo = $_SESSION['usuario'];
$rut = '';
$sql = "SELECT rut FROM propietarios WHERE correo = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->bind_result($rut);
$stmt->fetch();
$stmt->close();

// Registro de propiedad
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'], $_POST['descripcion'])) {
    $titulo = htmlspecialchars($_POST['titulo']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $sql = "INSERT INTO propiedades (rut_propietario, titulopropiedad, descripcion, estado, idtipo_propiedad, idsectores, precio_pesos, precio_uf, fecha_publicacion) VALUES (?, ?, ?, 1, 1, 1, 0, 0, NOW())";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('sss', $rut, $titulo, $descripcion);
    if ($stmt->execute()) {
        $mensaje = 'Propiedad registrada correctamente.';
    } else {
        $mensaje = 'Error al registrar la propiedad.';
    }
    $stmt->close();
}

// Listar propiedades del propietario (por rut)
$sql_propiedades = "SELECT p.*, t.tipo, s.nombre_sector 
    FROM propiedades p 
    LEFT JOIN tipo_propiedad t ON p.idtipo_propiedad = t.idtipo_propiedad 
    LEFT JOIN sectores s ON p.idsectores = s.idsectores 
    WHERE p.rut_propietario = ? 
    ORDER BY p.num_propiedad DESC";
$stmt = $conexion->prepare($sql_propiedades);
$stmt->bind_param('s', $rut);
$stmt->execute();
$result_propiedades = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Propietario</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header" style="background: #7c3aed; color: #fff;">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Panel de Propietario</h2>
                    <a href="index.php" class="btn btn-light" style="color: #7c3aed; font-weight: bold;">Volver al inicio</a>
                </div>
            </div>
            <div class="card-body">
                <div class="button-container mb-3">
                    <!-- Botón de expansión eliminado -->
                </div>
                <div id="propiedadForm" class="form-container">
                    <form id="registroPropiedadForm" class="registro-forma" enctype="multipart/form-data" method="post" action="registrar_propiedad_propietario.php">
                        <h4 class="mb-3" style="color: #7c3aed;">Registro de Propiedad</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="titulo" class="form-label">Título de la Propiedad</label>
                                <input type="text" id="titulo" name="titulo" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tipo_propiedad" class="form-label">Tipo de Propiedad</label>
                                <select id="tipo_propiedad" name="tipo_propiedad" class="form-select" required>
                                    <option value="">Seleccione tipo</option>
                                    <option value="1">Casa</option>
                                    <option value="2">Departamento</option>
                                    <option value="3">Terreno</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="region" class="form-label">Región</label>
                                <select id="region" name="region" class="form-select" required onchange="cargarProvincias()">
                                    <option value="">Seleccione región</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="provincia" class="form-label">Provincia</label>
                                <select id="provincia" name="provincia" class="form-select" required onchange="cargarComunas()">
                                    <option value="">Seleccione provincia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="comuna" class="form-label">Comuna</label>
                                <select id="comuna" name="comuna" class="form-select" required onchange="cargarSectores()">
                                    <option value="">Seleccione comuna</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sector" class="form-label">Sector</label>
                                <select id="sector" name="sector" class="form-select" required>
                                    <option value="">Seleccione sector</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="precio_pesos" class="form-label">Precio (CLP)</label>
                                <input type="number" id="precio_pesos" name="precio_pesos" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="precio_uf" class="form-label">Precio (UF)</label>
                                <input type="number" id="precio_uf" name="precio_uf" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="area_total" class="form-label">Área Total (m²)</label>
                                <input type="number" id="area_total" name="area_total" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="area_construida" class="form-label">Área Construida (m²)</label>
                                <input type="number" id="area_construida" name="area_construida" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cant_domitorios" class="form-label">Dormitorios</label>
                                <input type="number" id="cant_domitorios" name="cant_domitorios" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cant_banos" class="form-label">Baños</label>
                                <input type="number" id="cant_banos" name="cant_banos" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Características</label>
                                <div class="caracteristicas-grid">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="bodega" name="bodega">
                                        <label class="form-check-label" for="bodega">Bodega</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="estacionamiento" name="estacionamiento">
                                        <label class="form-check-label" for="estacionamiento">Estacionamiento</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="logia" name="logia">
                                        <label class="form-check-label" for="logia">Logia</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="cocina_amoblada" name="cocina_amoblada">
                                        <label class="form-check-label" for="cocina_amoblada">Cocina Amoblada</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="antejardin" name="antejardin">
                                        <label class="form-check-label" for="antejardin">Antejardín</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="patio_trasero" name="patio_trasero">
                                        <label class="form-check-label" for="patio_trasero">Patio Trasero</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="piscina" name="piscina">
                                        <label class="form-check-label" for="piscina">Piscina</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="imagenes" class="form-label">Imágenes de la Propiedad</label>
                                <input type="file" id="imagenes" name="imagenes[]" class="form-control" multiple accept="image/*" required>
                                <small class="form-text text-muted">Puede seleccionar múltiples imágenes. La primera será la imagen principal.</small>
                            </div>
                        </div>
                        <button type="submit" class="btn mt-3" style="background: #7c3aed; color: #fff;">Registrar Propiedad</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">Mis Propiedades</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Sector</th>
                                <th>Precio (CLP)</th>
                                <th>Precio (UF)</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($prop = $result_propiedades->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $prop['num_propiedad']; ?></td>
                                <td><?php echo htmlspecialchars($prop['titulopropiedad']); ?></td>
                                <td><?php echo htmlspecialchars($prop['tipo']); ?></td>
                                <td><?php echo htmlspecialchars($prop['nombre_sector']); ?></td>
                                <td><?php echo number_format($prop['precio_pesos'], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($prop['precio_uf'], 0, ',', '.'); ?></td>
                                <td><?php echo ($prop['estado'] == 1) ? '<img src=\'img/check.png?v=1\' width=\'16px\'>' : '<img src=\'img/ina.png?v=1\' width=\'16px\'>'; ?></td>
                                <td>
                                    <img src="img/galeria.png?v=1" width="18px" style="cursor:pointer;" title="Ver/Editar Fotos" onclick="toggleFotos(<?php echo $prop['num_propiedad']; ?>)">
                                    |
                                    <img src="img/update.png?v=1" width="16px" style="cursor:pointer;" title="Editar" onclick="editarPropiedad(<?php echo $prop['num_propiedad']; ?>)">
                                    |
                                    <img src="img/borrar.png?v=1" width="16px" style="cursor:pointer;" title="Eliminar" onclick="eliminarPropiedad(<?php echo $prop['num_propiedad']; ?>)">
                                </td>
                            </tr>
                            <tr id="fotos_<?php echo $prop['num_propiedad']; ?>" class="fotos-row" style="display:none; background:#f9f9f9;">
                                <td colspan="8">
                                    <div id="galeria_<?php echo $prop['num_propiedad']; ?>">
                                        <?php
                                        $sql_fotos = "SELECT * FROM galeria WHERE idpropiedades = " . $prop['num_propiedad'];
                                        $result_fotos = mysqli_query($conexion, $sql_fotos);
                                        if(mysqli_num_rows($result_fotos) > 0) {
                                            echo '<div style="display:flex; gap:10px; flex-wrap:wrap;">';
                                            while($foto = mysqli_fetch_array($result_fotos)) {
                                                $principal = ($foto['principal'] == 1) ? 'border:2px solid #7e57c2;' : '';
                                                echo '<div style="text-align:center;">';
                                                echo '<img src="'.htmlspecialchars($foto['foto']).'" width="100" style="'.$principal.'margin-bottom:5px;"><br>';
                                                if($foto['principal'] != 1) {
                                                    echo '<button class="btn btn-sm btn-outline-primary" onclick="marcarPrincipal('.$foto['id'].', '.$prop['num_propiedad'].')">Principal</button> ';
                                                }
                                                echo '<button class="btn btn-sm btn-outline-danger" onclick="eliminarFoto('.$foto['id'].', '.$prop['num_propiedad'].')">Eliminar</button>';
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                        } else {
                                            echo '<span>No hay fotos para esta propiedad.</span>';
                                        }
                                        ?>
                                    </div>
                                    <form class="mt-3" enctype="multipart/form-data" onsubmit="return subirFotos(event, <?php echo $prop['num_propiedad']; ?>)">
                                        <input type="file" name="imagenes[]" multiple required accept="image/*">
                                        <button type="submit" class="btn btn-sm btn-success">Subir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Edición de Propiedad -->
    <div class="modal fade" id="modalEditarPropiedad" tabindex="-1" aria-labelledby="modalEditarPropiedadLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPropiedadLabel">Editar Propiedad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarPropiedad">
                        <input type="hidden" id="edit_num_propiedad" name="num_propiedad">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="edit_titulo" name="titulo" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_tipo_propiedad" class="form-label">Tipo de Propiedad</label>
                                <select class="form-select" id="edit_tipo_propiedad" name="tipo_propiedad" required>
                                    <option value="1">Casa</option>
                                    <option value="2">Departamento</option>
                                    <option value="3">Terreno</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_precio_pesos" class="form-label">Precio (CLP)</label>
                                <input type="number" class="form-control" id="edit_precio_pesos" name="precio_pesos" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_precio_uf" class="form-label">Precio (UF)</label>
                                <input type="number" class="form-control" id="edit_precio_uf" name="precio_uf" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_area_total" class="form-label">Área Total (m²)</label>
                                <input type="number" class="form-control" id="edit_area_total" name="area_total" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_area_construida" class="form-label">Área Construida (m²)</label>
                                <input type="number" class="form-control" id="edit_area_construida" name="area_construida" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_cant_domitorios" class="form-label">Dormitorios</label>
                                <input type="number" class="form-control" id="edit_cant_domitorios" name="cant_domitorios" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_cant_banos" class="form-label">Baños</label>
                                <input type="number" class="form-control" id="edit_cant_banos" name="cant_banos" required>
                            </div>
                            <div class="col-12">
                                <label for="edit_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="4" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_region" class="form-label">Región</label>
                                <select class="form-select" id="edit_region" name="region" required onchange="cargarProvincias()">
                                    <option value="">Seleccione región</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_provincia" class="form-label">Provincia</label>
                                <select class="form-select" id="edit_provincia" name="provincia" required onchange="cargarComunas()">
                                    <option value="">Seleccione provincia</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_comuna" class="form-label">Comuna</label>
                                <select class="form-select" id="edit_comuna" name="comuna" required onchange="cargarSectores()">
                                    <option value="">Seleccione comuna</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_sector" class="form-label">Sector</label>
                                <select class="form-select" id="edit_sector" name="sector" required>
                                    <option value="">Seleccione sector</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_bodega" name="bodega" value="1">
                                    <label class="form-check-label" for="edit_bodega">Bodega</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_estacionamiento" name="estacionamiento" value="1">
                                    <label class="form-check-label" for="edit_estacionamiento">Estacionamiento</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_logia" name="logia" value="1">
                                    <label class="form-check-label" for="edit_logia">Logia</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_cocina_amoblada" name="cocina_amoblada" value="1">
                                    <label class="form-check-label" for="edit_cocina_amoblada">Cocina Amoblada</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_antejardin" name="antejardin" value="1">
                                    <label class="form-check-label" for="edit_antejardin">Antejardín</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_patio_trasero" name="patio_trasero" value="1">
                                    <label class="form-check-label" for="edit_patio_trasero">Patio Trasero</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_piscina" name="piscina" value="1">
                                    <label class="form-check-label" for="edit_piscina">Piscina</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_estado" class="form-label">Estado</label>
                                <select class="form-select" id="edit_estado" name="estado" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="js/propiedades.js"></script>
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/filtros.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleForm(id) {
        var form = document.getElementById(id);
        if (form.classList.contains('expanded')) {
            form.classList.remove('expanded');
        } else {
            form.classList.add('expanded');
        }
    }

    // Envío AJAX del formulario de registro de propiedad
    document.getElementById('registroPropiedadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = document.getElementById('registroPropiedadForm');
        var formData = new FormData(form);
        fetch('registrar_propiedad_propietario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    confirmButtonColor: '#7c3aed'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonColor: '#7c3aed'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al registrar la propiedad',
                confirmButtonColor: '#7c3aed'
            });
        });
    });

    // Función para abrir el modal de edición y cargar datos
    function editarPropiedad(num_propiedad) {
        fetch('obtener_propiedad.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'num_propiedad=' + num_propiedad
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const prop = data.propiedad;
                document.getElementById('edit_num_propiedad').value = prop.num_propiedad;
                document.getElementById('edit_titulo').value = prop.titulopropiedad;
                document.getElementById('edit_precio_pesos').value = prop.precio_pesos;
                document.getElementById('edit_precio_uf').value = prop.precio_uf;
                document.getElementById('edit_area_total').value = prop.area_total;
                document.getElementById('edit_area_construida').value = prop.area_construida;
                document.getElementById('edit_cant_domitorios').value = prop.cant_domitorios;
                document.getElementById('edit_cant_banos').value = prop.cant_banos;
                document.getElementById('edit_descripcion').value = prop.descripcion;
                document.getElementById('edit_tipo_propiedad').value = prop.idtipo_propiedad;
                document.getElementById('edit_sector').value = prop.idsectores;
                document.getElementById('edit_bodega').checked = prop.bodega === '1';
                document.getElementById('edit_estacionamiento').checked = prop.estacionamiento === '1';
                document.getElementById('edit_logia').checked = prop.logia === '1';
                document.getElementById('edit_cocina_amoblada').checked = prop.cocinaamoblada === '1';
                document.getElementById('edit_antejardin').checked = prop.antejardin === '1';
                document.getElementById('edit_patio_trasero').checked = prop.patiotrasero === '1';
                document.getElementById('edit_piscina').checked = prop.piscina === '1';
                document.getElementById('edit_estado').value = prop.estado;
                // Llenar regiones antes de cargar la ubicación
                cargarRegiones('edit_region');
                setTimeout(function() {
                    cargarUbicacion(prop.idsectores);
                }, 300);
                var modal = new bootstrap.Modal(document.getElementById('modalEditarPropiedad'));
                modal.show();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        });
    }

    // Función para cargar la ubicación
    function cargarUbicacion(idsectores) {
        fetch('obtener_ubicacion.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'idsectores=' + idsectores
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('edit_region').value = data.region;
                cargarProvincias(data.region, 'edit_provincia', function() {
                    document.getElementById('edit_provincia').value = data.provincia;
                    cargarComunas(data.provincia, 'edit_comuna', function() {
                        document.getElementById('edit_comuna').value = data.comuna;
                        cargarSectores(data.comuna, 'edit_sector', function() {
                            document.getElementById('edit_sector').value = data.sector;
                        });
                    });
                });
            }
        });
    }

    // Envío AJAX del formulario de edición
    document.getElementById('formEditarPropiedad').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('actualizar_propiedad.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message || 'Propiedad actualizada correctamente',
                    confirmButtonColor: '#7c3aed'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Ocurrió un error al actualizar la propiedad',
                    confirmButtonColor: '#7c3aed'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al actualizar la propiedad',
                confirmButtonColor: '#7c3aed'
            });
        });
    });

    // Función para eliminar propiedad
    function eliminarPropiedad(num_propiedad) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará la propiedad y sus imágenes',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('eliminar_propiedad.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + num_propiedad
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({ icon: 'success', title: '¡Eliminado!', text: data.message, confirmButtonColor: '#7c3aed' }).then(() => { window.location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#7c3aed' });
                    }
                });
            }
        });
    }

    function cargarRegiones(selectId = 'region') {
        fetch('obtener_ubicaciones.php?tipo=regiones')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById(selectId);
                    select.innerHTML = '<option value="">Seleccione región</option>';
                    data.data.forEach(region => {
                        select.innerHTML += `<option value="${region.idregion}">${region.nombre_region}</option>`;
                    });
                } else {
                    console.error('Error al cargar regiones:', data.error);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function cargarProvincias(regionId = null, selectId = 'provincia', callback = null) {
        if (!regionId) {
            regionId = document.getElementById(selectId === 'provincia' ? 'region' : 'edit_region').value;
        }
        if (regionId) {
            fetch(`obtener_ubicaciones.php?tipo=provincias&id=${regionId}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById(selectId);
                    select.innerHTML = '<option value="">Seleccione provincia</option>';
                    data.data.forEach(provincia => {
                        select.innerHTML += `<option value="${provincia.idprovincias}">${provincia.nombre_provincia}</option>`;
                    });
                    if (callback) callback();
                })
                .catch(error => console.error('Error al cargar provincias:', error));
        }
    }

    function cargarComunas(provinciaId = null, selectId = 'comuna', callback = null) {
        if (!provinciaId) {
            provinciaId = document.getElementById(selectId === 'comuna' ? 'provincia' : 'edit_provincia').value;
        }
        if (provinciaId) {
            fetch(`obtener_ubicaciones.php?tipo=comunas&id=${provinciaId}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById(selectId);
                    select.innerHTML = '<option value="">Seleccione comuna</option>';
                    data.data.forEach(comuna => {
                        select.innerHTML += `<option value="${comuna.idcomunas}">${comuna.nombre_comuna}</option>`;
                    });
                    if (callback) callback();
                })
                .catch(error => console.error('Error al cargar comunas:', error));
        }
    }

    function cargarSectores(comunaId = null, selectId = 'sector', callback = null) {
        if (!comunaId) {
            comunaId = document.getElementById(selectId === 'sector' ? 'comuna' : 'edit_comuna').value;
        }
        if (comunaId) {
            fetch(`obtener_ubicaciones.php?tipo=sectores&id=${comunaId}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById(selectId);
                    select.innerHTML = '<option value="">Seleccione sector</option>';
                    data.data.forEach(sector => {
                        select.innerHTML += `<option value="${sector.idsectores}">${sector.nombre_sector}</option>`;
                    });
                    if (callback) callback();
                })
                .catch(error => console.error('Error al cargar sectores:', error));
        }
    }

    function toggleFotos(id) {
        var row = document.getElementById('fotos_' + id);
        if(row.style.display === 'none') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }

    function subirFotos(event, idProp) {
        event.preventDefault();
        var form = event.target;
        var formData = new FormData(form);
        formData.append('idpropiedad', idProp);
        fetch('subir_fotos_propiedad.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire('Éxito', data.message, 'success').then(() => { location.reload(); });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'Ocurrió un error al subir las fotos', 'error'));
        return false;
    }

    function eliminarFoto(idFoto, idProp) {
        Swal.fire({
            title: '¿Eliminar foto?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.isConfirmed) {
                fetch('eliminar_foto_propiedad.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'idfoto=' + idFoto
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        Swal.fire('Eliminada', data.message, 'success').then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Ocurrió un error al eliminar la foto', 'error'));
            }
        });
    }

    function marcarPrincipal(idFoto, idProp) {
        fetch('marcar_principal_foto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'idfoto=' + idFoto + '&idpropiedad=' + idProp
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire('Éxito', data.message, 'success').then(() => { location.reload(); });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'Ocurrió un error al marcar la foto principal', 'error'));
    }
    </script>
</body>
</html> 