<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/utils.php';
require_once __DIR__ . '/../app/csrf.php';

require_login();

// CORRECCIÓN AQUÍ: $_GET['ID'] en mayúscula
$id = isset($_GET['ID']) ? (int)$_GET['ID'] : 0;

// VALIDACION DE ERROR
if ($id <= 0) {
    header("Location: items_list.php?IDNOP");
    exit;
}

$sql = "SELECT P.*, M.NOMBRE AS MARCA_NOMBRE 
        FROM PRODUCTO P 
        LEFT JOIN MARCA M ON P.MARCA_ID = M.ID 
        WHERE P.ID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    headerHtml("Error");
    echo "<div class='alert error'>Producto no encontrado (ID: $id).</div>";
    echo "<a href='items_list.php' class='btn-edit'>Volver</a>";
    footerHtml();
    exit;
}

// GENERAR DESC
$nombre = h($producto['NOMBRE']);
$categoria = h($producto['CATEGORIA']);
$marca = h($producto['MARCA_NOMBRE'] ?? 'Genérico');
$precio = number_format($producto['PRECIO'], 2);
$stock = $producto['STOCK_DISPONIBLE'];
$receta = $producto['RECETA'] ? "SÍ requiere receta médica." : "NO requiere receta médica.";

$texto_descripcion = "
FICHA TÉCNICA
-------------
Producto: $nombre
Marca: $marca
Categoría: $categoria

Detalles:
El artículo '$nombre' pertenece a la categoría '$categoria' y es distribuido por $marca.
Su precio actual es de $precio €.

Información Adicional:
- Disponibilidad: $stock unidades en stock.
- Receta: Este producto $receta
";

headerHtml("Detalle de " . $nombre);
?>

<div style="max-width: 800px; margin: 0 auto;">
    <a href="items_list.php" class="btn-edit" style="margin-bottom: 20px; display:inline-block;">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>

    <h1><?= $nombre ?></h1>

   <div class="show-container">
    
    <div style="margin-bottom: 20px;">
        <a href="items_list.php" class="btn-edit" style="background-color: #6c757d; color: white;">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="ficha-card">
        
        <div class="ficha-header">
            <h1 class="ficha-title"><?= $nombre ?></h1>
            <span class="ficha-category"><?= $categoria ?></span>
        </div>

        <div class="ficha-body">
            
            <p class="intro-text">
                El producto <strong><?= $nombre ?></strong> es una referencia destacada dentro de nuestra línea de <?= strtolower($categoria) ?>. 
                Es fabricado y garantizado por la marca <strong><?= $marca ?></strong>, cumpliendo con todos los estándares de calidad sanitaria.
            </p>

            <hr class="divider">

            <h3><i class="fa-solid fa-circle-info"></i> Información de venta</h3>
            
            <ul class="details-list">
                <li>
                    <strong>Precio actual:</strong> <span class="price-highlight"><?= $precio ?> €</span>
                </li>
                <li>
                    <strong>Disponibilidad:</strong> 
                    <?php if($stock > 0): ?>
                        Contamos con <strong><?= $stock ?> unidades</strong> en nuestros almacenes listas para envío inmediato.
                    <?php else: ?>
                        <span class="text-danger">Actualmente sin stock.</span>
                    <?php endif; ?>
                </li>
                <li>
                    <strong>Condiciones de dispensación:</strong><br>
                    <?= $icono_receta ?> <?= $texto_receta ?>
                </li>
            </ul>

            <div class="extra-note">
                <small>Nota: Los precios pueden variar sin previo aviso. Consulte con su farmacéutico si tiene dudas sobre el uso de este producto.</small>
            </div>

        </div>

        <?php if ($_SESSION['user_rol'] != 'Administrador'): ?>
        <div class="ficha-footer">
            <a href="carrito_add.php?ID=<?= $producto['ID'] ?>" class="btn-buy big-buy-btn">
                <i class="fa-solid fa-cart-shopping"></i> Añadir al Carrito (<?= $precio ?> €)
            </a>
        </div>
        <?php endif; ?>

    </div>
</div>

<style>
    /* Contenedor central */
    .show-container {
        max-width: 800px;
        margin: 0 auto;
        padding-bottom: 40px;
    }

    /* Tarjeta estilo papel */
    .ficha-card {
        background-color: var(--color-card); /* Blanco en modo claro */
        border: 1px solid var(--color-borde);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08); /* Sombra suave */
        overflow: hidden;
    }

    /* Encabezado */
    .ficha-header {
        background: linear-gradient(to right, #f8f9fa, #ffffff);
        padding: 30px 40px;
        border-bottom: 1px solid var(--color-borde);
    }
    
    /* Modo oscuro ajuste header */
    body.tema-oscuro .ficha-header {
        background: linear-gradient(to right, #2c2c2c, #1e1e1e);
    }

    .ficha-title {
        margin: 0;
        font-size: 2rem;
        color: var(--color-primario);
        font-weight: 700;
    }

    .ficha-category {
        display: inline-block;
        margin-top: 10px;
        background-color: rgba(0,0,0,0.05);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        color: var(--color-texto-muted);
        border: 1px solid var(--color-borde);
    }

    /* Cuerpo del texto */
    .ficha-body {
        padding: 40px;
        font-size: 1.1rem;
        line-height: 1.8; /* Espaciado entre líneas para leer mejor */
        color: var(--color-texto);
    }

    .intro-text {
        margin-bottom: 30px;
        font-size: 1.15rem;
    }

    .divider {
        border: 0;
        border-top: 1px solid var(--color-borde);
        margin: 30px 0;
    }

    h3 {
        color: var(--color-texto);
        font-size: 1.3rem;
        margin-bottom: 20px;
    }

    /* Lista de detalles limpia */
    .details-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .details-list li {
        margin-bottom: 15px;
        padding-left: 15px;
        border-left: 3px solid var(--color-primario); /* Línea de color a la izquierda */
    }

    .price-highlight {
        font-size: 1.4rem;
        font-weight: bold;
        color: var(--color-primario);
    }

    .text-danger { color: var(--color-peligro); }
    .text-success { color: var(--color-secundario); }

    .extra-note {
        margin-top: 40px;
        padding: 15px;
        background-color: rgba(0,0,0,0.02);
        border-radius: 8px;
        color: var(--color-texto-muted);
        font-style: italic;
    }

    /* Footer con botón */
    .ficha-footer {
        padding: 20px 40px;
        background-color: var(--color-fondo);
        border-top: 1px solid var(--color-borde);
        text-align: right;
    }

    .big-buy-btn {
        padding: 12px 30px;
        font-size: 1.2rem;
        display: inline-block;
        text-decoration: none;
        border-radius: 8px;
    }
</style>

<?php
footerHtml();
?>