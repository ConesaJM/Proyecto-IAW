<?php
// Si ya tengo cookies, 
if (
    isset($_COOKIE['name']) &&
    isset($_COOKIE['password'])
) {
    header("");
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio de sesión</title>
</head>

<!--Formulario -->
<body>
    <h1>Introduzca sus credenciales</h1><br><br>

    <form action="" method="post">
        <label for="user">Usuario</label>
        <input type="text" name="user" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>
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