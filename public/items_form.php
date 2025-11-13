<?php 

// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/utils.php'; // (3º: Carga nuestras funciones)

// $marcas = 'listar_marcas'($pdo); ---> items_list 

require_admin();

// VALIDACION ESTADO Y ERRORES

$errores= [];
$modo_edicion = false;
$producto = [
    'ID' => null,
    'NOMBRE' => '',
    'ACTIVO' => '',
    'RECETA' => false,
    'PRECIO' => 0.0,
    'STOCK_DISPONIBLE' => 0,
    'MARCA_ID' => null
];

$marcas = listarMarcas($pdo);

// 3. VERIFICAR SI ESTAMOS EN MODO EDICIÓN (leyendo la URL)
$producto_id = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);

if ($producto_id) {
    // SI HAY ID, intentamos cargar el producto
    $producto_carga = leerProductoPorId($pdo, $producto_id); //
    
    if ($producto_carga){
        // Si lo encontramos, cambiamos a modo edición
        $producto = $producto_carga;
        $modo_edicion = true;
    } else {
        // Si el ID es inválido, redirigimos
        header ('Location: items_list.php?error=no_existe');
        exit;
    }
}

// 4. (LÓGICA DE GUARDADO - POST)
// ... (Esto lo haremos más adelante) ...

// 5. MOSTRAR LA VISTA
// El título cambia si estamos editando o creando
$titulo_pagina = $modo_edicion ? "Editar Producto: " . h($producto['NOMBRE']) : "Crear Nuevo Producto";
headerHtml($titulo_pagina); 
?>


<?php
footerHtml();
?>