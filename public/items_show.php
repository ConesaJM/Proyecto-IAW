<?php
// 1. INCLUIR LOS CEREBROS
// No necesitamos pdo.php si no hacemos consultas SQL
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/utils.php';
require_once __DIR__ . '/../app/csrf.php'; // (CSRF PROTECCION POR TOKEN)

require_login();

// VALIDAR ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: items_list.php");
    exit;
}

// DATOA PRODUCTO + MARCA
$sql = "SELECT P.*, M.NOMBRE AS MARCA_NOMBRE 
        FROM PRODUCTO P 
        LEFT JOIN MARCA M ON P.MARCA_ID = M.ID 
        WHERE P.ID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$producto = $stmt->fetch();

// SI PRODUCTO NO EXISTE, REDIRIGIR
if (!$producto) {
    headerHtml("Producto no encontrado");
    echo "<div class='alert error'>El producto solicitado no existe o ha sido eliminado.</div>";
    echo "<a href='items_list.php'>Volver al listado</a>";
    footerHtml();
    exit;
}


$is_admin = isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'Administrador';

// LOGICA STOCK
$stock_class = 'stock-high';
$stock_text  = 'Alto';
if ($producto['STOCK_DISPONIBLE'] < 20) {
    $stock_class = 'stock-low';
    $stock_text  = 'Bajo';
} elseif ($producto['STOCK_DISPONIBLE'] < 50) {
    $stock_class = 'stock-med';
    $stock_text  = 'Medio';
}

// Inicio del HTML
headerHtml("Detalle del Producto: " . $producto['NOMBRE']);
?>

<div style="margin-bottom: 20px;">
    <a href="items_list.php" class="btn-edit" style="background-color: #6c757d;">
        <i class="fa-solid fa-arrow-left"></i> Volver al listado
    </a>
</div>

<div class="product-card">
    
    <div class="card-header">
        <h2>
            <?php if ($producto['CATEGORIA'] == 'Medicamento' || $producto['CATEGORIA'] == 'Antibiótico'): ?>
                <i class="fa-solid fa-pills" style="color: var(--color-primario);"></i>
            <?php elseif ($producto['CATEGORIA'] == 'Vitaminas'): ?>
                <i class="fa-solid fa-apple-whole" style="color: var(--color-secundario);"></i>
            <?php else: ?>
                <i class="fa-solid fa-box-open" style="color: #666;"></i>
            <?php endif; ?>
            
            <?= h($producto['NOMBRE']) ?>
        </h2>
        <span class="badge-cat"><?= h($producto['CATEGORIA']) ?></span>
    </div>

    <div class="card-body">
        <div class="detail-row">
            <strong>Marca:</strong> 
            <span><?= h($producto['MARCA_NOMBRE'] ?? 'Sin marca') ?></span>
        </div>

        <div class="detail-row">
            <strong>Precio:</strong> 
            <span class="price-tag"><?= number_format($producto['PRECIO'], 2) ?> €</span>
        </div>

        <div class="detail-row">
            <strong>Requiere Receta:</strong>
            <?php if ($producto['RECETA']): ?>
                <span style="color: var(--color-peligro); font-weight: bold;">
                    <i class="fa-solid fa-file-prescription"></i> SÍ
                </span>
            <?php else: ?>
                <span style="color: var(--color-secundario); font-weight: bold;">NO</span>
            <?php endif; ?>
        </div>

        <div class="detail-row">
            <strong>Stock Disponible:</strong>
            <span class="<?= $stock_class ?>">
                <?= $producto['STOCK_DISPONIBLE'] ?> u. 
                <i class="fa-solid fa-circle stock-indicator"></i> 
                <small>(<?= $stock_text ?>)</small>
            </span>
        </div>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--color-borde);">

        <div class="action-buttons">
            <a href="#" class="btn-buy" style="padding: 10px 20px; font-size: 1rem;">
                <i class="fa-solid fa-cart-shopping"></i> Añadir al carrito
            </a>

            <?php if ($is_admin): ?>
                <div style="margin-top: 10px; display:inline-block; margin-left: 10px;">
                    <a href="items_form.php?id=<?= $producto['ID'] ?>" class="btn-edit" style="padding: 10px 15px;">
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                    <a href="items_delete.php?id=<?= $producto['ID'] ?>" class="btn-delete" style="padding: 10px 15px;">
                        <i class="fa-solid fa-trash"></i> Borrar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .product-card {
        background-color: white;
        border: 1px solid var(--color-borde);
        border-radius: var(--radio-borde);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: 0 auto;
        overflow: hidden;
    }
    
    /* Soporte modo oscuro */
    body.tema-oscuro .product-card {
        background-color: #333;
        border-color: #444;
    }

    .card-header {
        background-color: var(--color-primario); /* Azul del tema */
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* En modo oscuro, el header se mantiene o se ajusta */
    body.tema-oscuro .card-header {
        background-color: var(--color-primario-hover);
    }

    .card-header h2 {
        margin: 0;
        font-size: 1.8rem;
    }

    .badge-cat {
        background-color: rgba(255, 255, 255, 0.2);
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.9rem;
        border: 1px solid rgba(255,255,255,0.4);
    }

    .card-body {
        padding: 30px;
        font-size: 1.1rem;
    }

    .detail-row {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .detail-row strong {
        width: 180px;
        color: #555;
    }
    
    body.tema-oscuro .detail-row strong {
        color: #bbb;
    }

    .price-tag {
        font-size: 1.5rem;
        color: var(--color-primario);
        font-weight: bold;
    }
</style>

<?php
footerHtml();
?>