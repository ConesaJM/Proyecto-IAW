<?php 

// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/utils.php'; // (3º: Carga nuestras funciones)

// VARIABLES


$producto_carga = leerProductoPorId($pdo,$producto_id);

$producto = $producto_carga;
$modo_edicion = true;

// $marcas = 'listar_marcas'($pdo); ---> items_list

// FORMULARIO


require_login();  


// 3. MOSTRAR LA PÁGINA
// Si el script llega aquí, el usuario SÍ está logueado.
// Con la función headerHtml() mostramos el HTML creado dentro de utils.php
headerHtml('Panel Principal - Pharmasphere');




// 5. CIERRE DE LA PÁGINA
// Llamamos a la otra función para cerrar </body></html>
// También dentro de utils.php
// 
footerHtml(); 


// VALIDACION ESTADO Y ERRORES

$errores= [];
$modo_edicion = false;
$producto = [
    'ID' => null,
    'NOMBRE' => '',
    'ACTIVO' => false,
    'RECETA' => false,
    'PRECIO' => 0.0,
    'STOCK_DISPONIBLE' => 0,
    'MARCA_ID' => null
];

// VERIFICACION MODO EDICION

$producto_id = filter_input(INPUT_GET, 'ID',FILTER_VALIDATE_INT);
if ($producto_id) {
    // SI HAY ID ESTAMOS EN EDICION
    $producto_carga = leerProductoPorId($pdo,$producto_id);
     // SI NO HAY, REDIRIGE
    if (!$producto_carga){
        header ('Location: index.php?error=no_existe');
        exit;
    }

}




?>