<?php
// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/utils.php'; // (3º: Carga nuestras funciones)

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
 <!-- Saludamos al usuario usando php echo -->

<h2>¡Bienvenido, <?php echo h($_SESSION['user_nombre_usuario']); ?>!</h2>
<p>Este es tu panel de gestión de Pharmasphere.</p>
<p>Usa el menú de arriba para navegar por la aplicación.</p>

<?php
// 5. CIERRE DE LA PÁGINA
// Llamamos a la otra función para cerrar </body></html>
// También dentro de utils.php
footerHtml(); 
?>