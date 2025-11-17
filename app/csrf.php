<?php
// app/csrf.php

// Activamos el modo estricto
// Para que todas las declaraciones de tipos sean estrictas
// Si se espera un string, no se aceptará un int, por ejemplo
// De esta manera mejoramos la seguridad y robustez del código
declare(strict_types=1);

// Genera o recupera el token CSRF de la sesión.
// Llama a esta función para OBTENER el token.
function csrf_token(): string
{
    // app/auth.php ya ha iniciado la sesión, 
    // así que no necesitamos session_start() aquí.

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Imprime el <input type="hidden"> con el token CSRF interno.
// Se debe llamar a esta función dentro de las etiquetas <form>.
function csrf_input(): void
{
    echo '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

// Valida el token CSRF enviado por POST.
// Esta función se debe llamar al principio de cualquier script que procese un POST.

function check_csrf(): bool
{
    return isset($_POST['_csrf'], $_SESSION['csrf_token']) 
           && hash_equals($_SESSION['csrf_token'], $_POST['_csrf']);
}

// Función de la validación: Comprueba el token y, si falla,
// detiene la ejecución del script.
function require_csrf(): void
{
    if (!check_csrf()) {
        // En caso de error la petición se detiene aquí
        // Y se envía un código de estado 419
        http_response_code(419); // 419 = "Page Expired" 
        exit('Error: Petición no válida. Por favor, recargue la página e inténtelo de nuevo.');
    }
}
?>