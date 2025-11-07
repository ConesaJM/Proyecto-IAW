<?php
// Si ya tengo cookies, 
if (
    isset($_COOKIE['name']) &&
    isset($_COOKIE['password'])
) {
    header("");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // validación campos vacíos
    if (empty($user) || empty($password)) {
        $error = "Por favor, rellene todos los campos.";
    } else {
        // validación de datos incorrectos
        $error = "Usuario o contraseña incorrectos";
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

      <title>Inicio de sesión</title>
  </head>

  <!--Formulario -->
  <body>
      <h1>Introduzca sus credenciales</h1><br><br>

      <form action="" method="post">
        <?php if (!empty($error)): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <label for="user">Usuario</label>
        <input type="text" id="user" name="user"
          value="<?= htmlspecialchars($_POST['user'] ?? '') ?>" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password"
          value="<?= htmlspecialchars($_POST['password'] ?? '') ?>" required>
        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i><br><br>

        <button type="submit">Iniciar sesión</button>
    </form>

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