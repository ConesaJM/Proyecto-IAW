<?php

// llamadas a auth, pdo y utils
 require_once __DIR__ . '/../app/auth.php';
 require_once __DIR__ . '/../app/pdo.php';
 require_once __DIR__ . '/../app/utils.php';

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
            (isset($u['password_hash']) && password_verify($password, $u['password_hash'])) ||
            (isset($u['password']) && $u['password'] === $password)
        )) {           //mantener sesión
            $_SESSION['user_id'] = $u['ID'];
            $_SESSION['user_rol'] = $u['ROL'];
            $_SESSION['user_nombre_usuario'] = $u['NOMBRE'];

            //redirección a index
            header('Location: ./index.php');
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
      <link rel="stylesheet"  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
      <link rel="stylesheet" href="style.css">
      <link rel="icon" href="media/pharmasphere.png" type="image/png" sizes="512x512">
      <title>Inicio de sesión</title> 
  </head>

  <!--Formulario -->
  <body>
      <h1>Bienvenido a</h1> 
      
      <img src="media/pharmasphere.png">

      <h2>Introduzca sus credenciales</h2><br><br>

      <form action="" method="post">
        <?php if (!empty($error)): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <label for="user">Usuario</label>
        <input type="text" id="user" name="user"
          value="<?= htmlspecialchars($_POST['user'] ?? '') ?>" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" value="" required autocomplete="current-password">
        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i><br><br>

        <button type="submit">Iniciar sesión</button>
    </form>

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