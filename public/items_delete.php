<?php 
/*



require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/utils.php'; // (3º: Carga nuestras funciones)

-- ESTA PENDIENTE ESTE ARCHIVO



// 1. Obtención y validación de parámetros de entrada
$producto_id = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
$fallo = filter_input(INPUT_GET, 'fallo', FILTER_VALIDATE_INT); 

// 2. Si NO hay ID, redirigir
if (!$producto_id) { 
    header('Location: index.php?error=no_id');
    exit;
}

// INICIO TRANSACCION
// NOTA: Se asume que $pdo es una conexión PDO válida disponible.
$pdo->beginTransaction();
$exito = false;
$mensaje_error = null;

try {
    // 3. Registrar auditoría (Se espera que lance una Exception si falla)
    // Se asume que registra_auditoria y borrar_producto lanzan excepciones al fallar,
    // que es el patrón más limpio en transacciones.
    registra_auditoria($pdo, $producto_id, 'PRODUCTO', 'BORRADO');

    // 4. Simulación de fallo
    if ($fallo){
        // ¡CORRECCIÓN CLAVE!: Se elimina "message:" del mensaje de la excepción.
        throw new Exception("Fallo simulado, ¡el borrado no se ejecutará!"); 
    }

    // 5. Borrar producto (Se espera que lance una Exception si falla)
    borrar_producto($pdo, $producto_id);
    
    // CONFIRMAMOS (COMMIT) sólo si todas las operaciones fueron exitosas
    $pdo->commit();
    $exito = true;

} catch (Exception $e) { // BLOQUE CATCH
    // ROLLBACK si ocurre cualquier excepción
    $pdo->rollBack();
    $mensaje_error = $e->getMessage(); 
    $exito = false; 
}

// REDIRIGIR 
if ($exito) {
    // Redirige al éxito
    header('Location: index.php?msg=producto_borrado_ok!');
} else {
    // Redirige al error, codificando el mensaje para la URL
    $error_msg = urlencode($mensaje_error ?? "Error desconocido al intentar borrar el producto");
    header('Location: index.php?error=' . $error_msg);
}
exit; // Aseguramos que el script termine después de la redirección
*/
?>