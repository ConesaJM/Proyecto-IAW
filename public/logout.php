<?php
require_once __DIR__ . '/../app/auth.php';

// Vaciar variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Redirección a login
header('Location: login.php');
exit;
