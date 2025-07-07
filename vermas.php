<?php
include("setup/conexion.php");

if (!isset($_GET['idpro'])) {
    header("Location: index.php");
    exit;
}

$id_propiedad = $_GET['idpro'];

// Función para verificar y reconectar si es necesario
function verificarConexion($conexion) {
    if (!$conexion || !mysqli_ping($conexion)) {
        return conectar(); // Intenta reconectar
    }
    return $conexion;
}

// Obtener conexión y verificar
$conexion = conectar();
$conexion = verificarConexion($conexion);

// Consulta para obtener los detalles de la propiedad
$sql = "SELECT 
    p.*,
    t.tipo,
    s.nombre_sector,
    c.nombre_comuna,
    pr.nombre_provincia,
    r.nombre_region
FROM 
    propiedades p
    JOIN tipo_propiedad t ON p.idtipo_propiedad = t.idtipo_propiedad
    JOIN sectores s ON p.idsectores = s.idsectores
    JOIN comunas c ON s.idcomunas = c.idcomunas
    JOIN provincias pr ON c.idprovincias = pr.idprovincias
    JOIN regiones r ON pr.idregion = r.idregion
WHERE 
    p.num_propiedad = ?";

try {
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . mysqli_error($conexion));
    }

    mysqli_stmt_bind_param($stmt, "i", $id_propiedad);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al ejecutar la consulta: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $propiedad = mysqli_fetch_assoc($result);

    if (!$propiedad) {
        header("Location: index.php");
        exit;
    }

    // Consulta para obtener todas las fotos de la propiedad
    $sql_fotos = "SELECT foto FROM galeria WHERE idpropiedades = ? AND estado = 1";
    $stmt_fotos = mysqli_prepare($conexion, $sql_fotos);
    
    if (!$stmt_fotos) {
        throw new Exception("Error en la preparación de la consulta de fotos: " . mysqli_error($conexion));
    }

    mysqli_stmt_bind_param($stmt_fotos, "i", $id_propiedad);
    
    if (!mysqli_stmt_execute($stmt_fotos)) {
        throw new Exception("Error al ejecutar la consulta de fotos: " . mysqli_stmt_error($stmt_fotos));
    }

    $result_fotos = mysqli_stmt_get_result($stmt_fotos);
    $fotos = mysqli_fetch_all($result_fotos, MYSQLI_ASSOC);

    // Formatear precios
    $fmt = numfmt_create('es_CL', NumberFormatter::CURRENCY);
    $precio_pesos = numfmt_format_currency($fmt, $propiedad['precio_pesos'], "CLP");

} catch (Exception $e) {
    // Log del error
    error_log("Error en vermas.php: " . $e->getMessage());
    // Redirigir a una página de error o mostrar mensaje amigable
    header("Location: error.html");
    exit;
}

