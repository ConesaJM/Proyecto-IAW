<?php
// ---------------------------------------------------------------------------------------------------------------\\
/* ========= 5. HELPERS DE VISTA =========== */

// Escapa el HTML para prevenir ataques XSS.
function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Imprime el HTML, los estilos CSS y la navegación.
function headerHtml($title = 'Pharmasphere') 
{
    $is_logged_in = isset($_SESSION['user_id']);
    $is_admin = $is_logged_in && isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'Administrador';
    $current_page = basename($_SERVER['SCRIPT_NAME']);

    echo "<!DOCTYPE html><html lang='es'><head><meta charset='utf-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    
    // Fuentes e Iconos
    echo "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css\"/>";
    echo "<link href=\"https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap\" rel=\"stylesheet\">";

    echo "<style>
            :root {
                --color-primario: #007BFF;
                --color-primario-hover: #0056b3;
                --color-secundario: #56ac47;
                --color-secundario-hover: #438a36;
                --color-peligro: #dc3545;
                --color-peligro-hover: #b02a37;
                --color-fondo: #f4f7f6;
                --color-card: #ffffff;
                --color-borde: #e0e0e0;
                --color-texto: #212529;
                --color-texto-muted: #6c757d;
                --ancho-max: 1200px;
                --radio-borde: 12px;
                --sombra: 0 4px 12px rgba(0, 0, 0, 0.05);
            }

            /* --- MODO OSCURO (Verde Pharmasphere) --- */
            body.tema-oscuro { 
                --color-fondo: #121212;
                --color-card: #1e1e1e;
                --color-texto: #e0e0e0;
                --color-texto-muted: #a0a0a0;
                --color-borde: #333;
                
                /* AQUÍ EL CAMBIO: El color principal ahora es VERDE en modo oscuro */
                --color-primario: #56ac47; 
                --color-primario-hover: #438a36;
                
                /* El secundario (acciones positivas) se mantiene igual o se ajusta */
                --color-secundario: #56ac47; 
            }

            body { 
                font-family: 'Outfit', sans-serif; 
                background-color: var(--color-fondo);
                color: var(--color-texto);
                margin: 0;
                padding-top: 80px;
                min-height: 100vh;
                display: flex; flex-direction: column;
            }

            /* --- TAMAÑO DE FUENTE --- */
            body.fuente-grande {
                font-size: 1.15rem; /* Aumenta el tamaño base un 15% */
            }
            /* Ajuste para que los iconos no se desmadren */
            body.fuente-grande i {
                font-size: 1.1em; 
            }

            /* 1. La clase general para la fuente grande */
            body.fuente-grande {
                font-size: 1.15rem; /* Aumenta el tamaño base un 15% */
            }

            /* 2. Forzamos la escala de los enlaces de navegación y del texto del usuario */
            body.fuente-grande .nav-link,
            body.fuente-grande .nav-right strong {
                /* Forzamos un tamaño basado en el nuevo font-size del body */
                font-size: 1.15em !important; 
            }

            /* 3. Aseguramos que los iconos también se vean grandes, pero controlados */
            body.fuente-grande .nav-right i {
                font-size: 1.4em !important; /* Subimos un poco el tamaño de los iconos (fa-xl) */
            }
            
            a { text-decoration: none; transition: 0.2s; }
            h1, h2, h3 { font-weight: 700; letter-spacing: -0.5px; margin-bottom: 1rem; }

            /* --- BARRA DE BÚSQUEDA COMPACTA (Izquierda) --- */
            /* Usamos 'form.search-form' para ganar prioridad sobre la regla general de formularios */
            form.search-form {
                display: flex;
                align-items: center;
                gap: 10px;

                /* 1. FORZAR POSICIÓN A LA IZQUIERDA */
                /* 'margin: 0' anula el 'margin: 0 auto' que lo centraba */
                margin: 0 0 20px 0 !important; 
                
                /* 2. TAMAÑO CONTENIDO */
                max-width: 500px !important;   /* Ancho máximo restringido */
                width: 100%;

                /* 3. ESTÉTICA MÁS FINA (Menos relleno) */
                padding: 12px !important;      /* Mucho más fino que los 30px por defecto */
                background-color: var(--color-card);
                border-radius: var(--radio-borde);
                border: 1px solid var(--color-borde);
                box-shadow: var(--sombra);
            }

            .search-input-group {
                flex-grow: 1;
                position: relative;
            }

            /* Estilos del input interno */
            form.search-form input {
                margin-bottom: 0 !important; 
                height: 38px;             /* Altura compacta */
                font-size: 0.95rem;
            }

            /* Estilos del botón interno */
            form.search-form button {
                height: 38px;             /* Misma altura que el input */
                padding: 0 20px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                white-space: nowrap;
            }
            /* NAVBAR */
            .navbar {
                position: fixed; top: 0; left: 0; right: 0; height: 70px;
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--color-borde);
                display: flex; align-items: center; justify-content: space-between;
                padding: 0 4%; z-index: 1000;
                box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            }

            /* --- LOGO PRINCIPAL --- */
            .navbar-logo-img {
                height: 58px;            /* Mucho más grande (casi llena la barra de 70px) */
                width: auto;             /* Mantiene la proporción */
                vertical-align: middle;
                transition: transform 0.3s ease, filter 0.3s ease; /* Animación suave */
                
                /* Opcional: Una sombra suave para que se separe del fondo */
                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
            }

            .navbar-logo-img:hover {
                transform: scale(1.1);   /* Efecto zoom al pasar el ratón */
                filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2)); /* Sombra más fuerte */
            }

            body.tema-oscuro .navbar { background: rgba(30, 30, 30, 0.9); }
            .nav-left, .nav-right { display: flex; align-items: center; gap: 15px; }
            /* --- ESTILOS GENERALES DE LOS ENLACES DEL MENÚ --- */
            .nav-link {
                color: var(--color-texto-muted); 
                font-weight: 600; 
                font-size: 1.10rem;
                padding: 8px 16px; 
                border-radius: 50px; 
                
                /* Transición suave para el tamaño (transform) y el color */
                transition: all 0.2s ease; 
                
                /* Aseguramos que el icono y texto estén alineados */
                display: inline-flex; 
                align-items: center;
                justify-content: center;
            }

            /* --- HOVER GENERAL (Escala + Color del Tema) --- */
            .nav-link:hover { 
                color: var(--color-primario); 
                background-color: color-mix(in srgb, var(--color-primario), transparent 90%);
                
                /* AQUÍ EL EFECTO ZOOM QUE PEDÍAS: */
                transform: scale(1.08); 
            }

            /* --- ENLACE ACTIVO (El que indica dónde estás) --- */
            .nav-link.active {
                background-color: var(--color-primario); 
                color: white !important;
                box-shadow: 0 4px 10px color-mix(in srgb, var(--color-primario), transparent 60%);
            }
            /* Al hacer hover sobre el activo, también hacemos zoom */
            .nav-link.active:hover {
                transform: scale(1.08);
            }

            a.nav-link[href='logout.php'] {
                color: var(--color-peligro) !important;
                
                /* AJUSTE VISUAL: Al ser solo un icono, reducimos el padding lateral 
                   para que el fondo al hacer hover sea más cuadrado/redondo y no un óvalo largo */
                padding: 15px 8px; 
            }

            /* HOVER ESPECÍFICO PARA LOGOUT */
            a.nav-link[href='logout.php']:hover {
                /* Fondo ROJO: Ajustado a un tono suave (15% de opacidad) */
                background-color: rgba(220, 53, 69, 0.15) !important;
                
                /* Icono rojo intenso */
                color: var(--color-peligro) !important;
                
                /* ZOOM: Mantenemos el escalado, pero QUITAMOS la rotación */
                transform: scale(1.15); 
                
                /* Opcional: Una sombra roja muy suave para darle profundidad */
                box-shadow: 0 0 10px rgba(220, 53, 69, 0.2);
            }
            body.tema-oscuro .nav-link.active { 
            color: #121212 !important; 
            box-shadow: 0 4px 10px rgba(86, 172, 71, 0.3);
            }

            /* TABLAS */
            table {
                width: 100%; border-collapse: separate; border-spacing: 0;
                background-color: var(--color-card); border-radius: var(--radio-borde);
                overflow: hidden; margin-top: 20px; box-shadow: var(--sombra);
                border: 1px solid var(--color-borde);
            }
            th {
                background-color: var(--color-primario); /* Azul en Claro, Verde en Oscuro */
                color: white;  /* Texto blanco para contrastar */
                padding: 15px;
                text-align: left;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.85rem;
                letter-spacing: 0.5px;
                border-bottom: none; /* Quitamos borde inferior porque ya tiene fondo */
            }
            td { padding: 15px; border-bottom: 1px solid var(--color-borde); vertical-align: middle; }
            body.tema-oscuro tbody tr:nth-child(even) { background-color: rgba(255,255,255,0.03); }

            /* ALERTAS (CORREGIDAS) */
            .alert {
                padding: 15px; margin: 20px auto; max-width: 800px; width: 90%;
                border-radius: var(--radio-borde); font-weight: 500;
                animation: fadeIn 0.5s ease-out; border: 1px solid transparent;
            }
            .alert.success {
                background-color: var(--color-secundario); color: white;
                border-color: var(--color-secundario-hover);
            }
            .alert.success::before {
                font-family: 'Font Awesome 6 Free'; font-weight: 900; content: '\\f00c'; margin-right: 10px;
            }

            /* --- ESTILO DE ERROR (IMPORTANTE) --- */
            /* Cubrimos tanto .alert.error como .error suelto por si acaso */
            .alert.error, .error {
                background-color: #f8d7da;
                color: #842029; /* Rojo oscuro para modo claro */
                border-color: #f5c6cb;
            }
            /* MODO OSCURO: Forzamos texto rojo brillante para que se lea */
            body.tema-oscuro .alert.error, body.tema-oscuro .error {
                background-color: rgba(220, 53, 69, 0.15) !important;
                color: #ff6b6b !important;
                border: 1px solid #ff6b6b !important;
            }

            /* STOCKS */
            .stock-low { 
                color: var(--color-peligro); 
                /* Aplicamos la animación solo al stock bajo */
                animation: pulse-low 1.5s infinite ease-in-out; 
            }
            .stock-med { color: #fd7e14; }
            .stock-high { color: var(--color-secundario); }

            /* FORMULARIOS */
            form:not(.inline) {
                background-color: var(--color-card); padding: 30px;
                border-radius: var(--radio-borde); border: 1px solid var(--color-borde);
                box-shadow: var(--sombra); max-width: 800px; margin: 0 auto;
            }

            form:not(.inline), .theme-card {
                transition: all 0.3s ease-out;
            }

            /* --- TARJETA DE SELECCIÓN DE TEMA (Alineada a la Izquierda) --- */
            .theme-card {
                /* Caja contenedora */
                background-color: var(--color-card);
                border: 1px solid var(--color-borde);
                border-radius: var(--radio-borde);
                box-shadow: var(--sombra);
                
                /* Tamaño y Posición */
                max-width: 500px; /* Ancho limitado para que no sea gigante */
                width: 100%;
                margin: 0;        /* Alineado a la izquierda (quita el margin auto) */
                padding: 30px;
            }

            /* Contenedor flexible para los dos botones */
            .theme-options {
                display: flex;
                gap: 15px; /* Espacio entre los botones */
                margin-top: 15px;
            }

            /* Los Botones de Opción (Estilo visual) */
            .theme-btn {
                flex: 1; /* Ambos ocupan el mismo ancho */
                display: flex;
                flex-direction: column; /* Icono arriba, texto abajo */
                align-items: center;
                justify-content: center;
                padding: 20px;
                border: 2px solid var(--color-borde);
                border-radius: 12px;
                background-color: var(--color-fondo);
                color: var(--color-texto-muted);
                cursor: pointer;
                transition: all 0.2s ease;
                font-weight: 600;
            }

            .theme-btn i {
                font-size: 2rem; /* Icono grande */
                margin-bottom: 10px;
            }

            /* Efecto Hover (Al pasar el ratón) */
            .theme-btn:hover {
                border-color: var(--color-primario);
                color: var(--color-primario);
                /* Fondo muy suave del color del tema */
                background-color: color-mix(in srgb, var(--color-primario), transparent 95%);
                transform: translateY(-2px);
            }

            /* ESTADO SELECCIONADO (Cómo se ve el activo) */
            /* Cuando el input radio está marcado, cambiamos el estilo del div .theme-btn hermano */
            input[type='radio']:checked + .theme-btn {
                border-color: var(--color-primario);
                background-color: color-mix(in srgb, var(--color-primario), transparent 90%);
                color: var(--color-primario);
                box-shadow: 0 0 0 2px color-mix(in srgb, var(--color-primario), transparent 80%);
            }

            /* Ocultamos los circulitos de radio nativos feos */
            input[type='radio'] {
                display: none; 
            }
            
            /* Ajuste del botón de guardar */
            .btn-save-prefs {
                margin-top: 25px;
                width: 100%;
                padding: 12px;
                font-size: 1rem;
            }
            
            /* --- REGLA PARA ETIQUETAS (Estaba faltando) --- */
            label {
                display: block; /* Para que nombre, precio, etc. queden encima del input */
                margin-bottom: 8px;
                font-weight: 600;
                font-size: 0.9rem;
            }

            input, select, textarea {
                width: 100%; padding: 12px; margin-bottom: 20px; /* Añadido margen inferior */
                border: 1px solid var(--color-borde); border-radius: 8px;
                background: var(--color-fondo); color: var(--color-texto);
                box-sizing: border-box; transition: 0.3s;
            }
            /* --- FOCUS EN INPUTS Y SELECTS --- */
            input:focus, 
            select:focus, 
            textarea:focus {
                /* 1. El borde toma el color fuerte del tema (Verde o Azul) */
                border-color: var(--color-primario);
                
                /* 2. Quitamos el outline nativo del navegador */
                outline: none;
                
                /* 3. AQUÍ EL ARREGLO: */
                /* Antes tenías un azul fijo rgba(0,123,255...). */
                /* Ahora usamos el color del tema diluido (85% transparente) para el resplandor */
                box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-primario), transparent 85%);
            }
            
            /* --- FIX PARA CHECKBOX --- */
            /* Clase especial para agrupar checkbox y texto horizontalmente */
            .checkbox-group {
                display: flex;
                align-items: center;
                margin-bottom: 20px;
                background: rgba(0,0,0,0.02);
                padding: 10px;
                border-radius: 8px;
            }
            /* Hacemos que dentro de este grupo, el label no sea bloque */
            .checkbox-group label {
                display: inline-block;
                margin-bottom: 0;
                margin-right: 15px;
                cursor: pointer;
            }
            .checkbox-group input[type=checkbox] {
                width: 20px !important;
                height: 20px;
                margin: 0;
                cursor: pointer;
            }

            input[type=checkbox] {
                width: auto !important;   /* Ocupa solo su espacio real */
                display: inline-block;    
                vertical-align: middle;   /* Alineado al medio con el texto */
                margin-left: 10px;        /* Separación con el texto */
                margin-top: 17px;            
                transform: scale(1.3);    /* Un poco más grande */
                cursor: pointer;          /* Manita al pasar el ratón */
                
                /* TRUCO VISUAL: Ajuste fino para que quede centrado perfecto */
                position: relative; 
                top: -1px; 
            }

            /* --- ESTO QUITA EL BORDE AZUL AL HACER CLIC --- */
            input[type=checkbox]:focus {
                outline: none !important;      /* Quita la línea de contorno */
                box-shadow: none !important;   /* Quita el resplandor azul */
                border-color: var(--color-borde); /* Mantiene el borde gris suave */
            }

            /* Opcional: Asegurar que el label no fuerce saltos raros */
            label {
                vertical-align: middle;
            }

            /* BOTONES */
            .btn-table {
                padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 600;
                display: inline-flex; align-items: center; gap: 6px; border: 1px solid transparent;
            }
            .btn-edit {
                background-color: color-mix(in srgb, var(--color-primario), transparent 85%);
                
                /* 2. TEXTO: Color sólido del tema */
                color: var(--color-primario);
                
                /* 3. Sin bordes */
                border: none;
                
                /* Estructura */
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 0.85rem;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: all 0.3s ease;
            }

            .btn-edit, button, .theme-btn {
                transition: all 0.2s ease-in-out; 
            }

            /* Estado al HACER CLICK (Hundimiento) */
            .btn-edit:active, 
            button:active, 
            .nav-link:active,
            .pagination a:active, 
            .pagination strong:active {
                transform: scale(0.95) !important; /* Se hace 5% más pequeño, simulando la presión */
                filter: brightness(0.9);           /* Se oscurece ligeramente */
            }

            .btn-edit:hover {
                /* AL HOVER: El fondo se rellena con el color sólido */
                background-color: var(--color-primario);
                
                /* El texto pasa a blanco para contrastar */
                color: white;
                
                /* Efectos de brillo y zoom */
                transform: scale(1.05);
                cursor: pointer;
                text-decoration: none;
            }
            .btn-delete { background-color: rgba(239, 68, 68, 0.15); color: #ef4444; transition: all 0.3s ease;}
            .btn-delete:hover { background-color: #ef4444; color: white; transform: scale(1.05);}
            .btn-buy { background: var(--color-secundario); color: white; padding: 6px 12px; border-radius: 8px; }
            .btn-buy:hover { background: var(--color-secundario-hover); }
            button[type='submit'] { 
                padding: 12px 24px; background: var(--color-primario); color: white; 
                border: none; border-radius: 8px; font-weight: 600; cursor: pointer; 
            }
            button[type='submit']:hover { background: var(--color-primario-hover); }

            /* --- ESTILOS DE PELIGRO (Delete) --- */

            /* 1. INPUT/TEXTAREA DE PELIGRO */
            .input-danger {
                border-color: var(--color-peligro) !important; /* Borde rojo suave siempre */
            }
            .input-danger:focus {
                /* Borde rojo intenso al hacer clic */
                border-color: var(--color-peligro) !important; 
                /* Resplandor rojo (en vez de verde/azul) */
                box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-peligro), transparent 85%) !important;
                outline: none;
            }

            /* 2. BOTÓN DE PELIGRO (Rojo Sólido) */
            button.btn-danger {
                background-color: var(--color-peligro) !important; /* Rojo base */
                color: white !important;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            button.btn-danger:hover {
                /* CAMBIO RADICAL: Mezclamos el rojo con un 40% de NEGRO puro */
                /* Esto crea un granate muy profundo y serio */
                background-color: color-mix(in srgb, var(--color-peligro), black 40%) !important;
                
                /* Quitamos el filtro de brillo porque el color ya es muy oscuro de por sí */
                filter: none; 
                
                /* Mantenemos el zoom para el feedback táctil */
                transform: scale(1.05);
                cursor: pointer;
            }
            
            /* --- PAGINACIÓN MODERNA --- */
            .pagination {
                display: flex;             /* Pone los números en fila */
                align-items: center;       /* Centrado vertical */
                justify-content: center;   /* Centrado horizontal en la página */
                gap: 8px;                  /* Espacio entre botones */
                margin-top: 30px;          /* Separación con la tabla */
                margin-bottom: 20px;
                flex-wrap: wrap;           /* Si hay muchas páginas, que bajen de línea */
            }

            /* Estilo para los botones (enlaces y el activo) */
            .pagination a, 
            .pagination strong {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 40px;           /* Ancho mínimo para que sean cuadrados */
                height: 40px;              /* Altura fija */
                padding: 0 10px;
                border-radius: 8px;        /* Bordes redondeados */
                text-decoration: none;
                font-weight: 600;
                font-size: 0.95rem;
                transition: all 0.2s ease;
                
                /* Colores por defecto (Inactivos) */
                background-color: var(--color-card);
                border: 1px solid var(--color-borde);
                color: var(--color-texto);
            }

            /* HOVER: Cuando pasas el ratón por un número */
            .pagination a:hover {
                border-color: var(--color-primario);
                color: var(--color-primario);
                background-color: color-mix(in srgb, var(--color-primario), transparent 90%);
                transform: translateY(-2px); /* Pequeño salto hacia arriba */
            }

            /* ACTIVO: La página actual (strong) */
            .pagination strong {
                background-color: var(--color-primario); /* Color del tema (Azul/Verde) */
                color: white;
                border-color: var(--color-primario);
                box-shadow: 0 4px 10px color-mix(in srgb, var(--color-primario), transparent 60%);
            }

            /* --- FILAS CLICABLES --- */
            tr.clickable-row {
                cursor: pointer; /* Manita al pasar el ratón */
                transition: background-color 0.2s ease;
            }
            
            /* Efecto hover más notorio para indicar interactividad */
            tr.clickable-row:hover {
                background-color: color-mix(in srgb, var(--color-primario), transparent 90%) !important;
            }
            
            /* IMPORTANTE: Que los botones de acción estén por encima */
            tr.clickable-row .btn-table {
                position: relative;
                z-index: 2;
            }

            /* Animación */
            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .main-container { animation: fadeIn 0.5s ease-out; max-width: var(--ancho-max); margin: 0 auto; padding: 0 20px; width: 100%; }
            .footer-bottom { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--color-borde); }

            /* --- ANIMACIÓN PARA STOCK BAJO (Urgencia) --- */
            @keyframes pulse-low {
                0% { transform: scale(0.9); opacity: 1; }
                50% { transform: scale(1.2); opacity: 0.9; }
                100% { transform: scale(0.9); opacity: 1; }
            }
        </style>";

    // Cookie de tema
    // 1. Tema
    $tema_cookie = 'claro';
    // 2. Fuente
    $fuente_cookie = 'normal';

    if (isset($_SESSION['user_id'])) {
        $nombre_cookie_tema = 'user_theme_' . $_SESSION['user_id'];
        $tema_cookie = $_COOKIE[$nombre_cookie_tema] ?? 'claro';

        $nombre_cookie_fuente = 'user_font_' . $_SESSION['user_id'];
        $fuente_cookie = $_COOKIE[$nombre_cookie_fuente] ?? 'normal';
    }

    // APLICAMOS AMBAS CLASES AL BODY
    echo "</head><body class='tema-" . h($tema_cookie) . " fuente-" . h($fuente_cookie) . "'>";

    // Parte izquierda del navbar
    echo "<nav class='navbar'>
            <div class='nav-left'>
                <a href='index.php' style='margin-right: 20px;'>
                    <img src='media/pharmasphere_sinfondo.png' alt='Logo' class='navbar-logo-img'>
                </a>";
                
    if ($is_logged_in) {
        echo "<a href='index.php' class='nav-link " . ($current_page == 'index.php' ? 'active' : '') . "'>Panel</a>";
        echo "<a href='items_list.php' class='nav-link " . ($current_page == 'items_list.php' ? 'active' : '') . "'>Productos</a>";
        
        if ($is_admin) {
            echo "<a href='items_form.php' class='nav-link " . ($current_page == 'items_form.php' ? 'active' : '') . "'>Nuevo</a>";
            echo "<a href='user_form.php' class='nav-link " . ($current_page == 'user_form.php' ? 'active' : '') . "'>Usuarios</a>";
            echo "<a href='items_delete.php?action=auditoria' class='nav-link " . ($current_page == 'items_delete.php' ? 'active' : '') . "'>Auditoría</a>";
        }
    }
    echo "</div>";

    // Parte derecha del navbar
    echo "<div class='nav-right'>";
    if ($is_logged_in) {
        $nombre_usuario = h($_SESSION['user_nombre_usuario'] ?? 'Usuario');
        // Usuario y Preferencias
        echo "<a href='preferencias.php' class='nav-link' title='Configuración'><i class='fa-solid fa-circle-user fa-xl' style='margin-right:5px;'></i> <strong>$nombre_usuario</strong></a>";
        // Logout
        echo "<a href='logout.php' class='nav-link' style='color:var(--color-peligro);' title='Cerrar Sesión'><i class='fa-solid fa-right-from-bracket fa-xl'></i></a>";
    } else {
        echo "<a href='login.php' class='nav-link active'>Iniciar Sesión</a>";
    }
    echo "</div></nav>";

    echo "<div class='main-container'>";

    if ($title) {
        echo "<h1>" . h($title) . "</h1>";
    }
}
// Imprime el pie de página y cierra el HTML
function footerHtml()
{
    echo "<hr><small>Proyecto IAW - Pharmasphere</small>"; 

    // --- INICIO DEL WIDGET DE TAWK.TO ---
    ?>
    
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/691af7ebccfc7c195b6c49fc/1ja8lgs42';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <?php
    // --- FIN DEL WIDGET ---

    echo "</body></html>";
}
?>