<?php 

require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/utils.php'; // (3º: Carga nuestras funciones)
require_once __DIR__ . '/../app/csrf.php'; // (4º: CSRF PROTECCION POR TOKEN)


// SOLO EL ADMIN ACCEDE
require_admin();


// [INICIO] GESTOR DE ACCIÓN DE AUDITORÍA


// Comprobar si se está pidiendo ver la auditoría
$accion = filter_input(INPUT_GET, 'action');

if ($accion === 'auditoria') {
    
    // FUNCION AUDITORIA
    $auditorias = auditoria_list($pdo); //

    // VISTA AUDITORIA
    $titulo_pagina = "Registro de Auditoría";
    headerHtml($titulo_pagina); //
    
    // TABLA

    echo "<table>";
    echo "<thead><tr><th>ID</th><th>Acción Registrada</th><th>Fecha</th><th>Motivo del Borrado</th></tr></thead>";
    echo "<tbody>";

    if (empty($auditorias)) {
        echo "<tr><td colspan='4'>No hay registros de auditoría.</td></tr>";
    } else {
        foreach ($auditorias as $evento) {
            echo "<tr>";
            echo "<td>" . h($evento['ID']) . "</td>"; 
            echo "<td><strong>" . h($evento['NOMBRE']) . "</strong></td>"; 
            echo "<td>" . h($evento['FECHA']) . "</td>"; 

            // [NUEVO] BLOQUE PARA MOSTRAR SÓLO EL MOTIVO
     
            
            // SE DECODIFICA EL JSON
            $detalle_array = json_decode($evento['DETALLE'], true);
            
            // EXTRAEMOS MOTIVOS
            $motivo = $detalle_array['AUDITORIA_MOTIVO'] ?? 'N/A';

            // MOTIVO
            echo "<td><small>" . h($motivo) . "</small></td>"; //
            
           
            echo "</tr>";
        }
    }


    echo "</tbody></table>";

    footerHtml(); //
    
    exit; 
}


// [FIN] GESTOR DE ACCIÓN DE AUDITORÍA



// VARIABLES NECESARIAS 
$errores = [];
$producto_id_get = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
$producto_id_post = $_POST['ID'] ?? null;
$producto_id = $producto_id_get ?: $producto_id_post;


// 2. LÓGICA DE BORRADO (POST)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf(); // VERIFICAR TOKEN CSRF, SI FALLA SE DETIENE LA EJECUCIÓN

$simular_fallo = filter_input(INPUT_GET, 'fallo', FILTER_VALIDATE_INT);



    if ($producto_id_post) {
        
        // INICIO DE LA TRANSACCIÓN
        $pdo->beginTransaction(); 

        try {

            // BLOQUE PARA SIMULAR FALLO
           
            if ($simular_fallo) {
               
                throw new Exception("¡FALLO SIMULADO! El borrado no se ejecutará.");
            }

            // MOTIVO FORMULARIO
            $motivo_borrado = filter_input(INPUT_POST, 'motivo_borrado', FILTER_UNSAFE_RAW);
            
            // VALIDACION NO PUEDE ESTAR VACIO
 
            if (empty(trim($motivo_borrado))) {
                throw new Exception("Debe proporcionar un motivo para el borrado. La operación ha sido cancelada.");
            }

            // RECUPERAR PRODUCTOS ANTES DE BORRARSE
            $producto_para_auditoria = leerProductoPorId($pdo, $producto_id_post); 


            if (!$producto_para_auditoria) {
                 throw new Exception("El producto (ID: $producto_id_post) no existe o ya fue borrado.");
            }

          
            // MODIFICACIÓN DEL DETALLE DE AUDITORÍA

            
            // NOMBRE USUARIO OBTENIDO
            $nombre_usuario_auditoria = $_SESSION['user_nombre_usuario'] ?? 'UsuarioDesconocido';

            // SE AÑADE MOTIVO + USUARIO
            $producto_para_auditoria['AUDITORIA_MOTIVO'] = trim($motivo_borrado);
            $producto_para_auditoria['AUDITORIA_USUARIO'] = $nombre_usuario_auditoria;

            // MOTIVO ESCRITO EN JSON
            $detalle_auditoria = json_encode($producto_para_auditoria);
            
            // VARIABLE AUDITORIAS
            $nombre_auditoria = sprintf(
                "Usuario '%s' borró: %s",
                $nombre_usuario_auditoria,
                $producto_para_auditoria['NOMBRE']
            );
           
            // BORRAR PRODUCTO
            borrarProducto($pdo, $producto_id_post);
            
            //  REGISTRAR EN AUDITORÍA
        
            registrarAuditoria($pdo, $nombre_auditoria, $detalle_auditoria); //

            // CONFIRMAMOS (COMMIT) 
            $pdo->commit(); 
            
            $titulo_pagina = "Producto Borrado";
            headerHtml($titulo_pagina); 

            // ESPERA 10 SEGUNDOS PARA BORRAR
            echo '<meta http-equiv="refresh" content="10;url=items_list.php?exito=borrado">';

            // Mensaje de éxito
            echo "<div class='success' style='text-align: center; padding: 20px;'>";
            echo "<h2><i class='fa-solid fa-check-circle'></i> ¡Borrado con éxito!</h2>";
            echo "<p>El producto ha sido eliminado permanentemente.</p>";
            echo "<p>Serás redirigido al listado en 10 segundos...</p>";
            echo "<hr>";
            echo "<a href='items_list.php?exito=borrado'>Volver al listado ahora</a>";
            echo "</div>";

            footerHtml(); 
            exit;

        } catch (Exception $e) {
            
            // ROLLBACK CON EXCEPCION
            $pdo->rollBack(); 
            
            // GUARDAR ERROR
            // GUARDAR ERROR
            $errores[] = "Error al borrar el producto: " . $e->getMessage();
        
        }

    } else {
        // SI NO HAY ID EN EL POST, REDIRIGIR
        header('Location: items_list.php?error=generico');
        exit;
    }
}


