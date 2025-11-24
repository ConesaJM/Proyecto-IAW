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

// Lógica de Receta (Iconos y texto)
if ($producto['RECETA']) {
    $texto_receta = "<span class='text-danger'>Este medicamento <strong>requiere receta médica</strong>.</span>";
    $icono_receta = "<i class='fa-solid fa-file-prescription' style='color:var(--color-peligro);'></i>";
} else {
    $texto_receta = "<span class='text-success'>Este producto es de <strong>venta libre</strong>.</span>";
    $icono_receta = "<i class='fa-solid fa-circle-check' style='color:var(--color-secundario);'></i>";
}

// --- LÓGICA DE LA BARRA DE PROGRESO (STOCK) ---
$max_stock_ref = 250; // El tope visual es 250
$porcentaje = ($stock / $max_stock_ref) * 100;
if ($porcentaje > 100) $porcentaje = 100; // Que no se salga si hay más de 250

// Determinar color de la barra (Misma lógica que items_list)
$bar_color = 'var(--color-secundario)'; // Verde (High)
if ($stock <= 50) {
    $bar_color = 'var(--color-peligro)'; // Rojo (Low)
} elseif ($stock <= 150) {
    $bar_color = '#fd7e14'; // Naranja (Med)
}

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

headerHtml("");
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div class="show-container">

    <div class="ficha-card">
        
        <div class="ficha-header">
            <div>
                <h1 class="ficha-title"><?= $nombre ?></h1>
                <span class="ficha-category"><?= $categoria ?></span>
            </div>

            <div>
                <a href="items_list.php" class="btn-edit" style="padding: 8px 15px; font-size: 1rem; margin-bottom: 38px;">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>
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
                    <strong>Disponibilidad: </strong>
                    <div class="stock-container">
                        <div class="progress-track">
                            <div class="progress-fill" style="width: <?= $porcentaje ?>%; background-color: <?= $bar_color ?>;"></div>
                        </div>
                        <div class="stock-info">
                            <span style="font-weight:bold; color:<?= $bar_color ?>">
                            </span>
                        </div>
                    </div>
                </li> 
                <li>
                    <strong>Receta</strong><br>
                    <?= $icono_receta ?> <?= $texto_receta ?>
                </li>
            </ul>

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
        background-color: var(--color-card);
        border: 1px solid var(--color-borde);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    /* Encabezado */
    .ficha-header {
        background: linear-gradient(to right, #f8f9fa, #ffffff);
        padding: 15px 30px;
        border-bottom: 1px solid var(--color-borde);
        
        /* FLEXBOX PARA ALINEACIÓN */
        display: flex;                  
        justify-content: space-between; 
        align-items: center;            
    }
   
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
        padding: 5px 30px;
        font-size: 1.2rem;
        line-height: 1.8;
        color: var(--color-texto);
    }

    .intro-text {
        margin-bottom: 0px;
        font-size: 1.2rem;
    }

    .divider {
        border: 0;
        border-top: 1px solid var(--color-borde);
        margin: 20px 0;
    }

    h3 {
        color: var(--color-texto);
        font-size: 1.3rem;
        margin-bottom: 20px;
    }

    /* Lista de detalles */
    .details-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .details-list li {
        margin-bottom: 20px; 
        padding-left: 15px;
        border-left: 3px solid var(--color-primario);
    }

    /* ESTILOS PARA LA BARRA DE PROGRESO */
    .stock-container {
        margin-top: 8px;
        max-width: 400px; 
    }
   
    .progress-track {
        width: 100%;
        height: 10px;
        background-color: #e9ecef; 
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 5px;
    }
   
    /* Modo oscuro para el fondo de la barra */
    body.tema-oscuro .progress-track {
        background-color: #333;
    }

    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease-in-out; /* Animación suave al cargar */
    }

    .stock-info {
        display: flex;
        justify-content: space-between;
        font-size: 0.95rem;
    }
    /* ----------------------------------------------- */

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
        background-color: #1a1919ff;
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