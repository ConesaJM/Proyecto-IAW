<?php
// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/style.php'; // (3º: Carga los estilos CSS)
require_once __DIR__ . '/../app/utils.php'; // (4º: Carga nuestras funciones)
require_once __DIR__ . '/../app/csrf.php'; // (5º: CSRF PROTECCION POR TOKEN)

// 2. PROTECCIÓN DE LA PÁGINA 
// Esta función de auth.php comprobará si hay una sesión iniciada.
// Si no la hay, redirige a login.php y el script muere aquí.
require_login(); 

// Mostrar mensaje de éxito si viene en la URL
if (isset($_GET['exito'])) { // 1. Primero, comprueba si 'exito' existe

    // 2. Si existe, comprueba qué valor tiene
    if ($_GET['exito'] === 'guardado') {
        echo '<div class="alert success">Producto guardado correctamente.</div>';
    } elseif ($_GET['exito'] === 'borrado') {
        // Ahora sí compara el valor de 'exito' con 'borrado'
        echo '<div class="alert error">Producto borrado correctamente.</div>';
    }
}
?>

<?php
// 3. MOSTRAR LA PÁGINA
// Si el script llega aquí, el usuario SÍ está logueado.
// Con la función headerHtml() mostramos el HTML creado dentro de utils.php
headerHtml('');
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

    <h1>Listado de productos</h2>

  <!-- Buscador -->
<form method="get" class="search-form">
      <div class="search-input-group">
          <input type="text" name="q" value="<?= h($q) ?>" placeholder="Buscar producto por nombre...">
      </div>
      
      <button type="submit">
          <i class="fa-solid fa-magnifying-glass"></i> Buscar
      </button>
  </form>


<!-- Tabla productos -->
<table>
  <thead>
      <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Categoria</th>
          <th>Receta</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Marca</th>
          <?php if ($_SESSION['user_rol'] === 'Administrador'): ?>
          <th>Acciones</th>
          <?php endif; ?>
          <?php if ($_SESSION['user_rol'] != 'Administrador'): ?>
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
      <?php foreach ($productos as $p): 
            $stock = (int)$p['STOCK_DISPONIBLE'];
            $stockClass = '';
            $stockIcon = 'fa-circle'; // Círculo relleno

            if ($stock <= 50) {
                $stockClass = 'stock-low'; // Rojo
            } elseif ($stock <= 150) {
                $stockClass = 'stock-med'; // Naranja
            } else {
                $stockClass = 'stock-high'; // Verde
            }
        ?>
            <tr class="clickable-row" onclick="window.location.href='items_show.php?ID=<?= h($p['ID']) ?>';">
                <td><?= h($p['ID']) ?></td>
                <td><?= h($p['NOMBRE']) ?></td>
                <td><?= h($p['CATEGORIA']) ?></td>
                <td><?= $p['RECETA'] ? 'Sí' : 'No' ?></td>
                <td><?= h($p['PRECIO']) ?> €</td>
                
                <td>
                    <i class="fa-solid fa-circle <?= $stockClass ?>" 
                       title="Stock: <?= $stock ?>" 
                       style="font-size: 1.2em; cursor: help; vertical-align: middle;"></i>
                </td>

                <td>
                    <?= h($p['MARCA_NOMBRE'] ?? 'Sin Marca') ?> 
                </td>
                
                <td onclick="event.stopPropagation();">
                <?php if ($_SESSION['user_rol'] === 'Administrador'): ?>
                    
                    <div class="action-buttons">
                        <a href="items_form.php?ID=<?php echo h($p['ID']); ?>" class="btn-table btn-edit">
                            <i class="fa-solid fa-pen"></i> Editar
                        </a>

                        <a href="items_delete.php?ID=<?php echo h($p['ID']); ?>" 
                           class="btn-table btn-delete"
                           onclick="return confirm('¿Estás seguro de que quieres iniciar el borrado de este producto?');">
                           <i class="fa-solid fa-trash"></i> Borrar
                        </a>
                    </div>

                <?php else: ?>
                
                    <div class="action-buttons">
                        <a href="carrito_add.php?ID=<?php echo h($p['ID']); ?>" class="btn-table btn-buy">
                            <i class="fa-solid fa-cart-plus"></i> Añadir
                        </a>
                    </div>

                <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<!-- Paginación -->

<div class="pagination">
    <?php

    for ($i = 1; $i <= $total_pages; $i++) {
        // Mostramos la página actual
        $isCurrent = ($i === $page);
        
        // Construimos el link
        $link = "items_list.php?page=$i&q=" . urlencode($q);

        if ($isCurrent) {
            echo "<strong>$i</strong>"; 
        } else {
            // Otras páginas
            echo "<a href='$link'>$i</a>";
        }
    }
    ?>
</div>



<?php 
footerHtml(); 
?>