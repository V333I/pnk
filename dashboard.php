<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include("setup/conexion.php"); // Incluye el archivo de configuración para la conexión a la base de datos

function contarusu() {
    $conexion = conectar();
    $sql = "SELECT COUNT(*) as total FROM usuarios";
    $result = mysqli_query($conexion, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Verificar si el usuario está logueado y es administrador
if(isset($_SESSION['usuario']) && isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'admin')
{

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <style>
        .registro-forma {
            background-color: #fff;
            width: 800px;
            color: #555;
            border-radius: 15px;
            padding: 2.5rem;
            margin: 2rem auto;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
        }

        .registro-forma h2 {
            color: #7e57c2;
            font-size: 1.8rem;
            margin-bottom: 2rem;
            font-weight: 600;
            text-align: center;
        }

        .input-group {
            margin-bottom: 1.5rem;
            text-align: left;
            width: 48%;
            box-sizing: border-box;
            float: left;
            background-color: #fff;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid #e0e0e0;
        }

        .input-group.full-width {
            width: 100%;
            clear: both;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 20px;
            width: 100%;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 600;
            font-size: 1rem;
            color: #444;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: #7e57c2;
            outline: none;
            box-shadow: 0 0 0 3px rgba(126, 87, 194, 0.1);
            background-color: #fff;
        }

        .input-group small {
            display: block;
            margin-top: 0.5rem;
            color: #666;
            font-size: 0.85rem;
        }

        button[type="submit"] {
            width: 100%;
            padding: 1rem 2rem;
            background-color: #7e57c2;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 2rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        button[type="submit"]:hover {
            background-color: #673ab7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(126, 87, 194, 0.2);
        }

        .button-container {
            width: 100%;
            text-align: center;
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }

        .toggle-button {
            background-color: #7e57c2;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(126, 87, 194, 0.2);
        }

        .toggle-button:hover {
            background-color: #673ab7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(126, 87, 194, 0.3);
        }

        .form-container {
            display: none;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(-10px);
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .form-container.expanded {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* Estilos para el input de archivo */
        .input-group input[type="file"] {
            padding: 0.5rem;
            background-color: #f8f9fa;
            border: 2px dashed #7e57c2;
            cursor: pointer;
        }

        .input-group input[type="file"]:hover {
            background-color: #f0f0f0;
        }

        /* Estilos para los mensajes de error */
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.3rem;
            display: none;
        }

        .input-group.error input,
        .input-group.error select {
            border-color: #dc3545;
        }

        .input-group.error .error-message {
            display: block;
        }

        /* Ajuste para el contenedor principal */
        #formulario {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .contenido-dashboard {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin-bottom: 20px;
            gap: 15px;
        }

        .texto-icono {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            width: 100%;
        }

        .texto-icono.cerrar-sesion {
            background-color: #7e57c2;
            padding: 12px 25px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(126, 87, 194, 0.2);
            width: auto;
        }

        .texto-icono.cerrar-sesion:hover {
            background-color: #673ab7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(126, 87, 194, 0.3);
        }

        .texto-icono.cerrar-sesion a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .texto-icono.cerrar-sesion img {
            width: 20px;
            height: 20px;
            filter: brightness(0) invert(1);
        }

        .dashboard {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .caracteristicas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .caracteristica {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .caracteristica input[type="checkbox"] {
            width: auto;
        }

        .caracteristica label {
            margin: 0;
            font-weight: normal;
        }

        textarea {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            resize: vertical;
        }

        textarea:focus {
            border-color: #7e57c2;
            outline: none;
            box-shadow: 0 0 0 3px rgba(126, 87, 194, 0.1);
            background-color: #fff;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-izquierda">
            <img src="img/Logo.png?v=2" alt="Logo PNK" class="logo">
            <div class="titulo">PNK INMOBILIARIA</div>
        </div>
    </header>

    <main class="main">
        <div class="dashboard">
            <div class="contenido-dashboard">
                <div class="texto-icono">
                    <span><img src="img/dash.png?v=2" alt="Dashboard">  Bienvenido: <?php echo $_SESSION['usuario']; ?>
                    </span>
                </div>
                <div class="texto-icono cerrar-sesion">
                    <img src="img/exit.png?v=2" alt="Cerrar sesión">
                    <a href="cerrar.php">Cerrar sesión</a>
                </div>
            </div>
            <div id="formulario">
                <div class="button-container">
                    <button class="toggle-button" onclick="toggleForm('formGestorContainer')">Registro de Gestor</button>
                    <button class="toggle-button" onclick="toggleForm('propiedadForm')">Registrar Nueva Propiedad</button>
                </div>
                <div id="formGestorContainer" class="form-container">
                    <div class="registro-forma">
                        <h2>Registro de Gestor Inmobiliario</h2>
                        <form id="formGestor" action="procesar_gestor.php" method="post" onsubmit="return validarFormularioGestor(event)">
                            <div class="form-row">
                                <div class="input-group">
                                    <label for="run">RUN:</label>
                                    <input type="text" name="run" id="run" placeholder="12345678-9">
                                    <div class="error-message">Ingrese un RUT válido</div>
                                </div>
                                <div class="input-group">
                                    <label for="nombre">Nombre Completo:</label>
                                    <input type="text" name="nombre" id="nombre" placeholder="Ingrese su nombre completo">
                                    <div class="error-message">Este campo es obligatorio</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="input-group">
                                    <label for="fecha">Fecha de Nacimiento:</label>
                                    <input type="date" name="fecha" id="fecha" max="">
                                    <div class="error-message">Seleccione una fecha válida</div>
                                </div>
                                <div class="input-group">
                                    <label for="correo">Correo Electrónico:</label>
                                    <input type="email" name="correo" id="correo" placeholder="ejemplo@correo.com">
                                    <div class="error-message">Ingrese un correo válido</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="input-group">
                                    <label for="password">Contraseña:</label>
                                    <input type="password" name="password" id="password" placeholder="Ingrese su contraseña">
                                    <small>Mínimo 8 caracteres, una mayúscula, una minúscula y un carácter especial</small>
                                    <div class="error-message">La contraseña no cumple con los requisitos</div>
                                </div>
                                <div class="input-group">
                                    <label for="password2">Confirmar Contraseña:</label>
                                    <input type="password" name="password2" id="password2" placeholder="Repita su contraseña">
                                    <div class="error-message">Las contraseñas no coinciden</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="input-group">
                                    <label for="genero">Sexo:</label>
                                    <select name="genero" id="genero">
                                        <option value="">Seleccione...</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                        <option value="O">Otro</option>
                                    </select>
                                    <div class="error-message">Seleccione una opción</div>
                                </div>
                                <div class="input-group">
                                    <label for="telefono">Teléfono:</label>
                                    <input type="text" name="telefono" id="telefono" placeholder="+56912345678">
                                    <div class="error-message">Ingrese un teléfono válido</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="input-group full-width">
                                    <label for="certificado">Certificado de Antecedentes:</label>
                                    <input type="file" name="certificado" id="certificado" accept=".pdf,.jpg,.jpeg,.png">
                                    <small>Formatos permitidos: PDF, JPG, JPEG, PNG (Máximo 5MB)</small>
                                    <div class="error-message">Seleccione un archivo válido</div>
                                </div>
                            </div>
                            <button type="submit">Registrar Gestor</button>
                        </form>
                    </div>
                </div>
                <div id="propiedadForm" class="form-container">
                    <form id="registroPropiedadForm" class="registro-forma" enctype="multipart/form-data">
                        <h2>Registro de Propiedad</h2>
                        <div class="form-row">
                            <div class="input-group">
                                <label for="rut_propietario">Propietario</label>
                                <select id="rut_propietario" name="rut_propietario" required>
                                    <option value="">Seleccione propietario</option>
                                    <?php
                                    $result_prop = mysqli_query(conectar(), "SELECT rut, nombre_completo FROM propietarios");
                                    while($prop = mysqli_fetch_array($result_prop)) {
                                        echo '<option value="'.htmlspecialchars($prop['rut']).'">'.htmlspecialchars($prop['rut']).' - '.htmlspecialchars($prop['nombre_completo']).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="titulo">Título de la Propiedad</label>
                                <input type="text" id="titulo" name="titulo" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-group">
                                <label for="tipo_propiedad">Tipo de Propiedad</label>
                                <select id="tipo_propiedad" name="tipo_propiedad" required>
                                    <option value="">Seleccione tipo</option>
                                    <option value="1">Casa</option>
                                    <option value="2">Departamento</option>
                                    <option value="3">Terreno</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="region">Región</label>
                                <select id="region" name="region" required onchange="cargarProvincias()">
                                    <option value="">Seleccione región</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-group">
                                <label for="provincia">Provincia</label>
                                <select id="provincia" name="provincia" required onchange="cargarComunas()">
                                    <option value="">Seleccione provincia</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="comuna">Comuna</label>
                                <select id="comuna" name="comuna" required onchange="cargarSectores()">
                                    <option value="">Seleccione comuna</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-group">
                                <label for="sector">Sector</label>
                                <select id="sector" name="sector" required>
                                    <option value="">Seleccione sector</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="precio_pesos">Precio (CLP)</label>
                                <input type="number" id="precio_pesos" name="precio_pesos" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-group">
                                <label for="precio_uf">Precio (UF)</label>
                                <input type="number" id="precio_uf" name="precio_uf" required>
                            </div>
                            <div class="input-group">
                                <label for="area_total">Área Total (m²)</label>
                                <input type="number" id="area_total" name="area_total" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-group">
                                <label for="area_construida">Área Construida (m²)</label>
                                <input type="number" id="area_construida" name="area_construida" required>
                            </div>
                            <div class="input-group">
                                <label for="cant_domitorios">Dormitorios</label>
                                <input type="number" id="cant_domitorios" name="cant_domitorios" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-group">
                                <label for="cant_banos">Baños</label>
                                <input type="number" id="cant_banos" name="cant_banos" required>
                            </div>
                            <div class="input-group full-width">
                                <label for="descripcion">Descripción</label>
                                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-group full-width">
                                <label>Características</label>
                                <div class="caracteristicas-grid">
                                    <div class="caracteristica">
                                        <input type="checkbox" id="bodega" name="bodega">
                                        <label for="bodega">Bodega</label>
                                    </div>
                                    <div class="caracteristica">
                                        <input type="checkbox" id="estacionamiento" name="estacionamiento">
                                        <label for="estacionamiento">Estacionamiento</label>
                                    </div>
                                    <div class="caracteristica">
                                        <input type="checkbox" id="logia" name="logia">
                                        <label for="logia">Logia</label>
                                    </div>
                                    <div class="caracteristica">
                                        <input type="checkbox" id="cocina_amoblada" name="cocina_amoblada">
                                        <label for="cocina_amoblada">Cocina Amoblada</label>
                                    </div>
                                    <div class="caracteristica">
                                        <input type="checkbox" id="antejardin" name="antejardin">
                                        <label for="antejardin">Antejardín</label>
                                    </div>
                                    <div class="caracteristica">
                                        <input type="checkbox" id="patio_trasero" name="patio_trasero">
                                        <label for="patio_trasero">Patio Trasero</label>
                                    </div>
                                    <div class="caracteristica">
                                        <input type="checkbox" id="piscina" name="piscina">
                                        <label for="piscina">Piscina</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-group full-width">
                                <label for="imagenes">Imágenes de la Propiedad</label>
                                <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*" required>
                                <small>Puede seleccionar múltiples imágenes. La primera será la imagen principal.</small>
                            </div>
                        </div>

                        <button type="submit">Registrar Propiedad</button>
                    </form>
                </div>
            </div>
        </div>
        <br>
        <div id="mostrarusuarios">
            <div class="card mb-4">
                <div class="card-header">Lista de Usuarios del Sistema (<b>Total: <?php echo contarusu();?></b>)</div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rut</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Usuario</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $sql = "select * from usuarios";
                            $result = mysqli_query(conectar(), $sql);
                            while($datos=mysqli_fetch_array($result))
                            {
                        ?>
                            <tr>
                                <td><?php echo $datos['rut'];?></td>
                                <td><?php echo $datos['nombres'];?></td>
                                <td><?php echo $datos['ap_paterno']." ".$datos['ap_materno'];?></td>
                                <td><?php echo $datos['usuario'];?></td>
                                <td>
                                    <?php
                                    if($datos['estado']==1)
                                    {
                                    ?>
                                        <img src="img/check.png" width="16px">
                                    <?php
                                    }else{
                                    ?>
                                        <img src="img/ina.png" width="16px">
                                    <?php    
                                    }
                                    ?>
                                </td>
                                <td>
                                    <img src="img/update.png?v=1" width="16px" style="cursor: pointer;" onclick="abrirModalEditar('<?php echo $datos['id'];?>', '<?php echo $datos['rut'];?>', '<?php echo $datos['nombres'];?>', '<?php echo $datos['ap_paterno'];?>', '<?php echo $datos['ap_materno'];?>', '<?php echo $datos['usuario'];?>', '<?php echo $datos['estado'];?>')">
                                    |
                                    <img src="img/borrar.png?v=1" width="16px" style="cursor: pointer;" onclick="eliminarUsuario('<?php echo $datos['id'];?>')">
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Nueva sección para Propietarios -->
            <div class="card">
                <div class="card-header">Lista de Propietarios Registrados</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>RUT</th>
                                    <th>Nombre Completo</th>
                                    <th>Fecha Nacimiento</th>
                                    <th>Correo</th>
                                    <th>Sexo</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql_propietarios = "SELECT * FROM propietarios";
                                $result_propietarios = mysqli_query(conectar(), $sql_propietarios);
                                while($propietario = mysqli_fetch_array($result_propietarios))
                                {
                                    // Convertir el formato de la fecha
                                    $fecha = date('d/m/Y', strtotime($propietario['fecha_nacimiento']));
                                    // Convertir el sexo a texto
                                    $sexo = ($propietario['sexo'] == 'M') ? 'Masculino' : (($propietario['sexo'] == 'F') ? 'Femenino' : 'Otro');
                            ?>
                                <tr>
                                    <td><?php echo $propietario['rut'];?></td>
                                    <td><?php echo $propietario['nombre_completo'];?></td>
                                    <td><?php echo $fecha;?></td>
                                    <td><?php echo $propietario['correo'];?></td>
                                    <td><?php echo $sexo;?></td>
                                    <td><?php echo $propietario['telefono'];?></td>
                                    <td>
                                        <img src="img/update.png?v=1" width="16px" style="cursor: pointer;" onclick="abrirModalEditarPropietario('<?php echo $propietario['id'];?>', '<?php echo $propietario['rut'];?>', '<?php echo $propietario['nombre_completo'];?>', '<?php echo $propietario['fecha_nacimiento'];?>', '<?php echo $propietario['correo'];?>', '<?php echo $propietario['sexo'];?>', '<?php echo $propietario['telefono'];?>')">
                                        <img src="img/delete.png?v=1" width="16px" style="cursor: pointer;" onclick="eliminarPropietario('<?php echo $propietario['id'];?>')">
                                    </td>
                                </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Nueva sección para Gestores -->
            <div class="card mb-4">
                <div class="card-header">Lista de Gestores Registrados</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>RUT</th>
                                    <th>Nombre Completo</th>
                                    <th>Fecha Nacimiento</th>
                                    <th>Correo</th>
                                    <th>Sexo</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql_gestores = "SELECT * FROM gestores";
                                $result_gestores = mysqli_query(conectar(), $sql_gestores);
                                while($gestor = mysqli_fetch_array($result_gestores))
                                {
                                    // Convertir el formato de la fecha
                                    $fecha = date('d/m/Y', strtotime($gestor['fecha_nacimiento']));
                                    // Convertir el sexo a texto
                                    $sexo = ($gestor['sexo'] == 'M') ? 'Masculino' : (($gestor['sexo'] == 'F') ? 'Femenino' : 'Otro');
                            ?>
                                <tr>
                                    <td><?php echo $gestor['rut'];?></td>
                                    <td><?php echo $gestor['nombre_completo'];?></td>
                                    <td><?php echo $fecha;?></td>
                                    <td><?php echo $gestor['correo'];?></td>
                                    <td><?php echo $sexo;?></td>
                                    <td><?php echo $gestor['telefono'];?></td>
                                    <td>
                                        <?php
                                        if($gestor['estado']==1)
                                        {
                                        ?>
                                            <img src="img/check.png?v=1" width="16px">
                                        <?php
                                        }else{
                                        ?>
                                            <img src="img/ina.png?v=1" width="16px">
                                        <?php    
                                        }
                                        ?>
                                    </td>
                                    <td>
<img src="img/update.png?v=1" width="16px" style="cursor: pointer;" onclick="abrirModalEditarGestor('<?php echo $gestor['id'];?>', '<?php echo $gestor['rut'];?>', '<?php echo $gestor['nombre_completo'];?>', '<?php echo $gestor['fecha_nacimiento'];?>', '<?php echo $gestor['correo'];?>', '<?php echo $gestor['sexo'];?>', '<?php echo $gestor['telefono'];?>', '<?php echo $gestor['estado'];?>')">
                                        |
                                        <img src="img/borrar.png?v=1" width="16px" style="cursor: pointer;" onclick="eliminarGestor('<?php echo $gestor['id'];?>')">
                                    </td>
                                </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Nueva sección para Propiedades -->
            <div class="card mb-4">
                <div class="card-header">Lista de Propiedades Registradas</div>
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
                            <?php
                            $sql_propiedades = "SELECT p.*, t.tipo, s.nombre_sector FROM propiedades p 
                                LEFT JOIN tipo_propiedad t ON p.idtipo_propiedad = t.idtipo_propiedad 
                                LEFT JOIN sectores s ON p.idsectores = s.idsectores 
                                ORDER BY p.num_propiedad DESC";
                            $result_propiedades = mysqli_query(conectar(), $sql_propiedades);
                            while($prop = mysqli_fetch_array($result_propiedades)) {
                                $estado = ($prop['estado'] == 1) ? '<img src=\'img/check.png?v=1\' width=\'16px\'>' : '<img src=\'img/ina.png?v=1\' width=\'16px\'>';
                            ?>
                                <tr>
                                    <td><?php echo $prop['num_propiedad']; ?></td>
                                    <td><?php echo htmlspecialchars($prop['titulopropiedad']); ?></td>
                                    <td><?php echo htmlspecialchars($prop['tipo']); ?></td>
                                    <td><?php echo htmlspecialchars($prop['nombre_sector']); ?></td>
                                    <td><?php echo number_format($prop['precio_pesos'], 0, ',', '.'); ?></td>
                                    <td><?php echo number_format($prop['precio_uf'], 0, ',', '.'); ?></td>
                                    <td><?php echo $estado; ?></td>
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
                                            $result_fotos = mysqli_query(conectar(), $sql_fotos);
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
                                            <form class="mt-3" enctype="multipart/form-data" onsubmit="return subirFotos(event, <?php echo $prop['num_propiedad']; ?>)">
                                                <label>Subir nuevas fotos:</label>
                                                <input type="file" name="imagenes[]" multiple accept="image/*" required>
                                                <button type="submit" class="btn btn-sm btn-success">Subir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Edición -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditar" onsubmit="actualizarUsuario(event)">
                        <input type="hidden" id="editId" name="id">
                        <div class="mb-3">
                            <label for="editRut" class="form-label">RUT</label>
                            <input type="text" class="form-control" id="editRut" name="rut" required>
                        </div>
                        <div class="mb-3">
                            <label for="editNombres" class="form-label">Nombres</label>
                            <input type="text" class="form-control" id="editNombres" name="nombres" required>
                        </div>
                        <div class="mb-3">
                            <label for="editApPaterno" class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="editApPaterno" name="ap_paterno" required>
                        </div>
                        <div class="mb-3">
                            <label for="editApMaterno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="editApMaterno" name="ap_materno" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUsuario" class="form-label">Usuario</label>
                            <input type="email" class="form-control" id="editUsuario" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEstado" class="form-label">Estado</label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" style="background-color: #AC1754; border-color: #AC1754;">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Propietario -->
    <div class="modal fade" id="modalEditarPropietario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Propietario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarPropietario" onsubmit="actualizarPropietario(event)">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_rut" class="form-label">RUT</label>
                            <input type="text" class="form-control" id="edit_rut" name="rut" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre_completo" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_fecha" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="edit_fecha" name="fecha_nacimiento" max="" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="edit_correo" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_sexo" class="form-label">Sexo</label>
                            <select class="form-control" id="edit_sexo" name="sexo" required>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="O">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="edit_telefono" name="telefono" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Gestor -->
    <div class="modal fade" id="modalEditarGestor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Gestor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarGestor" onsubmit="actualizarGestor(event)">
                        <input type="hidden" id="id_gestor" name="id">
                        <div class="mb-3">
                            <label for="rut_gestor" class="form-label">RUT</label>
                            <input type="text" class="form-control" id="rut_gestor" name="rut" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_gestor" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre_gestor" name="nombre_completo" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_nacimiento_gestor" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento_gestor" name="fecha_nacimiento" max="" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo_gestor" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="correo_gestor" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="sexo_gestor" class="form-label">Sexo</label>
                            <select class="form-control" id="sexo_gestor" name="sexo" required>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="O">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="telefono_gestor" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono_gestor" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado_gestor" class="form-label">Estado</label>
                            <select class="form-control" id="estado_gestor" name="estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Propiedad -->
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="js/usuarios.js"></script>
    <script src="js/propietarios.js"></script>
    <script src="js/gestores.js"></script>
    <script>
        function toggleForm(formId) {
            const form = document.getElementById(formId);
            form.classList.toggle('expanded');
        }

        // Función para validar RUT chileno
        function validarRut(rut) {
            if (!/^[0-9]+-[0-9kK]{1}$/.test(rut)) return false;
            var tmp = rut.split('-');
            var digv = tmp[1].toLowerCase();
            var rut = tmp[0];
            if (digv == 'k') digv = 'k';
            return (dv(rut) == digv);
        }

        function dv(T) {
            var M = 0, S = 1;
            for (; T; T = Math.floor(T/10))
                S = (S + T % 10 * (9 - M++ % 6)) % 11;
            return S ? S - 1 : 'k';
        }

        // Función para validar contraseña
        function validarPassword(password) {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.{8,})/;
            return regex.test(password);
        }

        // Función para validar email
        function validarEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        // Función para validar teléfono
        function validarTelefono(telefono) {
            const regex = /^\+569\d{8}$/;
            return regex.test(telefono);
        }

        // Función para validar fecha de nacimiento
        function validarFechaNacimiento(fecha) {
            const fechaSeleccionada = new Date(fecha);
            const hoy = new Date();
            return fechaSeleccionada <= hoy;
        }

        // Establecer la fecha máxima como hoy
        document.addEventListener('DOMContentLoaded', function() {
            const fechaInput = document.getElementById('fecha');
            const hoy = new Date();
            const formatoFecha = hoy.toISOString().split('T')[0];
            fechaInput.max = formatoFecha;
        });

        // Función principal de validación para gestor
        function validarFormularioGestor(event) {
            event.preventDefault();
            
            const run = document.getElementById('run').value;
            const nombre = document.getElementById('nombre').value;
            const fecha = document.getElementById('fecha').value;
            const correo = document.getElementById('correo').value;
            const password = document.getElementById('password').value;
            const password2 = document.getElementById('password2').value;
            const genero = document.getElementById('genero').value;
            const telefono = document.getElementById('telefono').value;
            const certificado = document.getElementById('certificado').value;

            // Validar campos vacíos
            if (!run || !nombre || !fecha || !correo || !password || !password2 || !genero || !telefono || !certificado) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Todos los campos son obligatorios'
                });
                return false;
            }

            // Validar RUT
            if (!validarRut(run)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El RUT ingresado no es válido'
                });
                return false;
            }

            // Validar fecha de nacimiento
            if (!validarFechaNacimiento(fecha)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La fecha de nacimiento no puede ser una fecha futura'
                });
                return false;
            }

            // Validar email
            if (!validarEmail(correo)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El correo electrónico no es válido'
                });
                return false;
            }

            // Validar contraseña
            if (!validarPassword(password)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un carácter especial'
                });
                return false;
            }

            // Validar que las contraseñas coincidan
            if (password !== password2) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden'
                });
                return false;
            }

            // Validar teléfono
            if (!validarTelefono(telefono)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El teléfono debe tener el formato +569XXXXXXXX'
                });
                return false;
            }

            // Validar extensión del certificado
            const extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
            const extension = certificado.split('.').pop().toLowerCase();
            if (!extensionesPermitidas.includes(extension)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El certificado debe ser un archivo PDF, JPG, JPEG o PNG'
                });
                return false;
            }

            // Si todo está correcto, enviar el formulario
            const formData = new FormData(document.getElementById('formGestor'));
            
            fetch('procesar_gestor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Gestor registrado correctamente'
                    }).then(() => {
                        document.getElementById('formGestor').reset();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al registrar el gestor'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la solicitud'
                });
            });

            return false;
        }

        // Función para cargar las regiones
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

        // Función para cargar provincias y luego comunas y luego sectores en cascada (modal edición)
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
                    console.log('Cargar provincias para region:', data.region);
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
            })
            .catch(error => {
                console.error('Error al cargar la ubicación:', error);
            });
        }

        // Modificar cargarProvincias, cargarComunas y cargarSectores para aceptar callback
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

        // Manejar el envío del formulario
        document.getElementById('registroPropiedadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('procesar_propiedad.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.message,
                        confirmButtonColor: '#7e57c2'
                    }).then(() => {
                        this.reset();
                        document.getElementById('propiedadForm').classList.remove('expanded');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || data.error || 'Ocurrió un error al procesar la solicitud',
                        confirmButtonColor: '#7e57c2'
                    });
                }
            })
            .catch(async (response) => {
                let errorMsg = 'Ocurrió un error al procesar la solicitud';
                if (response && response.text) {
                    const text = await response.text();
                    try {
                        const json = JSON.parse(text);
                        if (json && json.message) errorMsg = json.message;
                        if (json && json.error) errorMsg = json.error;
                    } catch {}
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg,
                    confirmButtonColor: '#7e57c2'
                });
            });
        });

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

                    // Llenar regiones en el select correcto antes de cargar la ubicación
                    cargarRegiones('edit_region');
                    setTimeout(function() {
                        cargarUbicacion(prop.idsectores);
                    }, 300);
                    
                    var modal = new bootstrap.Modal(document.getElementById('modalEditarPropiedad'));
                    modal.show();
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
                    text: 'Error al obtener los datos de la propiedad',
                    confirmButtonColor: '#7c3aed'
                });
            });
        }

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
                            Swal.fire({ 
                                icon: 'success', 
                                title: '¡Eliminado!', 
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
                            text: 'Error al eliminar la propiedad',
                            confirmButtonColor: '#7c3aed'
                        });
                    });
                }
            });
        }

        // Cargar regiones al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarRegiones();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var formEditarPropiedad = document.getElementById('formEditarPropiedad');
            if (formEditarPropiedad) {
                formEditarPropiedad.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(formEditarPropiedad);
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
                                confirmButtonColor: '#7e57c2'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Ocurrió un error al actualizar la propiedad',
                                confirmButtonColor: '#7e57c2'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al actualizar la propiedad',
                            confirmButtonColor: '#7e57c2'
                        });
                    });
                });
            }
        });
    </script>
</body>
</html>

<?php
} else {
    header("Location: error.html");
}
?>
