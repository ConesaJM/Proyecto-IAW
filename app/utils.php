<?php
// (Aquí también irán las funciones de escapar HTML, validar, etc.)

// --- Funciones de Base de Datos para Productos --- */

/* CREATE: insertar producto */
function crearProducto(PDO $pdo, string $nombre, string $categoria, int $stock, float $precio): int {
    $sql = "INSERT INTO productos (nombre, categoria, stock, precio) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $categoria, $stock, $precio]);
    return (int)$pdo->lastInsertId();
}

/* READ-1: leer un producto por id */
function leerProductoPorId(PDO $pdo, int $id): ?array {
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $fila = $stmt->fetch();
    return $fila ?: null;
}

/* READ-N: listar productos con búsqueda y paginación */
function listarProductos(PDO $pdo, ?string $buscar = null, int $limit = 10, int $offset = 0): array {
    if ($buscar) {
        $sql = "SELECT * FROM productos WHERE nombre LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%{$buscar}%", $limit, $offset]);
    } else {
        $sql = "SELECT * FROM productos ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);
    }
    return $stmt->fetchAll();
}

/* UPDATE: actualizar producto */
function actualizarProducto(PDO $pdo, int $id, string $nombre, string $categoria, int $stock, float $precio): bool {
    $sql = "UPDATE productos SET nombre = ?, categoria = ?, stock = ?, precio = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $categoria, $stock, $precio, $id]);
    return $stmt->rowCount() > 0;
}
/* DELETE: borrar producto */
function borrarProducto(PDO $pdo, int $id): bool {
    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->rowCount() > 0;
}

// Escapa el HTML para prevenir ataques XSS.
function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// -----------------------------------------------------------------------------------------------------------------------------------------
// --- CREACIÓN ESTRUCTURA CSS Y HTML ---
/**
 * Imprimimos el <head> HTML, los estilos CSS y la navegación.
 * ademas de mostrar o ocultar enlaces según la sesión (auth.php).
 */
function headerHtml($title = 'Supermercado')
{
    // Comprobamos si el usuario está logueado y si es admin
    $is_logged_in = isset($_SESSION['user_id']);
    $is_admin = $is_logged_in && isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin';

    // Imprimimos el doctype, head y estilos
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='utf-8'><title>" . h($title) . "</title>";
    
    // Introducimos la interfaz CSS 
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
            table { 
                border-collapse: collapse; 
                width: 100%; 
                margin-top: 1rem; 
                box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            }
            th, td { 
                border: 1px solid var(--color-borde); 
                padding: 10px 12px; 
                text-align: left; 
            }
            th { 
                background: #eee; 
                font-weight: 600;
            }
            .topnav { 
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                padding-bottom: 10px;
                border-bottom: 2px solid var(--color-borde);
                margin-bottom: 20px;
            }
            .topnav-left a { 
                margin-right: 1rem; 
                text-decoration: none;
                font-weight: 500;
                color: var(--color-primario);
            }
            .topnav-left a:hover {
                text-decoration: underline;
            }
            .topnav-right { 
                text-align: right; 
                font-size: 0.9rem;
            }
            .topnav-right a {
                color: var(--color-peligro);
            }
            
            form.inline { display:inline; margin:0; padding:0; }
            .pagination a, .pagination strong { margin-right:8px; text-decoration: none; }
            .pagination strong { font-weight: bold; }
            
            /* --- Formularios Mejorados --- */
            form p {
                margin-bottom: 15px;
            }
            label {
                display: block;
                margin-bottom: 5px;
                font-weight: 500;
            }
            input[type=text], input[type=number], input[type=password], select {
                width: 100%;
                padding: 8px 10px;
                border: 1px solid var(--color-borde);
                border-radius: var(--radio-borde);
                box-sizing: border-box; /* Clave para que padding no rompa el ancho */
            }
            
            /* --- Botones Mejorados --- */
            button { 
                padding: 10px 15px; 
                cursor: pointer; 
                border: none;
                border-radius: var(--radio-borde);
                background-color: var(--color-primario);
                color: white;
                font-size: 1rem;
                font-weight: 500;
            }
            button:hover {
                background-color: var(--color-primario-hover);
            }
            button.danger { 
                background-color: var(--color-peligro);
                padding: 4px 8px; /* Hacemos los de borrar más pequeños */
                font-size: 0.9rem;
            }
            button.danger:hover {
                background-color: var(--color-peligro-hover);
            }

            /* --- Alertas --- */
            .error {
                color: var(--color-peligro);
                background-color: #fdd;
                border: 1px solid var(--color-peligro);
                padding: 10px;
                border-radius: var(--radio-borde);
                margin-bottom: 15px;
            }
          </style>";
    echo "</head><body>";