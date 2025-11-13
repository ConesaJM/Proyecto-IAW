<?php
// 1. INCLUIR LOS CEREBROS
// No necesitamos pdo.php si no hacemos consultas SQL
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/utils.php';

// 2. PROTECCIÓN DE LA PÁGINA 
require_login(); // Un usuario debe estar logueado para guardar sus preferencias

// 3. LÓGICA DE GUARDAR (POST)
// Esto se ejecuta en el momento de que el usuario envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // (Aquí iría validación CSRF en un futuro)
    
    // 3.1 Primero de todo crearemos una variable para el tema
    $tema = $_POST['tema'] ?? 'claro'; // 'claro' por defecto

    // 3.2 Guardamos la cookie
    setcookie(
        name: 'user_theme',       // Nombre de la cookie
        value: $tema,              // Valor de la cookie, por defecto es 'claro'
        expires_or_options: time() + (60*60*24*30), // Expiración de la cookie (30 días)
        path: '/'                 // Path, disponible en toda la web
    );
    
    // 3.3 Redirigir, cumpliendo con el patrón PRG (Post/Redirect/Get)
    // Redirigimos de vuelta a esta misma página con GET.
    // Esto evita el reenvío del formulario si el usuario recarga.
    header('Location: preferencias.php');
    exit;
}

// 4. LÓGICA DE MOSTRAR (GET)
// Se ejecuta al cargar la página normalmente

// Creamos una nueva variable donde almacenaremos el valor de la cookie
// Para posteriormente, leer la cookie actual para saber qué opción marcar
$tema_actual = $_COOKIE['user_theme'] ?? 'claro'; 

// 5. MOSTRAR LA PAGINA A TRAVÉS DE FUNCION HTML DE UTILS.PHP:
headerHtml('Preferencias de Usuario');
?>

<!-- 6. FORMULARIO DE PREFERENCIAS -->
<!------------------------------------------------------------------------------------------------------------------------------------->
<p>Aquí puedes cambiar el tema visual de la aplicación Pharmasphere.</p>

<form action="preferencias.php" method="POST">
    <p>
        <label for="tema">Tema de la interfaz:</label>
        <select name="tema" id="tema">
            <option value="claro" <?php if ($tema_actual == 'claro') echo 'selected'; ?>>
                Tema Claro
            </option>
            <option value="oscuro" <?php if ($tema_actual == 'oscuro') echo 'selected'; ?>>
                Tema Oscuro
            </option>
        </select>
    </p>
    
    <p>
        <button type="submit">Guardar Preferencias</button>
    </p>
</form>
<!------------------------------------------------------------------------------------------------------------------------------------->

<!-- 7. CIERRE DE LA PÁGINA CON FUNCION DE UTILS.PHP -->
<?php
footerHtml(); 
?>