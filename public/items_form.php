<?php 

// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º:  INICIA SESION)
require_once __DIR__ . '/../app/pdo.php';   // (2º: CONEXION DB)
require_once __DIR__ . '/../app/utils.php'; // (3º: CARGAR FUNCIONES)

//FUNCION DE auth.php QUE PROTEGE LA PÁG
// ONLY EL ADMIN ACCEDE
require_admin();

// 2. DEFINIR MODO EDICION Y DATOS INICIALES
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


//LISTAR MARCAS

$marcas = listarMarcas($pdo);

// 3. VERIFICAR SI ESTAMOS EN MODO EDICIÓN (leyendo la URL)
$producto_id = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);

if ($producto_id) {
    // SI HAY ID, intentamos cargar el producto
    $producto_carga = leerProductoPorId($pdo, $producto_id); 
    
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

if ($_SERVER ['REQUEST_METHOD'] === 'POST'){
    // CUANDO LE DAN A "GUARDAR CAMBIOS" SE REALIZA ESTE PROCESO

    $id = $_POST['ID'] ??  null;
    $nombre = trim($_POST['NOMBRE'] ?? '');
    $activo = trim($_POST['ACTIVO'] ??'');
    $receta = isset($_POST['RECETA']);
    $precio = (float) ($_POST['PRECIO'] ?? 0.00);
    $stock = (int)($_POST['STOCK_DISPONIBLE'] ?? 0);
    $marca_id = (int)($_POST['MARCA_ID'] ?? 0);

    // VALIDACIONES PRODUCTOS
    if (empty($receta)) {
        $receta = 0;
    }
    // VALIDACION DATOS METIDOS

    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    }

    if (empty($marca_id)) {
        $errores[] = "Debe seleccionar una marca.";
    }

    if ($precio < 0) {
        $errores[] = "El precio no puede ser negativo.";
    }

    if ($stock < 0) {
        $errores[] = "El stock no puede ser negativo.";
    }

    // SE GUARDA SI NO HAY ERROR

    if (empty($errores)) {
        try {
            if ($id) {

            // MODO UPDATE 
            // ACTUALIZAR SI HAY ID

                actualizarProducto($pdo, $id, $nombre, $activo, $receta, $precio, $stock, $marca_id); //

            } else {

            // MODO CREATE 
            // CREAR PRODUCTO SI NO HAY ID

              crearProducto($pdo, $nombre, $activo, $receta, $precio, $stock, $marca_id);
            }

            // RECARGA PAG (PRG)
            header('Location: items_list.php?exito=guardado');
            exit;
            
        }   catch (PDOException $e) {
            $errores[] = "Error al guardar en la base de datos: " . $e->getMessage();
            }

    }




    // VALIDACION ERROR PRODUCTO
    // SI ERROR PRODUCTO ENTONCES NO SE ENVIA

    $producto = [
        'ID' => $id,
        'NOMBRE' => $nombre,
        'ACTIVO' => $activo,
        'RECETA' => $receta,
        'PRECIO' => $precio,
        'STOCK_DISPONIBLE' => $stock,
        'MARCA_ID' => $marca_id
    ];

}
// 5. MOSTRAR LA VISTA
// El título cambia si estamos editando o creando
$titulo_pagina = $modo_edicion ? "Editar Producto: " . h($producto['NOMBRE']) : "Crear Nuevo Producto";
headerHtml($titulo_pagina); 
?>

<form method="post" action="">
    
    <?php if ($modo_edicion): ?>
        <input type='hidden' name='ID' value='<?php echo h($producto['ID']); ?>'>
    <?php endif; ?>

    <p>
        <label>Nombre:</label>
        <input type='text' name='NOMBRE' value='<?php echo h($producto['NOMBRE']); ?>' required>
    </p>

    <p>
        <label>Categoría (Activo):</label>
        <select name="ACTIVO">
            <?php 
            // Lista de categorías de tu ENUM
            $categorias = ['Medicamento', 'Antibiótico','Cuidado personal','Vitaminas','Otros'];
            foreach ($categorias as $cat):
                // Marcamos como 'selected' la categoría del producto
                $selected = ($cat === $producto['ACTIVO']) ? 'selected' : '';
                echo "<option value='" . h($cat) . "' $selected>" . h($cat) . "</option>";
            endforeach;
            ?>
        </select>
    </p>
    
    <p>
        <label>Marca:</label>
        <select name="MARCA_ID">
            <option value="">-- Seleccione una marca --</option>
            <?php foreach ($marcas as $marca):
                // Marcamos como 'selected' la marca del producto
                $selected = ($marca['ID'] == $producto['MARCA_ID']) ? 'selected' : '';
                echo "<option value='" . h($marca['ID']) . "' $selected>" . h($marca['NOMBRE']) . "</option>";
            endforeach;
            ?>
        </select>
    </p>

    <p>
        <label>Precio (€):</label>
        <input type='number' step='0.01' name='PRECIO' value='<?php echo h($producto['PRECIO']); ?>' required>
    </p>

    <p>
        <label>Stock Disponible:</label>
        <input type='number' name='STOCK_DISPONIBLE' value='<?php echo h($producto['STOCK_DISPONIBLE']); ?>' required>
    </p>

    <p>
       <label>
            <input type="checkbox" name="RECETA" value='' <?php if ($producto['RECETA']) echo 'checked'; ?>>
            ¿Necesita receta?
        </label>
    </p>

    <p>
        <button type='submit'>Guardar Cambios</button>
        <a href='items_list.php'>Cancelar</a>
    </p>
</form>

<?php
footerHtml();
?>