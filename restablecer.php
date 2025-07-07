<?php
session_start();
include 'setup/conexion.php';

// Verificar si hay un token válido
if (!isset($_GET['token'])) {
    header('Location: index.php');
    exit;
}

$token = $_GET['token'];
$sql = "SELECT * FROM tokens_recuperacion WHERE token = ? AND usado = 0 AND fecha_expiracion > NOW()";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$token_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="css/recuperar.css">
</head>
<body>
    <header class="header">
        <div class="header-izquierda">
            <img src="img/Logo.png?v=1" alt="Logo PNK" class="logo">
            <div class="titulo">PNK INMOBILIARIA</div>
        </div>
    </header>
    <main class="main">
        <div class="recuperar-contenedor">
            <h2>Restablecer Contraseña</h2>
            <div id="mensaje" class="mensaje" style="display: none;"></div>
            <form id="formRestablecer" onsubmit="return false;">
                <input type="hidden" id="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="input-group">
                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" name="password" id="password" required minlength="6">
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirmar Contraseña:</label>
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="6">
                </div>
                <button type="submit" onclick="restablecerPassword()">Restablecer Contraseña</button>
                <button type="button"><a href="index.php">Volver</a></button>
            </form>
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
            <a href="https://www.instagram.com/tioreneoficial_/?hl=es" target="_blank">
                <img src="img/logo-insta.png?v=1" alt="Logo instagram" class="logo-footer">
            </a>
            <a href="https://www.ticketmaster.cl/event/popin-un-show-muy-penca-centro-cultural-san-gines" target="_blank">
                <img src="img/linkedin.png?v=1" alt="Logo Linkedin" class="logo-footer">
            </a>
        </nav>
    </footer>

    <script>
        function mostrarMensaje(mensaje, tipo) {
            const mensajeDiv = document.getElementById('mensaje');
            mensajeDiv.textContent = mensaje;
            mensajeDiv.className = 'mensaje ' + tipo;
            mensajeDiv.style.display = 'block';
        }

        function restablecerPassword() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const token = document.getElementById('token').value;

            if (password !== confirmPassword) {
                mostrarMensaje('Las contraseñas no coinciden', 'error');
                return;
            }

            if (password.length < 6) {
                mostrarMensaje('La contraseña debe tener al menos 6 caracteres', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('token', token);
            formData.append('password', password);

            fetch('procesar_restablecer.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    mostrarMensaje(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                mostrarMensaje('Error al procesar la solicitud', 'error');
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html> 