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
            <input type="checkbox" name="RECETA" value="1" <?php if ($producto['RECETA']) echo 'checked'; ?>>
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