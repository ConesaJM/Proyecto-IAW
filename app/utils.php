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
function crearProducto(PDO $pdo, string $nombre, string $activo, int $receta, float $precio, int $stock, int $marca_id): int {
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
    echo "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css\"/>";

    // CSS 
    echo "<style>
            :root {
                --color-primario: #007BFF;
                --color-primario-hover: #0056b3;
                --color-peligro: #dc3545;
                --color-peligro-hover: #a00;
                --color-fondo: #f4f7f6;
                --color-borde: #ccc;
                --color-texto: #000000ff;
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
            body.tema-oscuro { 
                background-color: #222; color: #eee; 
                --color-fondo: #222; --color-borde: #555; --color-texto: #eee;
            }
            body.tema-oscuro table { background-color: #333; }
            body.tema-oscuro th { background-color: #444; }
            body.tema-oscuro input, body.tema-oscuro select { 
                background-color: #555; color: #eee; border-color: #777; 
            }
            body.tema-oscuro .topnav-left a { color: #8af; }
            body.tema-oscuro .topnav-left a:hover {
            color: white; 
            }
            table { border-collapse: collapse; width: 100%; margin-top: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
            th, td { border: 1px solid var(--color-borde); padding: 10px 12px; text-align: left; }
            th { background: #eee; font-weight: 600; }
            .topnav { display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 2px solid var(--color-borde); margin-bottom: 20px; }
            .topnav-left a { 
            margin-right: 0.5rem; /* Reducimos el margen para compensar el padding */
            text-decoration: none;
            font-weight: 500;
            color: var(--color-primario);
            padding: 8px 12px;
            border-radius: var(--radio-borde); 
            transition: background-color 0.3s ease, color 0.3s ease; 
            }
            .topnav-left a:hover { 
            background-color: var(--color-primario); 
            color: white; 
            }
            .topnav-right { text-align: right; font-size: 0.9rem; }
            .topnav-right a { color: var(--color-peligro); }
            .topnav i[class*=fa-] {
                font-size: 1.80em;
                vertical-align: middle; 
                transition: transform 0.2s ease; 
            }
            .topnav i[class*=fa-gear], 
            .topnav i[class*=fa-right-from-bracket] {
                margin-right: 6px; 
            }
            .topnav i[class*=fa-circle-user] {
                margin-left: 6px; 
            }
            a.logout-link {
                color: var(--color-primario); 
                text-decoration: none;
                transition: background-color 0.3s ease;
            }
            a.logout-link:hover, a.logout-link:hover i {
                color: var(--color-peligro); 
                transform: scale(1.15);
            }
            a.btn-edit {
              display: inline-block;
              padding: 4px 8px;
              font-size: 0.9rem;
              font-weight: 500;
              color: white;
              background-color: var(--color-primario);
              border-radius: var(--radio-borde);
              text-decoration: none;
              transition: background-color 0.3s ease;
            }
            a.btn-edit:hover {
              background-color: var(--color-primario-hover);
              text-decoration: none;
              color: white;
            }
            .topnav a:not(.logout-link):hover i, .topnav span i:hover {
                transform: scale(1.15); 
            }
            form.inline { display:inline; margin:0; padding:0; }
            .pagination a, .pagination strong { margin-right:8px; text-decoration: none; }
            .pagination strong { font-weight: bold; }
            form p { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; font-weight: 500; }
            input[type=text], input[type=number], input[type=password], select { width: 100%; padding: 8px 10px; border: 1px solid var(--color-borde); border-radius: var(--radio-borde); box-sizing: border-box; 
                transition: border-color 0.3s ease, box-shadow 0.3s ease;
            }
            input[type=text]:focus, 
            input[type=number]:focus, 
            input[type=password]:focus, 
            select:focus {
                border-color: var(--color-primario);
                box-shadow: 0 0 10px rgba(0,123,255, 0.6); /* Resplandor azul */
                outline: none; /* Quita el borde feo por defecto */
            }
            button { padding: 10px 15px; cursor: pointer; border: none; border-radius: var(--radio-borde); background-color: var(--color-primario); color: white; font-size: 1rem; font-weight: 500; transition: background-color 0.3s ease; }
            button:hover { background-color: var(--color-primario-hover); }
            button.danger { background-color: var(--color-peligro); padding: 4px 8px; font-size: 0.9rem; }
            button.danger:hover { background-color: var(--color-peligro-hover); }
            .error { color: var(--color-peligro); background-color: #fdd; border: 1px solid var(--color-peligro); padding: 10px; border-radius: var(--radio-borde); margin-bottom: 15px; }
            /* --- Animación de Keyframes --- */
            @keyframes fadeIn {
                from { 
                    opacity: 0; 
                    transform: translateY(10px); /* Sube 10px */
                }
                to { 
                    opacity: 1; 
                    transform: translateY(0); 
                }
            }
            
            /* Aplicamos la animación a los elementos principales */
            h1, h2, p, table, form {
                animation: fadeIn 0.5s ease-out forwards;
            }
        </style>";
    // 1. Leemos la cookie que guardamos en preferencias.php
    $tema_cookie = $_COOKIE['user_theme'] ?? 'claro'; // 'claro' u 'oscuro'

    // 2. Imprimimos el </head> y el <body> con preferencia del usuario
    echo "</head><body class='tema-" . h($tema_cookie) . "'>";

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
    // Muestra el nombre y el icono de usuario
    echo "<span><strong>" . $nombre_usuario . "</strong> <i class=\"fa-solid fa-circle-user fa-xl\"></i></span>"; 
    
    echo "&nbsp; <a href='logout.php' class='logout-link'><i class=\"fa-solid fa-right-from-bracket fa-xl\"></i></a>";
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