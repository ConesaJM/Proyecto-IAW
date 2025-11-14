<?php
// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/utils.php'; // (3º: Carga nuestras funciones)

// 2. PROTECCIÓN DE LA PÁGINA 
// Esta función de auth.php comprobará si hay una sesión iniciada.
// Si no la hay, redirige a login.php y el script muere aquí.
require_login(); 

// 3. MOSTRAR LA PÁGINA
// Si el script llega aquí, el usuario SÍ está logueado.
// Con la función headerHtml() mostramos el HTML creado dentro de utils.php
headerHtml('Panel Principal - Pharmasphere');
?>


<?php
$q = trim($_GET['q'] ?? '');
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) {
    $page = 1;
}

// Productos por página
$limit = 10;
$offset = ($page - 1) * $limit;

// Si no hay texto de busqueda, pasamos null a listarProductos
$buscar = ($q === '') ? null : $q;

// Llamamos productos con función utils.php
$productos = listarProductos($pdo, $buscar, $limit, $offset);

// Contar cuántos hay en total (para la paginación)
if ($buscar !== null) {
    $sqlCount = "SELECT COUNT(*) AS total FROM PRODUCTO WHERE NOMBRE LIKE ?";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute(["%{$buscar}%"]);
} else {
    $sqlCount = "SELECT COUNT(*) AS total FROM PRODUCTO";
    $stmtCount = $pdo->query($sqlCount);
}

$filaCount = $stmtCount->fetch();
$total = (int)($filaCount['total'] ?? 0);

$total_pages = ($total > 0 && $limit > 0)
    ? (int)ceil($total / $limit)
    : 1;


?>

    <h2>Listado de productos</h2>

    <!-- Buscador -->
<form method="get" style="margin-bottom:1rem;">
    <label>Buscar por nombre:</label>
    <input type="text" name="q" value="<?= h($q) ?>">
    <br><br>
    <button type="submit">Buscar</button>
</form>

<!-- Tabla productos -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Activo</th>
            <th>Receta</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Marca</th>


            <?php if ($_SESSION['user_rol'] === 'Administrador'): ?>
                <th>Acciones</th>
            <?php endif; ?>

            <?php if ($_SESSION['user_rol'] !== 'Administrador'): ?>
                <th>Compra</th>
            <?php endif; ?>

        </tr>
    </thead>
    <tbody>
    <?php if (empty($productos)): ?>
        <tr>
            <td colspan="7">No se han encontrado productos.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= h($p['ID']) ?></td>
                <td><?= h($p['NOMBRE']) ?></td>
                <td><?= h($p['ACTIVO']) ?></td>
                <td><?= $p['RECETA'] ? 'Sí' : 'No' ?></td>
                <td><?= h($p['PRECIO']) ?></td>
                <td><?= h($p['STOCK_DISPONIBLE']) ?></td>
                <td><?= h($p['MARCA_ID']) ?></td>


                  <!-- Acciones solo para admin -->
                    <?php if ($_SESSION['user_rol'] === 'Administrador'): ?>
                        <td>
                            <!-- Editar -->
                            <a href="items_form.php?ID=<?= h($p['ID']) ?>" class="btn-edit">Editar</a>
                            &nbsp;|&nbsp;

                            <!-- Borrar -->
                            <form class="inline" method="post" action="items_delete.php"
                                  onsubmit="return confirm('¿Estás seguro de que quieres borrar este producto?');">
                                <input type="hidden" name="ID" value="<?= h($p['ID']) ?>">
                                <button class="danger" type="submit">Borrar</button>
                            </form>
                        </td>
                    <?php endif; ?>

                     <!-- Si NO es admin → columna "Compra" -->
                    <?php if ($_SESSION['user_rol'] !== 'Administrador'): ?>
                        <td>
                            <button>Comprar</button>
                        </td>
                    <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<!-- Paginación -->

<div class="pagination" style="margin-top:1rem;">
    <?php
    echo "Páginas: ";
    for ($i = 1; $i <= $total_pages; $i++) {
        $isCurrent = ($i === $page);

 
        $link = "items_list.php?page=$i&q=" . urlencode($q);

        if ($isCurrent) {
            echo "<strong>[$i]</strong> ";
        } else {
            echo "<a href='$link'>$i</a> ";
        }
    }
    ?>
</div>



<?php
// 5. CIERRE DE LA PÁGINA
// Llamamos a la otra función para cerrar </body></html>
// También dentro de utils.php
// 
footerHtml(); 
?>