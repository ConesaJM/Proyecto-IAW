<?php
// 1. INCLUIR LAS VALIDACIONES Y CONEXIONES A BD
require_once __DIR__ . '/../app/auth.php'; // (1º: Inicia la sesión)
require_once __DIR__ . '/../app/pdo.php';   // (2º: Conecta a la BD)
require_once __DIR__ . '/../app/style.php'; // (3º: Carga los estilos CSS)
require_once __DIR__ . '/../app/utils.php'; // (4º: Carga nuestras funciones)
require_once __DIR__ . '/../app/csrf.php'; // (5º CSRF PROTECCION POR TOKEN)

// 2. SEGURIDAD
require_login();

// Verificar si es administrador. Si no lo es, fuera.
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'Administrador') {
    header('Location: items_list.php');
    exit;
}

$errores = [];
$mensaje = '';

// 3. PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// COMPROBACIÓN CSRF CORRECT
    require_csrf();

    // Recoger datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'Usuario';

    // Validaciones básicas
    if (empty($nombre)) {
        $errores[] = "El nombre de usuario es obligatorio.";
    }
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria.";
    }
    
    // Si no hay errores, intentamos guardar
    if (empty($errores)) {
        
        // Usamos la función buscarUsuarioPorNombre que está en tu utils.php
        if (buscarUsuarioPorNombre($pdo, $nombre)) {
            $errores[] = "Ese nombre de usuario ya existe.";
        } else {
            // Cifrar contraseña (¡Nunca guardar en texto plano!)
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Usamos la función crearUsuario que está en tu utils.php
            // crearUsuario($pdo, $nombre, $hash, $rol)
            if (crearUsuario($pdo, $nombre, $password_hash, $rol)) {
                $mensaje = "Usuario <strong>" . h($nombre) . "</strong> creado correctamente.";
                // Limpiamos los campos
                $nombre = '';
            } else {
                $errores[] = "Error al guardar en la base de datos.";
            }
        }
    }
}

// 4. MOSTRAR VISTA (Usando tu headerHtml de utils.php)
headerHtml("");
?>

<div style="max-width: 600px; margin: 0 auto;">

    <h1 style="text-align: center;">Nuevo Usuario</h1>

    <?php if (!empty($mensaje)): ?>
        <div class="alert success"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div class="alert error">
            <?php foreach ($errores as $error): ?>
                <div><i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        
        <!-- INCLUYE CSRF TOKEN -->
        <?php csrf_input(); ?>

        <label for="nombre">Nombre de Usuario:</label>
        <input type="text" 
               id="nombre" 
               name="nombre" 
               value="<?= h($nombre ?? '') ?>" 
               placeholder="Ej: nuevo_admin" 
               required>

        <label for="password">Contraseña:</label>
        <input type="password" 
               id="password" 
               name="password" 
               placeholder="Escribe la contraseña..." 
               required>

        <label for="rol">Rol de permisos:</label>
        <select id="rol" name="rol">
            <option value="Usuario">Usuario (Cliente)</option>
            <option value="Administrador">Administrador (Acceso total)</option>
        </select>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-save-prefs" style="width: 100%; font-size: 1.1rem;">
                <i class="fa-solid fa-user-plus"></i> Confirmar Creación
            </button>
        </div>

    </form>
</div>

<?php
footerHtml();
?>