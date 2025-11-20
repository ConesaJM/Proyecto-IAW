<?php
require_once __DIR__ . '/../app/auth.php';

// Vaciar variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Borrar cookie de sesión
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Redirección a login
header('Location: login.php');
exit;