// 3. LÓGICA DE CARGA (GET o si hay error POST)


if (!$producto_id) {
    // SI NO HAY ID
    header('Location: items_list.php?error=no_id');
    exit;
}

// CARGAR PRODUCTO
$producto = leerProductoPorId($pdo, $producto_id);

if (!$producto) {
    // REDIRIGIR ID INVALIDO
    header('Location: items_list.php?error=no_existe');
    exit;
}


$marcas = listarMarcas($pdo);


// 4. MOSTRAR LA VISTA
$titulo_pagina = "Confirmar Borrado: " . h($producto['NOMBRE']);
headerHtml($titulo_pagina);
?>


<?php
// MOSTRAR ERROR
if (!empty($errores)):
    echo "<div class'error'><ul>";
    foreach ($errores as $error) {
        echo "<li>" . h($error) . "</li>";
    }
    echo "</ul></div>";
endif;
?>

<div class="alert error">
    <h2 style="margin-top: 0;"><i class="fa-solid fa-triangle-exclamation"></i> ¡Atención!</h2>
    <p>Estás a punto de borrar permanentemente el siguiente producto...
        <strong>¡¡Esta acción no se puede deshacer!!.</strong></p>
    <p style="margin-bottom: 0;">¿Estás seguro de que quieres continuar?</p>
</div>



<form method="post" action="items_delete.php?<?php echo h($_SERVER['QUERY_STRING']); ?>">
    
    <?php csrf_input(); ?> 

    <input type='hidden' name='ID' value='<?php echo h($producto['ID']); ?>'>

    <p>
        <label>Nombre:</label>
        <input type='text' value='<?php echo h($producto['NOMBRE']); ?>' disabled>
    </p>

    <p>
        <label>Categoría:</label>
        <select disabled>
            <?php
            //  ENUM
            $categorias = ['Medicamento', 'Antibiótico','Cuidado personal', 
    'Primeros auxilios', 'Nutricion', 'Vitaminas','Otros'];
            foreach ($categorias as $cat):
                // SELECCION CATEGORIA
                $selected = ($cat === $producto['CATEGORIA']) ? 'selected' : '';
                echo "<option value='" . h($cat) . "' $selected>" . h($cat) . "</option>";
            endforeach;
            ?>
        </select>
    </p>
    
    <p>
        <label>Marca:</label>
        <select disabled>
            <option value="">-- Seleccione una marca --</option>
            <?php foreach ($marcas as $marca):
                // SELECT MARCA
                $selected = ($marca['ID'] == $producto['MARCA_ID']) ? 'selected' : '';
                echo "<option value='" . h($marca['ID']) . "' $selected>" . h($marca['NOMBRE']) . "</option>";
            endforeach;
            ?>
        </select>
    </p>

    <p>
        <label>Precio (€):</label>
        <input type='number' value='<?php echo h($producto['PRECIO']); ?>' disabled>
    </p>

    <p>
        <label>Stock Disponible:</label>
        <input type='number' value='<?php echo h($producto['STOCK_DISPONIBLE']); ?>' disabled>
    </p>

    <p>
        <label>
            <input type="checkbox" <?php if ($producto['RECETA']) echo 'checked'; ?> disabled>
            ¿Necesita receta?
        </label>
    </p>

    <p style="margin-top: 20px;">
        <label for="motivo_borrado" class="label-required">Motivo del Borrado (Obligatorio):</label>
        <textarea 
            id="motivo_borrado" 
            name="motivo_borrado" 
            class="form-textarea input-danger" 
            placeholder="Explica brevemente por qué eliminas este producto..." 
            required></textarea>
    </p>
    <p>
        <button type='submit' class="btn-danger">Sí, Borrar Permanentemente</button>
        <a href='items_list.php' style="margin-left: 10px;">Cancelar</a>
    </p>
</form>

<?php
footerHtml();
?>

<?php