// Cerrar las consultas preparadas
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
if (isset($stmt_fotos)) {
    mysqli_stmt_close($stmt_fotos);
}
// Cerrar la conexión
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $propiedad['titulopropiedad']; ?> - PNK Inmobiliaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/inicio.css">
    <style>
        .detalle-propiedad {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .galeria-fotos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .galeria-fotos img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .galeria-fotos img:hover {
            transform: scale(1.02);
        }
        .info-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin: 2rem 0;
        }
        .precio {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .precio .uf {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .precio .clp {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .caracteristicas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .caracteristica {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        .caracteristica i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #007bff;
        }
        .ubicacion {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin: 2rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        .ubicacion h3 {
            color: #007bff;
            margin-bottom: 1rem;
        }
        .amenidades {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin: 2rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        .amenidades h3 {
            color: #007bff;
            margin-bottom: 1rem;
        }
        .amenidades-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
        }
        .amenidad {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 6px;
            background: #f8f9fa;
        }
        .amenidad i {
            color: #28a745;
        }
        .amenidad.no-disponible {
            opacity: 0.5;
        }
        .amenidad.no-disponible i {
            color: #dc3545;
        }
        .descripcion {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin: 2rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        .descripcion h3 {
            color: #007bff;
            margin-bottom: 1rem;
        }
        .btn-volver {
            margin-top: 2rem;
            text-align: center;
        }
        .btn-volver .btn {
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-volver .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .info-container {
                grid-template-columns: 1fr;
            }
            .galeria-fotos {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-izquierda">
            <img src="img/Logo.png?v=1" alt="Logo PNK" class="logo">
            <div class="titulo">PNK INMOBILIARIA</div>
        </div>
    </header>

    <main class="detalle-propiedad">
        <h1 class="mb-4"><?php echo $propiedad['titulopropiedad']; ?></h1>
        
        <div class="galeria-fotos">
            <?php if (count($fotos) > 1): ?>
                <div id="carouselFotos" class="carousel slide w-100" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($fotos as $index => $foto): ?>
                            <div class="carousel-item <?php if ($index === 0) echo 'active'; ?>">
                                <img src="<?php echo htmlspecialchars($foto['foto']); ?>" class="d-block w-100" alt="Foto propiedad">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselFotos" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselFotos" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            <?php elseif (count($fotos) === 1): ?>
                <img src="<?php echo htmlspecialchars($fotos[0]['foto']); ?>" alt="Foto propiedad" class="d-block w-100">
            <?php endif; ?>
        </div>

        <div class="info-container">
            <div>
                <div class="precio">
                    <div class="uf">UF <?php echo number_format($propiedad['precio_uf'], 0, ',', '.'); ?></div>
                    <div class="clp"><?php echo $precio_pesos; ?></div>
                </div>

                <div class="caracteristicas">
                    <div class="caracteristica">
                        <i class="bi bi-house"></i>
                        <div>Tipo: <?php echo $propiedad['tipo']; ?></div>
                    </div>
                    <div class="caracteristica">
                        <i class="bi bi-door-open"></i>
                        <div>Dormitorios: <?php echo $propiedad['cant_domitorios']; ?></div>
                    </div>
                    <div class="caracteristica">
                        <i class="bi bi-droplet"></i>
                        <div>Baños: <?php echo $propiedad['cant_banos']; ?></div>
                    </div>
                    <div class="caracteristica">
                        <i class="bi bi-rulers"></i>
                        <div>Área Total: <?php echo $propiedad['area_total']; ?> m²</div>
                    </div>
                    <div class="caracteristica">
                        <i class="bi bi-building"></i>
                        <div>Área Construida: <?php echo $propiedad['area_construida']; ?> m²</div>
                    </div>
                </div>

                <div class="ubicacion">
                    <h3><i class="bi bi-geo-alt"></i> Ubicación</h3>
                    <p>
                        <?php echo $propiedad['nombre_sector']; ?>, 
                        <?php echo $propiedad['nombre_comuna']; ?>, 
                        <?php echo $propiedad['nombre_provincia']; ?>, 
                        <?php echo $propiedad['nombre_region']; ?>
                    </p>
                </div>
            </div>

            <div>
                <div class="amenidades">
                    <h3><i class="bi bi-check2-circle"></i> Amenidades</h3>
                    <div class="amenidades-grid">
                        <div class="amenidad <?php echo $propiedad['bodega'] ? '' : 'no-disponible'; ?>">
                            <i class="bi <?php echo $propiedad['bodega'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>"></i>
                            <span>Bodega</span>
                        </div>
                        
                        <div class="amenidad <?php echo $propiedad['estacionamiento'] ? '' : 'no-disponible'; ?>">
                            <i class="bi <?php echo $propiedad['estacionamiento'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>"></i>
                            <span>Estacionamiento</span>
                        </div>
                        
                        <div class="amenidad <?php echo $propiedad['logia'] ? '' : 'no-disponible'; ?>">
                            <i class="bi <?php echo $propiedad['logia'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>"></i>
                            <span>Logia</span>
                        </div>
                        
                        <div class="amenidad <?php echo $propiedad['cocinaamoblada'] ? '' : 'no-disponible'; ?>">
                            <i class="bi <?php echo $propiedad['cocinaamoblada'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>"></i>
                            <span>Cocina Amoblada</span>
                        </div>
                        
                        <div class="amenidad <?php echo $propiedad['antejardin'] ? '' : 'no-disponible'; ?>">
                            <i class="bi <?php echo $propiedad['antejardin'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>"></i>
                            <span>Antejardín</span>
                        </div>
                        
                        <div class="amenidad <?php echo $propiedad['patiotrasero'] ? '' : 'no-disponible'; ?>">
                            <i class="bi <?php echo $propiedad['patiotrasero'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>"></i>
                            <span>Patio Trasero</span>
                        </div>
                        
                        <div class="amenidad <?php echo $propiedad['piscina'] ? '' : 'no-disponible'; ?>">
                            <i class="bi <?php echo $propiedad['piscina'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>"></i>
                            <span>Piscina</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="descripcion">
            <h3><i class="bi bi-info-circle"></i> Descripción</h3>
            <p><?php echo nl2br($propiedad['descripcion']); ?></p>
        </div>

        <div class="btn-volver">
            <a href="index.php" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Volver al inicio
            </a>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-izquierda">
            <img src="img/Logo.png?v=1" alt="Logo PNK" class="logo-footer">
            <div class="titulo-footer">PNK INMOBILIARIA</div>
        </div>

        <nav class="footer-centro">
            <a href="registro_propietario.html" class="enlace-footer">Registro Propietario</a>
            <a href="registro_gestor.html" class="enlace-footer">Registro Gestor</a>
        </nav>

        <nav class="footer-derecha">
            <a href="https://www.instagram.com/" target="_blank">
                <img src="img/logo-insta.png?v=1" alt="Logo instagram" class="logo-footer">
            </a>
            <a href="https://www.linkedin.com/feed/" target="_blank">
                <img src="img/linkedin.png?v=1" alt="Logo Linkedin" class="logo-footer">
            </a>
        </nav>
    </footer>

    <div class="copyright">
        &copy; 2025 Todos los derechos Reservados PNK Inmobiliaria
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 