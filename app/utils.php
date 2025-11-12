<?php
// app/utils.php
// Versión acorde a la base de datos "pharmasphere_db"

/* ========= 1. FUNCIONES DE USUARIO =========== */

// Busca un usuario por su nombre (para el login).
function buscarUsuarioPorNombre(PDO $pdo, string $nombre_usuario): ?array {
    $sql = "SELECT * FROM USUARIO WHERE NOMBRE = ?"; // Tabla y columna actualizadas
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre_usuario]);
    $fila = $stmt->fetch();
    return $fila ?: null;
}

// Inserta un nuevo usuario en la base de datos (para el panel de admin).
function crearUsuario(PDO $pdo, string $nombre_usuario, string $password_hash, string $rol): bool {
    try {
        // Columnas actualizadas
        $sql = "INSERT INTO USUARIO (NOMBRE, CONTRASENHIA, ROL) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_usuario, $password_hash, $rol]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false; // Error de creación de usuario
    }
}

/* ========= 2. FUNCIONES DE PRODUCTO =========== */

// Crea un nuevo producto 
function crearProducto(PDO $pdo, string $nombre, string $activo, bool $receta, float $precio, int $stock, int $marca_id): int {
    // Tabla y columnas actualizadas
    $sql = "INSERT INTO PRODUCTO (NOMBRE, ACTIVO, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $activo, $receta, $precio, $stock, $marca_id]);
    return (int)$pdo->lastInsertId();
}

// Sistema de busqueda de producto por id
function leerProductoPorId(PDO $pdo, int $id): ?array {
    $sql = "SELECT * FROM PRODUCTO WHERE ID = ?"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $fila = $stmt->fetch();
    return $fila ?: null;
}

// READ-N: Listar los productos con búsqueda y paginación
function listarProductos(PDO $pdo, ?string $buscar = null, int $limit = 10, int $offset = 0): array {
    if ($buscar) {
        $sql = "SELECT * FROM PRODUCTO WHERE NOMBRE LIKE ? ORDER BY ID DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%{$buscar}%", $limit, $offset]);
    } else {
        $sql = "SELECT * FROM PRODUCTO ORDER BY ID DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);
    }
    return $stmt->fetchAll();
}

// UPDATE: Para actualizar un producto ya existente
function actualizarProducto(PDO $pdo, int $id, string $nombre, string $activo, bool $receta, float $precio, int $stock, int $marca_id): bool {
    $sql = "UPDATE PRODUCTO SET 
                NOMBRE = ?, 
                ACTIVO = ?, 
                RECETA = ?, 
                PRECIO = ?, 
                STOCK_DISPONIBLE = ?, 
                MARCA_ID = ? 
            WHERE ID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $activo, $receta, $precio, $stock, $marca_id, $id]);
    return $stmt->rowCount() > 0;
}

// DELETE: Para borrar producto
function borrarProducto(PDO $pdo, int $id): bool {
    $sql = "DELETE FROM PRODUCTO WHERE ID = ?"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->rowCount() > 0;
}

/* ========= 3. FUNCION DE FILTRADO POR MARCA =========== */

// Listar todas las marcas.
// Será usada en el formulario de productos para un filtrar por marcas.
function listarMarcas(PDO $pdo): array {
    $sql = "SELECT ID, NOMBRE FROM MARCA ORDER BY NOMBRE ASC";
    $stmt = $pdo->query($sql); 
    return $stmt->fetchAll();
}

// ---------------------------------------------------------------------------------------------------------------\\
/* ========= 4. HELPERS DE VISTA =========== */

