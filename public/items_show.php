<?php
// 1. INCLUIR ARCHIVOS NECESARIOS
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/utils.php';
require_once __DIR__ . '/../app/csrf.php';

require_login();

// 2. OBTENER ID DEL PRODUCTO
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: items_list.php");
    exit;
}

// 3. BUSCAR PRODUCTO EN BD
$sql = "SELECT P.*, M.NOMBRE AS MARCA_NOMBRE 
        FROM PRODUCTO P 
        LEFT JOIN MARCA M ON P.MARCA_ID = M.ID 
        WHERE P.ID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    headerHtml("Error");
    echo "<div class='alert error'>Producto no encontrado.</div>";
    echo "<a href='items_list.php'>Volver</a>";
    footerHtml();
    exit;
}

// 4. GENERAR LA DESCRIPCIÓN (TEXTO)
// Como en seed.sql no hay campo descripción, lo construimos nosotros.
$nombre = h($producto['NOMBRE']);
$categoria = h($producto['CATEGORIA']);
$marca = h($producto['MARCA_NOMBRE'] ?? 'Generico');
$precio = number_format($producto['PRECIO'], 2);
$stock = $producto['STOCK_DISPONIBLE'];
$receta = $producto['RECETA'] ? "SÍ requiere receta médica para su venta." : "NO requiere receta médica, es de venta libre.";

// Creamos el bloque de texto narrativo
$texto_descripcion = "
FICHA TÉCNICA DEL PRODUCTO
--------------------------
Producto: $nombre
Marca: $marca
Categoría: $categoria

Descripción Detallada:
El artículo '$nombre' es un producto farmacéutico clasificado dentro de la categoría de $categoria. Es distribuido oficialmente por la marca $marca.

Condiciones de Venta:
Actualmente, este producto tiene un precio de mercado de $precio €. 
Respecto a su regulación, este artículo $receta

Disponibilidad:
Contamos con $stock unidades disponibles en nuestros almacenes centrales.
";

// 5. MOSTRAR HTML
headerHtml("Detalle de " . $nombre);
?>

<div style="max-width: 800px; margin: 0 auto;">

    <a href="items_list.php" class="btn-edit" style="margin-bottom: 20px; display:inline-block;">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>

    <h1><?= $nombre ?></h1>

    <div class="form-group">
        <label for="desc-box" style="font-size: 1.1rem; margin-bottom: 10px;">Información del producto:</label>
        
        <textarea id="desc-box" readonly style="
            width: 100%;
            height: 300px;
            padding: 20px;
            font-family: 'Courier New', monospace; /* Fuente tipo máquina de escribir */
            font-size: 1rem;
            line-height: 1.6;
            border: 2px solid var(--color-borde);
            border-radius: 8px;
            background-color: #fff;
            resize: none; /* Evita que se cambie el tamaño */
            color: #333;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
        "><?= h($texto_descripcion) ?></textarea>
    </div>

    <?php if ($_SESSION['user_rol'] != 'Administrador'): ?>
        <div style="margin-top: 20px; text-align: right;">
            <a href="carrito_add.php?ID=<?= $producto['ID'] ?>" class="btn-buy" style="font-size: 1.2rem; padding: 12px 24px;">
                <i class="fa-solid fa-cart-plus"></i> Añadir al carrito (<?= $precio ?> €)
            </a>
        </div>
    <?php endif; ?>

</div>

<?php
footerHtml();
?>