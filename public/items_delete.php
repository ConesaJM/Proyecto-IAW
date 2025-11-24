<?php 
// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; 
require_once __DIR__ . '/../app/pdo.php';   
require_once __DIR__ . '/../app/style.php'; 
require_once __DIR__ . '/../app/utils.php'; 
require_once __DIR__ . '/../app/csrf.php'; 

// SOLO EL ADMIN ACCEDE
require_admin();

// [INICIO] GESTOR DE ACCIÓN DE AUDITORÍA
$accion = filter_input(INPUT_GET, 'action');

if ($accion === 'auditoria') {
    
    // FUNCION AUDITORIA
    $auditorias = auditoria_list($pdo);

    // VISTA AUDITORIA
    $titulo_pagina = "Registro de Auditoría";
    headerHtml($titulo_pagina); 
    
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

            // BLOQUE PARA MOSTRAR SÓLO EL MOTIVO
            $detalle_array = json_decode($evento['DETALLE'], true);
            $motivo = $detalle_array['AUDITORIA_MOTIVO'] ?? 'N/A';

            echo "<td><small>" . h($motivo) . "</small></td>"; 
            echo "</tr>";
        }
    }
    echo "</tbody></table>";

    footerHtml(); 
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
    require_csrf(); // VERIFICAR TOKEN CSRF

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
            
            // REGISTRAR EN AUDITORÍA
            registrarAuditoria($pdo, $nombre_auditoria, $detalle_auditoria); 

            // CONFIRMAMOS (COMMIT) 
            $pdo->commit(); 
            
            // 1. Redirección automática nativa del navegador (5 segundos)
            echo '<meta http-equiv="refresh" content="5;url=items_list.php?exito=borrado">';
            
            headerHtml("Producto Borrado"); 
            ?>

            <style>
                .success-wrapper {
                    display: flex; flex-direction: column; align-items: center;
                    justify-content: center; padding: 40px 20px; text-align: center;
                    animation: fadeIn 0.8s ease-out;
                }

                .icon-circle {
                    width: 100px; height: 100px; background-color: #d4edda;
                    border-radius: 50%; display: flex; align-items: center; justify-content: center;
                    margin-bottom: 25px; box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2);
                    animation: popIn 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
                }
                .icon-circle i { color: #28a745; font-size: 3.5rem; }

                .success-title { font-size: 2rem; color: #28a745; margin-bottom: 10px; font-weight: 700; }
                .success-desc { font-size: 1.1rem; color: var(--color-texto-muted); margin-bottom: 30px; }


                .countdown-text {
                    font-size: 1.2rem; font-weight: 600; color: var(--color-texto); margin: 20px 0;
                }
                

                .css-timer::after {
                    content: "5"; 
                    font-size: 1.5rem; color: var(--color-primario); font-weight: 800;
                    padding: 0 5px;
                    animation: countdown-change 5s step-end forwards;
                }


                .progress-bar-bg {
                    width: 100%; max-width: 400px; height: 8px;
                    background-color: #e9ecef; border-radius: 10px;
                    overflow: hidden; margin-bottom: 30px;
                }

                .progress-bar-fill {
                    height: 100%;
                    background-color: var(--color-primario);
                    width: 100%; 
                    border-radius: 10px;
                    /* La barra se encoge en 5 segundos de forma lineal */
                    animation: shrink-bar 5s linear forwards;
                }

                /* ANIMACIONES KEYFRAMES */
                @keyframes popIn {
                    0% { transform: scale(0); opacity: 0; }
                    80% { transform: scale(1.1); opacity: 1; }
                    100% { transform: scale(1); }
                }

                @keyframes shrink-bar {
                    from { width: 100%; }
                    to { width: 0%; }
                }

                /* Animación paso a paso para cambiar el número 5,4,3,2,1,0 */
                @keyframes countdown-change {
                    0% { content: "5"; }
                    20% { content: "4"; }
                    40% { content: "3"; }
                    60% { content: "2"; }
                    80% { content: "1"; }
                    100% { content: "0"; }
                }
            </style>

            <div class="success-wrapper">
                
                <div class="icon-circle">
                    <i class="fa-solid fa-check"></i>
                </div>

                <h1 class="success-title">¡Borrado Exitoso!</h1>
                
                <p class="success-desc">
                    El producto ha sido eliminado permanentemente.<br>
                    El registro de auditoría ha sido actualizado.
                </p>

                <div class="countdown-text">
                    Redirigiendo en <span class="css-timer"></span> segundos...
                </div>

                <div class="progress-bar-bg">
                    <div class="progress-bar-fill"></div>
                </div>

                <a href="items_list.php?exito=borrado" class="btn-buy" style="background-color: var(--color-texto); text-decoration: none; padding: 12px 25px;">
                    <i class="fa-solid fa-arrow-right"></i> Volver ahora mismo
                </a>

            </div>

            <?php
            footerHtml(); 
            exit; 

        } catch (Exception $e) {
            
            // ROLLBACK CON EXCEPCION
            $pdo->rollBack(); 
            $errores[] = "Error al borrar el producto: " . $e->getMessage();
        }

    } else {
        // SI NO HAY ID EN EL POST, REDIRIGIR
        header('Location: items_list.php?error=generico');
        exit;
    }
}


// 3. LÓGICA DE CARGA DE FORMULARIO (GET o si hay error POST)

if (!$producto_id) {
    header('Location: items_list.php?error=no_id');
    exit;
}

// CARGAR PRODUCTO
$producto = leerProductoPorId($pdo, $producto_id);

if (!$producto) {
    header('Location: items_list.php?error=no_existe');
    exit;
}

$marcas = listarMarcas($pdo);


// 4. MOSTRAR LA VISTA DE CONFIRMACIÓN
$titulo_pagina = "Confirmar Borrado: " . h($producto['NOMBRE']);
headerHtml($titulo_pagina);
?>

<?php
// MOSTRAR ERROR
if (!empty($errores)):
    echo "<div class='alert error'><ul>";
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