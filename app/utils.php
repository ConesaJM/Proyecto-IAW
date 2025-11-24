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
function crearProducto(PDO $pdo, string $nombre, string $categoria, int $receta, float $precio, int $stock, int $marca_id): int {
    // Tabla y columnas actualizadas

        $sql = "INSERT INTO PRODUCTO (NOMBRE, CATEGORIA, RECETA, PRECIO, STOCK_DISPONIBLE, MARCA_ID) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $categoria, $receta, $precio, $stock, $marca_id]);
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
function actualizarProducto(PDO $pdo, int $id, string $nombre, string $categoria, int $receta, float $precio, int $stock, int $marca_id): bool {
    $sql = "UPDATE PRODUCTO SET 
                NOMBRE = ?, 
                CATEGORIA = ?, 
                RECETA = ?, 
                PRECIO = ?, 
                STOCK_DISPONIBLE = ?, 
                MARCA_ID = ? 
            WHERE ID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $categoria, $receta, $precio, $stock, $marca_id, $id]);
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
?>

<!--------------------------------------------------------------------------------------------------------------------------------------->