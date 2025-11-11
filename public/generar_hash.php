<?php
// public/_generar_hash.php
// ATENCION: Este archivo NO debe estar accesible públicamente en un entorno de producción.
// Solo debe usarse en desarrollo para generar el hash de la contraseña del usuario admin
// Tras generar el hash, elimina este archivo por seguridad.

// Se introduce la contraseña que se quiere para el usuario admin 
$contrasena_plana = 'Admin_IAW_super';

// Generamos el hash
$hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);

echo "<h1>Generador de Hash para el usuario admin</h1>";
echo "<p>Contraseña: " . htmlspecialchars($contrasena_plana) . "</p>";
echo "<p><strong>Copia este hash y pégalo en el archivo 'seed.sql':</strong></p>";
echo '<textarea style="width:100%; height: 60px;">' . $hash . '</textarea>';
?>