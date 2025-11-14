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
            
           
            //MUESTRA MENSAJE EN LA PAGINA
            
            $titulo_pagina = "Producto Borrado";
            headerHtml($titulo_pagina); // Carga el <head> y el CSS

            
            echo '<meta http-equiv="refresh" content="3;url=items_list.php">';

            // MENSAJE EXITO
            
            echo "<div class='success' style='text-align: center; padding: 20px;'>";
            echo "<h2><i class='fa-solid fa-check-circle'></i> ¡Borrado con éxito!</h2>";
            echo "<p>El producto ha sido eliminado permanentemente.</p>";
            echo "<p>Serás redirigido al listado en 3 segundos...</p>";
            echo "<hr>";
            echo "<a href='items_list.php'>Volver al listado ahora</a>";
            echo "</div>";

            footerHtml();
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


<?php
// MOSTRAR ERROR
if (!empty($errores)):
    echo "<div class'error'><ul>";
    foreach ($errores as $error) {
        echo "<li>" . h($error) . "</li>";
    }
    echo "</ul></div>";
endif;
?>

<div class='error' style="background-color: #fdd; border-color: var(--color-peligro);">
    <h2><i class="fa-solid fa-triangle-exclamation"></i> ¡Atención!</h2>
    <p>Estás a punto de borrar permanentemente el siguiente producto...
        <strong>¡¡Esta acción no se puede deshacer!!.</strong></p>
    <p>¿Estás seguro de que quieres continuar?</p>
</div>


<form method="post" action="items_delete.php">
    
    <input type='hidden' name='ID' value='<?php echo h($producto['ID']); ?>'>

    <p>
        <label>Nombre:</label>
        <input type='text' value='<?php echo h($producto['NOMBRE']); ?>' disabled>
    </p>

    <p>
        <label>Categoría:</label>
        <select disabled>
            <?php
            //  ENUM
            $categorias = ['Medicamento', 'Antibiótico','Cuidado personal','Vitaminas','Otros'];
            foreach ($categorias as $cat):
                // SELECCION ACTIVO
                $selected = ($cat === $producto['ACTIVO']) ? 'selected' : '';
                echo "<option value='" . h($cat) . "' $selected>" . h($cat) . "</option>";
            endforeach;
            ?>
        </select>
    </p>
    
    <p>
        <label>Marca:</label>
        <select disabled>
            <option value="">-- Seleccione una marca --</option>
            <?php foreach ($marcas as $marca):
                // SELECT MARCA
                $selected = ($marca['ID'] == $producto['MARCA_ID']) ? 'selected' : '';
                echo "<option value='" . h($marca['ID']) . "' $selected>" . h($marca['NOMBRE']) . "</option>";
            endforeach;
            ?>
        </select>
    </p>

    <p>
        <label>Precio (€):</label>
        <input type='number' value='<?php echo h($producto['PRECIO']); ?>' disabled>
    </p>

    <p>
        <label>Stock Disponible:</label>
        <input type='number' value='<?php echo h($producto['STOCK_DISPONIBLE']); ?>' disabled>
    </p>

    <p>
        <label>
            <input type="checkbox" <?php if ($producto['RECETA']) echo 'checked'; ?> disabled>
            ¿Necesita receta?
        </label>
    </p>

    <p>
        <button type='submit' class="danger">Sí, Borrar Permanentemente</button>
        <a href='items_list.php'>Cancelar</a>
    </p>
</form>

<?php
footerHtml();
?>

