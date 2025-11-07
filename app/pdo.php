<?php
// Credenciales admin
$host = "localhost";
$db   = "supermercado";
$user = "admin";
$pass = "Admin_IAW_super";
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

// Credenciales usuario
$user2 = "usuario";
$pass2 = "Usuario_IAW_super";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    echo "Conexión admin OK<br>";
} catch (PDOException $e) {
    die("Error de conexión BD (admin): " . $e->getMessage());
}

try {
    $pdo2 = new PDO($dsn, $user2, $pass2, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    echo "Conexión usuario OK<br>";
} catch (PDOException $e) {
    die("Error de conexión BD (usuario): " . $e->getMessage());
}
?>