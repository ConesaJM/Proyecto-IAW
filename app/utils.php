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
    // Seleccionamos todo de PRODUCTO (P.*) y el NOMBRE de MARCA (M.NOMBRE)
    $campos = "P.*, M.NOMBRE AS MARCA_NOMBRE";
    
    if ($buscar) {
        $sql = "SELECT $campos 
                FROM PRODUCTO P
                LEFT JOIN MARCA M ON P.MARCA_ID = M.ID
                WHERE P.NOMBRE LIKE ? 
                ORDER BY P.ID DESC 
                LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%{$buscar}%", $limit, $offset]);
    } else {
        $sql = "SELECT $campos 
                FROM PRODUCTO P
                LEFT JOIN MARCA M ON P.MARCA_ID = M.ID
                ORDER BY P.ID DESC 
                LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);
    }
    return $stmt->fetchAll();
}

// UPDATE: Para actualizar un producto ya existente
function actualizarProducto(PDO $pdo, int $id, string $nombre, string $activo, int $receta, float $precio, int $stock, int $marca_id): bool {
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

/* ========= 4. FUNCIONES DE AUDITORÍA =========== */

// REGISTRAR: Guarda un evento en la tabla AUDITORIA
// Se usará dentro de items_delete.php

// Registra los datos del producto borrado dentro de la tabla AUDITORIA
function registrarAuditoria(PDO $pdo, string $accion, string $detalle): bool {
    $sql = "INSERT INTO AUDITORIA (NOMBRE, DETALLE) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$accion, $detalle]);
}

// LISTAR: Devuelve todo el historial de cambios
function auditoria_list(PDO $pdo): array {
    try {
        $sql = "SELECT * FROM AUDITORIA ORDER BY FECHA DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return []; // Si falla, devolvemos array vacío
    }
}

// ---------------------------------------------------------------------------------------------------------------\\
/* ========= 5. HELPERS DE VISTA =========== */

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
                --color-secundario: #56ac47ff; 
                --color-secundario-hover: #438a36ff;
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
            tbody tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            body.tema-oscuro tbody tr:nth-child(even) {
                background-color: #3f3f3fff; /* Un gris un poco más oscuro */
            }
            tbody tr {
                transition: background-color 0.2s ease;
            }
            tbody tr:hover {
                background-color: #d8ecff; /* Un azul muy claro */
            }
            body.tema-oscuro tbody tr:hover {
                background-color: #505050; /* Un gris más claro */
            }
            table { 
            width: 100%; 
            margin-top: 1.5rem; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            border-spacing: 0;
            border-radius: var(--radio-borde); /* Redondea las esquinas */
            overflow: hidden; 
            }
            th, td { 
            padding: 14px 16px; 
            text-align: left; 
            border-bottom: 1px solid var(--color-borde);
            }
            body.tema-oscuro th, body.tema-oscuro td {
            border-bottom: 1px solid var(--color-borde);
            }
            th { 
            background: var(--color-primario); 
            color: white;
            font-weight: 600;
            text-transform: uppercase; /* Estilo profesional */
            font-size: 0.85em;
            letter-spacing: 0.5px;
            }
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
              transition: background-color 0.3s ease, transform 0.2s ease;
            }
            a.btn-edit:hover {
              background-color: var(--color-primario-hover);
              text-decoration: none;
              color: white;
            }
            a.btn-buy {
              display: inline-block;
              padding: 4px 8px;
              font-size: 0.9rem;
              font-weight: 500;
              color: white;
              background-color: var(--color-secundario); 
              border-radius: var(--radio-borde);
              text-decoration: none;
              transition: background-color 0.3s ease, transform 0.2s ease;
              animation: pulse 2s infinite;
            }
            a.btn-buy:hover {
              background-color: var(--color-secundario-hover);
              text-decoration: none;
              color: white;
            }
            .topnav a:not(.logout-link):hover i, .topnav span i:hover {
                transform: scale(1.15); 
            }
            form.inline { display:inline; margin:0; padding:0; }
            .pagination a, .pagination strong {
                padding: 6px 12px;
                border: 1px solid var(--color-borde);
                border-radius: var(--radio-borde);
                text-decoration: none;
                margin-right: 5px;
            }
            .pagination a {
                color: var(--color-primario);
                transition: background-color 0.2s ease, color 0.2s ease;
            }
            .pagination a:hover {
                background-color: var(--color-primario-hover);
                color: white;
                border-color: var(--color-primario-hover);
            }
            .pagination strong {
                background-color: var(--color-primario);
                color: white;
                border-color: var(--color-primario);
                font-weight: 600;
            }
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
            button:active, 
            a.btn-edit:active, 
            a.btn-buy:active {
                transform: scale(0.97); /* Ligeramente más pequeño */
                filter: brightness(0.9); /* Ligeramente más oscuro */
            }
            a.btn-delete {
              display: inline-block;
              padding: 4px 8px;
              font-size: 0.9rem;
              font-weight: 500;
              color: white;
              background-color: var(--color-peligro); /* Rojo */
              border-radius: var(--radio-borde);
              text-decoration: none;
              transition: background-color 0.3s ease, transform 0.1s ease;
            }
            a.btn-delete:hover {
              background-color: var(--color-peligro-hover);
              color: white;
              text-decoration: none;
            }
            a.btn-delete:active {
                transform: scale(0.97);
            }
            button { padding: 10px 15px; cursor: pointer; border: none; border-radius: var(--radio-borde); background-color: var(--color-primario); color: white; font-size: 1rem; font-weight: 500; transition: background-color 0.3s ease, transform 0.2s ease; }
            button:hover { background-color: var(--color-primario-hover); }
            button.danger { background-color: var(--color-peligro); padding: 4px 8px; font-size: 0.9rem; }
            button.danger:hover { background-color: var(--color-peligro-hover); }
            .error { color: var(--color-peligro); background-color: #fdd; border: 1px solid var(--color-peligro); padding: 10px; border-radius: var(--radio-borde); margin-bottom: 15px; }
            /* --- Alertas de Feedback (con Iconos) --- */
            .alert {
                padding: 15px;
                margin-bottom: 20px;
                border-radius: var(--radio-borde);
                font-weight: 500;
                animation: fadeIn 0.5s ease-out forwards; /* Reutiliza tu anim. */
            }
            .alert.success {
                color: #fff; 
                background-color: var(--color-secundario); /* Tu verde principal */
                border: 2px solid var(--color-secundario-hover); /* Borde más oscuro */
            }
            .alert.error {
                color: var(--color-peligro);
                background-color: #fdd;
                border: 1px solid var(--color-peligro);
            }
            /* Iconos para las alertas */
            .alert.success::before {
                font-family: 'Font Awesome 6 Free';
                font-weight: 900;
                content: '\f00c'; /* Icono: fa-check */
                margin-right: 10px;
            }
            .alert.error::before {
                font-family: 'Font Awesome 6 Free';
                font-weight: 900;
                content: '\f071'; /* Icono: fa-exclamation-triangle */
                margin-right: 10px;
            }
            .stock-high { color: var(--color-secundario); font-weight: 600; } /* Verde (Alto) */
            .stock-med  { color: #fd7e14; font-weight: 600; } /* Naranja (Medio) */
            .stock-low  { color: var(--color-peligro); font-weight: 600; } /* Rojo (Bajo) */
            
            /* Círculo indicador */
            .stock-indicator { margin-left: 15px; font-size: 0.8em; vertical-align: middle; }

            /* --- Estilos para Textarea de Motivo --- */
            .form-textarea {
                width: 100%;
                padding: 12px;
                border: 1px solid var(--color-borde);
                border-radius: var(--radio-borde);
                font-family: inherit;
                font-size: 0.95rem;
                resize: vertical; /* Permite redimensionar solo verticalmente */
                transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
                background-color: #fff; 
                color: var(--color-texto);
                min-height: 80px;
            }

            body.tema-oscuro .form-textarea {
                background-color: #333; /* Gris oscuro */
                border-color: #555;     /* Borde más sutil */
                color: #eee;            /* Texto claro */
            }
            
            body.tema-oscuro .form-textarea:focus {
                border-color: var(--color-peligro); /* Mantiene el rojo al enfocar */
                background-color: #3a3a3a; /* Un poco más claro al enfocar */
            }
            
            .form-textarea:focus {
                border-color: var(--color-peligro); /* Rojo al enfocar (porque es borrado) */
                box-shadow: 0 0 8px rgba(220, 53, 69, 0.3); /* Resplandor rojo suave */
                outline: none;
            }

            /* Etiqueta destacada para el motivo */
            .label-required {
                font-weight: 600;
                color: var(--color-peligro); /* Texto rojo para indicar importancia */
                margin-bottom: 8px;
                display: block;
            }
            .label-required::after {
                content: *;
                color: var(--color-peligro);
            }

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
            @keyframes pulse {
                0% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
                }
                70% {
                    transform: scale(1.05);
                    box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
                }
                100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
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
            echo "<a href='items_delete.php?action=auditoria'>Auditoría</a>"; // Seccion auditoria creada
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

    // --- INICIO DEL WIDGET DE TAWK.TO ---
    ?>
    
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/691af7ebccfc7c195b6c49fc/1ja8lgs42';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <?php
    // --- FIN DEL WIDGET ---

    echo "</body></html>";
}
?>






<!--------------------------------------------------------------------------------------------------------------------------------------->