// Escapa el HTML para prevenir ataques XSS.
function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Imprime el HTML, los estilos CSS y la navegación.
function headerHtml($title = 'Pharmasphere') // Título actualizado a Pharmasphere
{
    // Lógica de sesión (auth.php)
    $is_logged_in = isset($_SESSION['user_id']);
    // Comprobación de rol de administrador
    $is_admin = $is_logged_in && isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'Administrador';

    echo "<!DOCTYPE html><html lang='es'><head><meta charset='utf-8'><title>" . h($title) . "</title>";
    
    // CSS 
    echo "<style>
            :root {
                --color-primario: #007BFF;
                --color-primario-hover: #0056b3;
                --color-peligro: #dc3545;
                --color-peligro-hover: #a00;
                --color-fondo: #f4f7f6;
                --color-borde: #ccc;
                --color-texto: #333;
                --ancho-max: 900px;
                --radio-borde: 5px;
            }
            body { 
                font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; 
                max-width: var(--ancho-max); 
                margin: 20px auto; 
                line-height: 1.6;
                background-color: var(--color-fondo);
                color: var(--color-texto);
                padding: 0 15px;
            }
            table { border-collapse: collapse; width: 100%; margin-top: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
            th, td { border: 1px solid var(--color-borde); padding: 10px 12px; text-align: left; }
            th { background: #eee; font-weight: 600; }
            .topnav { display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 2px solid var(--color-borde); margin-bottom: 20px; }
            .topnav-left a { margin-right: 1rem; text-decoration: none; font-weight: 500; color: var(--color-primario); }
            .topnav-left a:hover { text-decoration: underline; }
            .topnav-right { text-align: right; font-size: 0.9rem; }
            .topnav-right a { color: var(--color-peligro); }
            form.inline { display:inline; margin:0; padding:0; }
            .pagination a, .pagination strong { margin-right:8px; text-decoration: none; }
            .pagination strong { font-weight: bold; }
            form p { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; font-weight: 500; }
            input[type=text], input[type=number], input[type=password], select { width: 100%; padding: 8px 10px; border: 1px solid var(--color-borde); border-radius: var(--radio-borde); box-sizing: border-box; }
            button { padding: 10px 15px; cursor: pointer; border: none; border-radius: var(--radio-borde); background-color: var(--color-primario); color: white; font-size: 1rem; font-weight: 500; }
            button:hover { background-color: var(--color-primario-hover); }
            button.danger { background-color: var(--color-peligro); padding: 4px 8px; font-size: 0.9rem; }
            button.danger:hover { background-color: var(--color-peligro-hover); }
            .error { color: var(--color-peligro); background-color: #fdd; border: 1px solid var(--color-peligro); padding: 10px; border-radius: var(--radio-borde); margin-bottom: 15px; }
          </style>";
    echo "</head><body>";

    // --- BARRA DE NAVEGACIÓN ---
    echo "<div class='topnav'>";
    
    // Barra izquierda
    echo "<div class='topnav-left'>";
    if ($is_logged_in) {
        echo "<a href='index.php'>Panel</a>";
        echo "<a href='items_list.php'>Productos</a>"; 
        
        if ($is_admin) {
            echo "<a href='items_form.php'>Nuevo producto</a>";
            echo "<a href='user_form.php'>Crear Usuario</a>"; // Nuevo enlace para crear usuarios, para el futuro implementar o reconsiderar.
            // echo "<a href='auditoria_list.php'>Auditoría</a>"; | Para el futuro
        }
        echo "<a href='preferencias.php'>Preferencias</a>";
    }
    echo "</div>";

    // Barra derecha
    echo "<div class='topnav-right'>";
    if ($is_logged_in) {
        $nombre_usuario = h($_SESSION['user_nombre_usuario'] ?? 'Usuario');
        echo "<span>Hola, $nombre_usuario</span>";
        echo "&nbsp; <a href='logout.php'>Logout</a>";
    } else {
        echo "<a href='login.php'>Login</a>";
    }
    echo "</div>";
    
    echo "</div>";
    echo "<h1>" . h($title) . "</h1>";
}

// Imprime el pie de página y cierra el HTML
function footerHtml()
{
    echo "<hr><small>Proyecto IAW - Pharmasphere</small>"; 
    echo "</body></html>";
}
?>
<!--------------------------------------------------------------------------------------------------------------------------------------->