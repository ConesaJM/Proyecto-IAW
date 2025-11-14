<?php 

require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/utils.php'; // (3º: Carga nuestras funciones)


// SOLO EL ADMIN ACCEDE
require_admin();

// VARIABLES NECESARIAS 
$errores = [];
$producto_id_get = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
$producto_id_post = $_POST['ID'] ?? null;
$producto_id = $producto_id_get ?: $producto_id_post;


// 2. LÓGICA DE BORRADO (POST)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CONFIRMA BORRAR
    
    if ($producto_id_post) {
        try {
          
            borrarProducto($pdo, $producto_id_post);
            
            // REDIRIGIR PRG
            header('Location: items_list.php?exito=borrado');
            exit;

        } catch (PDOException $e) {
            // MENSAJE ERROR PRODUCTO ESTA
            $errores[] = "Error al borrar el producto. Es posible que esté asociado a otros registros. (" . $e->getMessage() . ")";

        }
    } else {
        // REDIRIGE SI NO HAY ID
        header('Location: items_list.php?error=generico');
        exit;
    }
}

// 3. LÓGICA DE CARGA (GET o si hay error POST)


if (!$producto_id) {
    // SI NO HAY ID
    header('Location: items_list.php?error=no_id');
    exit;
}

// CARGAR PRODUCTO
$producto = leerProductoPorId($pdo, $producto_id);

if (!$producto) {
    // REDIRIGIR ID INVALIDO
    header('Location: items_list.php?error=no_existe');
    exit;
}


$marcas = listarMarcas($pdo);


// 4. MOSTRAR LA VISTA
$titulo_pagina = "Confirmar Borrado: " . h($producto['NOMBRE']);
headerHtml($titulo_pagina);
?>

