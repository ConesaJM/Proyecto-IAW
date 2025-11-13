<?php 

// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/utils.php'; // (3º: Carga nuestras funciones)

// $marcas = 'listar_marcas'($pdo); ---> items_list 

require_admin();


{
    $id = $_GET['ID'] ?? 0;

    // Obtener datos actuales del producto
    $sql = "SELECT ID, NOMBRE, ACTIVO, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID  FROM PRODUCTO WHERE ID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $producto = $stmt->fetch();

    headerHtml("Editar producto");

    if (!$producto) {
        echo "<p>Producto no encontrado.</p>";
        echo "<p><a href='?action=list'>Volver</a></p>";
        footerHtml();
        return;
    }
}
?>


     <form method='post' action='?action=edit_save'>
            <input type='hidden' name='ID' value='" . h($producto['ID']) . "'>
            <p>
                <label>Nombre:<br>
                <input type='text' name='NOMBRE' value='" . h($producto['NOMBRE']) . "' required></label>
            </p>

            <p>
                <label>Activo:<br>
                <input type='text' name='ACTIVO' value='" . h($producto['ACTIVO']) . "' required></label>
            </p>

             <form>
               <label>Receta</label>
               <input type="checkbox" name="RECETA" value='" . h($producto['RECETA']) . "' required>>
            </form>

            <p>
                <label>Precio(€):<br>
                <input type='number' step='0.01' name='PRECIO' value='" . h($producto['PRECIO']) . "' required></label>
            </p>

            <p>
                <label>Stock Disponible:<br>
                <input type='text' name='STOCK_DISPONIBLE' value='" . h($producto['STOCK_DISPONIBLE']) . "' required></label>
            </p>

            <p>
                <label>Marca:<br>
                <input type='text' name='MARCA_ID' value='" . h($producto['MARCA_ID']) . "' required></label>
            </p>

            <p>
                <button type='submit'>Guardar cambios</button>
                <a href='?action=list'>Cancelar</a>
            </p>
          </form>;
<?php 
    footerHtml();
?>

