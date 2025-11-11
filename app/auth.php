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
        header('Location: public/login.php');
        
        // Detenemos la ejecución del script actual
        // para que no muestre nada de la página protegida.
        exit;
    }
}

// FUNCIÓN ADMIN : require_admin()
// Esta función unicamente se llamará en páginas que SÓLO los administradores pueden usar.
function require_admin() {
    // 1. Primero, nos aseguramos de que el usuario esté logueado (sea quien sea)
    require_login();

    // 2. Si está logueado, comprobamos su rol.
    // Esta variable "user_rol" también se creará en "login.php")
    if ($_SESSION['user_rol'] != 'admin') {
        
        // Si no es admin, lo echamos al panel principal.
        // No debería estar aquí.
        header('Location: public/index.php');
        exit;
    }
}