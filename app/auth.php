<?php
// app/auth.php

// Esto debe ser lo primero que se llama en cualquier página.
// Inicia la sesión de PHP para el login o reanuda la que ya existía.
session_start();

// FUNCIÓN PRINCIPAL: require_login()
// Se llamará al principio de cada página para asegurar que el usuario está logueado.
// (index.php, items_list.php, items_form.php, etc.).
function require_login() {
    // Comprueba si la variable 'user_id' no existe en la sesión
    // (Esta variable se creará en 'login.php' cuando el login sea exitoso)
    if (!isset($_SESSION['user_id'])) {
        
        // Si no existe un ID, el usuario no está logueado.
        // Lo redirigimos a la página de login.
        header('Location: login.php');
        
        // Detenemos la ejecución del script actual
        // para que no muestre nada de la página protegida.
        exit;
    }
}