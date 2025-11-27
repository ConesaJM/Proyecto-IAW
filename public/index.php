<?php
// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/style.php'; // (3º: Carga los estilos CSS)
require_once __DIR__ . '/../app/utils.php'; // (4º: Carga nuestras funciones)

// 2. PROTECCIÓN DE LA PÁGINA 
// Esta función de auth.php comprobará si hay una sesión iniciada.
// Si no la hay, redirige a login.php y el script muere aquí.
require_login(); 

// 3. MOSTRAR LA PÁGINA
// Si el script llega aquí, el usuario SÍ está logueado.
// Con la función headerHtml() mostramos el HTML creado dentro de utils.php
headerHtml('Panel Principal - Pharmasphere');

?>

<!-- 4. CONTENIDO DE LA PÁGINA -->

<h2>¡Bienvenido, <?= h($_SESSION['user_nombre_usuario']); ?>!</h2>
<p>Este es tu panel de gestión de <strong>Pharmasphere</strong>. Desde aquí podrás acceder a los módulos de productos, marcas y carrito.</p>

<hr>

<!-- Bloque de resumen -->
<section>
    <h3>Resumen rápido</h3>
    <p>Usa el menú superior para moverte por la aplicación. Algunas cosas que puedes hacer:</p>
    <ul>
        <li>Ver el <strong>listado de productos</strong> disponibles.</li>
        <li>Revisar las marcas registradas.</li>
        <li>Gestionar el carrito de compra.</li>
    </ul>
</section>

<hr>

<!-- Acciones rápidas -->
<section>
    <h3>Accesos rápidos</h3>
    <p>Elige una opción para empezar a trabajar:</p>
    <ul>
        <li><a href="items_list.php">Ir al listado de productos</a></li>
        <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'Administrador'): ?>
            <li><a href="items_form.php">Crear nuevo producto</a></li>
        <?php endif; ?>
    </ul>
</section>

<hr>

<!-- Pequeño panel de “estado” (simple relleno visual) -->
<section>
    <h3>Estado del sistema</h3>
    <table>
        <tr>
            <th>Usuario actual</th>
            <td><?= h($_SESSION['user_nombre_usuario']); ?></td>
        </tr>
        <tr>
            <th>Fecha de acceso</th>
            <td><?= date('d/m/Y H:i'); ?></td>
        </tr>
    </table>
</section>

<?php
// 5. CIERRE DE LA PÁGINA
// Llamamos a la otra función para cerrar </body></html>
// También dentro de utils.php
// 
footerHtml(); 
?>