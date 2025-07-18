<?php

require_once 'main.php';

// Obtiene el módulo de búsqueda desde el formulario
$modulo_buscador = limpiar_cadena($_POST['modulo_buscador']);

// Define los módulos permitidos
$modulos = ["usuario", "categoria", "producto"];

if (in_array($modulo_buscador, $modulos)) {
    
    // Mapeo de módulos a vistas
    $modulos_url = [
        "usuario" => "user_search",
        "categoria" => "category_search",
        "producto" => "product_search"
    ];

    // El módulo URL correspondiente
    $modulo_url = $modulos_url[$modulo_buscador];

    // Iniciar búsqueda
    if (isset($_POST['txt_buscador'])) {
        $txt = limpiar_cadena($_POST['txt_buscador']);

        if ($txt === "") {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrió un error inesperado!</strong><br>
                    Introduce el término de búsqueda
                </div>
            ';
        } elseif (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $txt)) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrió un error inesperado!</strong><br>
                    El término de búsqueda no coincide con el formato solicitado
                </div>
            ';
        } else {
            // Almacena el término de búsqueda en la sesión
            $_SESSION['busqueda_' . $modulo_buscador] = $txt;
            // Redirige a la vista correspondiente
            header("Location: index.php?vista=$modulo_url", true, 303);
            exit();
        }
    }

    // Eliminar búsqueda
    if (isset($_POST['eliminar_buscador'])) {
        unset($_SESSION['busqueda_' . $modulo_buscador]);
        header("Location: index.php?vista=$modulo_url", true, 303);
        exit();
    }

} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No podemos procesar la petición
        </div>
    ';
}
?>
