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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($user === '' || $password === '') {
        $error = 'Por favor, rellene todos los campos.';
    } else {
        $u = buscarUsuarioPorNombre($pdo, $user);
      
        // validar contraseña
        if ($u && (
            password_verify($password, $u['CONTRASENHIA']) || 
            $password === $u['CONTRASENHIA']                  
        )) {
                 //mantener sesión
            $_SESSION['user_id'] = $u['ID'];
            $_SESSION['user_rol'] = $u['ROL'];
            $_SESSION['user_nombre_usuario'] = $u['NOMBRE'];

            //redirección a index
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaSphere - Inicio de Sesión</title>
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
            <p class="brand-subtitle">Sistema de Gestión Farmacéutica</p>
        </div>

        <div class="login-card">
            <div class="card-header">
                <h2 class="card-title">Iniciar Sesión</h2>
                <p class="card-subtitle">Accede a tu cuenta</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-banner">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="form-group">
                    <label for="user">Usuario</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user input-icon-left"></i>
                        <input type="text" id="user" name="user" class="form-input" 
                               value="<?= htmlspecialchars($_POST['user'] ?? '') ?>" 
                               placeholder="Ej. admin" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock input-icon-left"></i>
                        <input type="password" id="password" name="password" class="form-input" 
                               placeholder="••••••••" required>
                        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
                </button>
            </form>
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