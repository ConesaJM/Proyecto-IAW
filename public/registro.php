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

            // Opcional: vaciar los campos del formulario
            $nombre = '';
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
        <div class="brand-section">
            <div class="logo-container">
                <img src="media/pharmasphere_sinfondo.png" alt="Logo PharmaSphere">
            </div>
            <h1 class="brand-title">PharmaSphere</h1>
            <p class="brand-subtitle">Sistema de Gestión Farmacéutica</p><br><br>
        <div class="form-section">
             <h2>Crear cuenta</h2>

             <?php if ($error): ?>
                 <div class="error"><?= h($error) ?></div>
            <?php endif; ?>

            <?php if ($msg): ?>
                <div class="ok"><?= h($msg) ?></div>
            <?php endif; ?>

            <form method="post">
                <label for="nombre">Nombre de usuario</label><br>
                <input type="text" id="nombre" name="nombre" value="<?= h($nombre ?? '') ?>"><br><br>

                <label for="password">Contraseña</label><br>
                <div style="position:relative; display:inline-block;">
                    <input type="password" id="password" name="password">
                    <i class="fa-solid fa-eye" id="togglePassword"
                       style="position:absolute; right:8px; top:50%; transform:translateY(-50%); cursor:pointer;"></i>
                </div>
                <br><br>

                <button type="submit">Crear usuario</button>
            </form>

            <br>
            <a href="login.php">Volver al login</a>
        </div>

    </div>

        <!-- Script para ver/ocultar contraseña -->
      <script> 
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('password');

      togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Cambia el icono
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
      });
    </script>
</body>
</html>