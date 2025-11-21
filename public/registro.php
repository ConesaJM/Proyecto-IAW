<?php
// llamadas a auth, pdo y utils
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/utils.php';

//Si el usuario ya hizo login, redirige a index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos lo que ha escrito el usuario
    $nombre   = trim($_POST['nombre'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Comprobar campos vacíos
    if ($nombre === '' || $password === '') {
        $error = 'Por favor, rellene todos los campos.';
    } else {
        // Comprobar si ya existe un usuario con ese nombre
        $stmt = $pdo->prepare("SELECT ID FROM USUARIO WHERE NOMBRE = ?");
        $stmt->execute([$nombre]);
        $existe = $stmt->fetch();

        if ($existe) {
            $error = 'Ese nombre de usuario ya existe.';
        } else {
            // Guardar contraseña cifrada en la BD
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO USUARIO (NOMBRE, CONTRASENHIA) VALUES (?, ?)"
            );
            $stmt->execute([$nombre, $hash]);

            // Mensaje de éxito
            $msg = 'Usuario creado correctamente. Ya puede iniciar sesión.';

            // Después de insertar
            $stmt->execute([$nombre, $hash]);

            header('Location: login.php?registro=ok');
            exit;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaSphere - Registro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="media/pharmasphere_sinfondo.png" type="image/png" sizes="512x512">
</head>
<body>

    <div class="login-wrapper">
        <!-- Zona del logo y título, igual que en el login -->
        <div class="brand-section">
            <div class="logo-container">
                <img src="media/pharmasphere_sinfondo.png" alt="Logo PharmaSphere">
            </div>
            <h1 class="brand-title">PharmaSphere</h1>
            <p class="brand-subtitle">Sistema de Gestión Farmacéutica</p>
        </div>

        <!-- Tarjeta del formulario (usa .login-card) -->
        <div class="login-card">
            <div class="card-header">
                <h2 class="card-title">Crear cuenta</h2>
                <p class="card-subtitle">Regístrese para acceder al panel</p>
            </div>

            <!-- Mensajes de error / éxito -->
            <?php if ($error): ?>
                <div class="error-banner">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= h($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($msg): ?>
                <div class="error-banner" style="background:#dcfce7;border-color:#bbf7d0;color:#166534;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= h($msg) ?></span>
                </div>
            <?php endif; ?>

            <form method="post">
                <!-- Nombre de usuario -->
                <div class="form-group">
                    <label for="nombre">Nombre de usuario</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user input-icon-left"></i>
                        <input type="text" id="nombre" name="nombre" class="form-input" value="<?= h($nombre ?? '') ?>">
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock input-icon-left"></i>
                        <input type="password" id="password" name="password" class="form-input">
                        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <!-- Botón crear usuario -->
                <button type="submit" class="btn-submit">
                    <span>Crear usuario</span>
                </button>
            </form>

            <p style="margin-top:1rem; font-size:0.9rem; text-align:center;">
                ¿Ya tiene cuenta?
                <a href="login.php">Iniciar sesión</a>
            </p>
        </div>
    </div>

    <!-- Script ver/ocultar contraseña -->
    <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput  = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password'
                ? 'text'
                : 'password';
            passwordInput.setAttribute('type', type);

            togglePassword.classList.toggle('fa-eye');
            togglePassword.classList.toggle('fa-eye-slash');
        });
    }
    </script>
</body>
</html>