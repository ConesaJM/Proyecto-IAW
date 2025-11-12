<?php 

require_once 'utils.php';

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
$producto = $producto_carga;
$modo_edicion = true;


}

$marcas = 'listar_marcas'($pdo);


?>