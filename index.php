<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNK INMOBILIARIA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/inicio.css">
    <link rel="icon" type="image/x-icon" href="img/favicon-16x16.png">
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/filtros.js"></script>
    <script>
        function enviar()
        {
            if(document.forms.form.usuario.value=="")
        {
            alert("Debe ingresar un usuario");
            document.forms.form.usuario.focus();
            return false;
        }else{
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(document.forms.form.usuario.value)) {
                alert("Debe Ingresar un email válido");
                return false;
            }
        }
        if(document.forms.form.password.value=="")
        {
            alert("Debe ingresar una Contraseña");
            document.forms.form.password.focus();
            return false;
        }else{
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordRegex.test(document.forms.form.password.value)) {
                alert("Debe Ingresar una contraseña válida");
                return false;
            }
        }
        document.forms.form.submit();
        }
    </script>
</head>
<body>
    <header class="header">
        <div class="header-izquierda">
            <img src="img/Logo.png?v=1" alt="Logo PNK" class="logo">
            <div class="titulo">PNK INMOBILIARIA</div>
        </div>

        <nav class="header-derecha">
            <?php
            session_start();
            if (isset($_SESSION['usuario'])) {
                $nombre = isset($_SESSION['nombre']) && $_SESSION['nombre'] ? htmlspecialchars($_SESSION['nombre']) : htmlspecialchars($_SESSION['usuario']);
                echo '<span class="bienvenida">Bienvenido, ' . $nombre . '</span>';
                if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'propietario') {
                    echo '<a href="dashboard_propietario.php" class="header-botones">Mi Panel</a>';
                }
                echo '<a href="cerrar_sesion.php" class="header-botones">Cerrar Sesión</a>';
            } else {
                echo '<a href="registro_propietario.html" class="header-botones">Crear Propietario</a>';
                echo '<a href="registro_gestor.html" class="header-botones">Crear Gestor</a>';
            }
            ?>
        </nav>

    </header>

    <main class="main">
        <?php if (!isset($_SESSION['usuario'])) { ?>
        <div class="login-contenedor">
            <h2>Autenticación</h2>
            <img src="img/key.png?v=1" alt="imagen" class="img-login">
            <form action="procesa.php"  name="form" method="post">
                <div class="input-group">
                    <label for="fname">Usuario:</label>
                    <input type="email" name="usuario" id="fname" placeholder="ingrese email" required>
                </div>
                <div class="input-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" name="password" id="password" placeholder="ingrese nombre de Contraseña" required>
                </div>
                <button type="button" onclick="enviar();">Ingresar</button>
            </form>            
            <br>
            <div><a href="recuperar.html" class="recuperar">Recuperar Contraseña</a></div>
        </div>
        <br>
        <?php } ?>
        <div class="container">
            <div class="caja-buscador">
                <h2 class="titulo-buscador mb-4">Buscador de Propiedades</h2>
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="filtro-grupo">
                            <label for="tipo_propiedad" class="form-label">Tipo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-house"></i></span>
                                <select id="tipo_propiedad" name="tipo_propiedad" class="form-select">
                                    <option value="">Seleccione tipo</option>
                                    <option value="1">Casas</option>
                                    <option value="2">Departamentos</option>
                                    <option value="3">Terrenos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="filtro-grupo">
                            <label for="region" class="form-label">Región</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <select id="region" name="region" class="form-select">
                                    <option value="">Seleccione región</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="filtro-grupo">
                            <label for="provincia" class="form-label">Provincia</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                <select id="provincia" name="provincia" class="form-select">
                                    <option value="">Seleccione provincia</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="filtro-grupo">
                            <label for="comuna" class="form-label">Comuna</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                <select id="comuna" name="comuna" class="form-select">
                                    <option value="">Seleccione comuna</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="filtro-grupo">
                            <label for="sector" class="form-label">Sector</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-pin-map"></i></span>
                                <select id="sector" name="sector" class="form-select">
                                    <option value="">Seleccione sector</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center mt-4">
                        <!-- Botón de buscar propiedades eliminado -->
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="galeria">
            <?php
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            include("setup/conexion.php");

            $sql = "SELECT 
                    p.num_propiedad,
                    p.estado,
                    p.titulopropiedad AS titulo,
                    p.precio_pesos,
                    p.precio_uf,
                    g.foto
                    FROM 
                    propiedades p
                    JOIN 
                    galeria g ON p.num_propiedad = g.idpropiedades
                    WHERE 
                    g.principal = 1 AND p.estado = 1";

            $result = mysqli_query(conectar(), $sql);
            if (!$result) {
                die("Error en la consulta: " . mysqli_error(conectar()));
            }

            while($datos = mysqli_fetch_array($result)) {
                $fmt = numfmt_create('es_CL', NumberFormatter::CURRENCY);
                $precio = numfmt_format_currency($fmt, $datos['precio_pesos'], "CLP");
            ?>
                <div class="propiedad">
                    <img src="<?php echo $datos['foto']; ?>" alt="<?php echo $datos['titulo']; ?>">
                    <div class="info-propiedad">
                        <h3><?php echo $datos['titulo']; ?></h3>
                        <div class="precios">
                            <span class="uf">UF <?php echo number_format($datos['precio_uf'], 0, ',', '.'); ?></span>
                            <span class="clp"><?php echo $precio; ?></span>
                            <a href="vermas.php?idpro=<?php echo $datos['num_propiedad']; ?>" class="btn-ver-mas">Ver más</a>
                        </div>
                    </div>
                </div>
            <?php    
            }
            ?>
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

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
