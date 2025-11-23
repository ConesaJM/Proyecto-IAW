<?php
// 1. INCLUIR LOS CEREBROS
// No necesitamos pdo.php si no hacemos consultas SQL
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/utils.php';
require_once __DIR__ . '/../app/csrf.php'; // (CSRF PROTECCION POR TOKEN)


// 2. PROTECCIÓN DE LA PÁGINA 
require_login(); // Un usuario debe estar logueado para guardar sus preferencias

// Definimos nombres de cookies unicos para el usuario.
$cookie_theme = 'user_theme_' . $_SESSION['user_id'];
$cookie_font  = 'user_font_'  . $_SESSION['user_id']; // [NUEVO] Cookie para la fuente

// 3. LÓGICA DE GUARDAR (POST)
// Esto se ejecuta en el momento de que el usuario envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_csrf(); // VERIFICAR TOKEN CSRF, SI FALLA SE DETIENE LA EJECUCIÓN
    
    // 3.1 Guardamos el TEMA
    $tema = $_POST['tema'] ?? 'claro'; 
    setcookie($cookie_theme, $tema, time() + (60*60*24*30), '/');

    // 3.2 Guardamos el TAMAÑO DE FUENTE [NUEVO]
    $fuente = $_POST['fuente'] ?? 'normal';
    setcookie($cookie_font, $fuente, time() + (60*60*24*30), '/');
    
    // 3.3 Redirigir, cumpliendo con el patrón PRG (Post/Redirect/Get)
    header('Location: preferencias.php');
    exit;
}

// 4. LÓGICA DE MOSTRAR (GET)
// Se ejecuta al cargar la página normalmente

// Leemos las cookies actuales
$tema_actual   = $_COOKIE[$cookie_theme] ?? 'claro'; 
$fuente_actual = $_COOKIE[$cookie_font]  ?? 'normal'; // [NUEVO]

// 5. MOSTRAR LA PAGINA A TRAVÉS DE FUNCION HTML DE UTILS.PHP:
headerHtml(''); 
?>

<div style="margin-bottom: 30px;">
    <h1><i class="fa-solid fa-gear"></i> Preferencias de Usuario</h1>
    <p style="color: var(--color-texto-muted);">Aquí puedes personalizar la interfaz de Pharmasphere.</p>
</div>

<form action="preferencias.php" method="POST" class="theme-card">
    <?php csrf_input(); ?> <div style="margin-bottom: 30px; padding-bottom: 25px; border-bottom: 1px solid var(--color-borde);">
        <label style="font-size: 1.1rem; margin-bottom: 15px; display:block; font-weight:600;">
            Tema de la interfaz:
        </label>

        <div class="theme-options">
            <label style="cursor: pointer;">
                <input type="radio" name="tema" value="claro" <?php if ($tema_actual == 'claro') echo 'checked'; ?>>
                <div class="theme-btn">
                    <i class="fa-solid fa-sun"></i>
                    <span>Claro</span>
                </div>
            </label>

            <label style="cursor: pointer;">
                <input type="radio" name="tema" value="oscuro" <?php if ($tema_actual == 'oscuro') echo 'checked'; ?>>
                <div class="theme-btn">
                    <i class="fa-solid fa-moon"></i>
                    <span>Oscuro</span>
                </div>
            </label>
        </div>
    </div>

    <div>
        <label style="font-size: 1.1rem; margin-bottom: 15px; display:block; font-weight:600;">
            Tamaño del texto:
        </label>
        
        <div class="theme-options">
            <label style="cursor: pointer;">
                <input type="radio" name="fuente" value="normal" <?php if ($fuente_actual == 'normal') echo 'checked'; ?>>
                <div class="theme-btn">
                    <i class="fa-solid fa-font" style="font-size: 1.2rem;"></i> <span>Normal</span>
                </div>
            </label>

            <label style="cursor: pointer;">
                <input type="radio" name="fuente" value="grande" <?php if ($fuente_actual == 'grande') echo 'checked'; ?>>
                <div class="theme-btn">
                    <i class="fa-solid fa-font" style="font-size: 2.2rem;"></i> <span>Grande</span>
                </div>
            </label>
        </div>
    </div>
    
    <p style="margin-top: 30px;">
        <button type="submit" class="btn-save-prefs">Guardar Preferencias</button>
    </p>
</form>

<!-- 7. CERRAR LA PÁGINA CON FOOTERHTML() DE UTILS.PHP -->
<?php
footerHtml(); 
?>