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