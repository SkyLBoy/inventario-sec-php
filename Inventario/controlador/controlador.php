<?php

ob_start(); // Inicia el buffer de salida

// Obtener la vista solicitada o asignar "login" por defecto
$vista = isset($_GET['vista']) && $_GET['vista'] !== "" ? $_GET['vista'] : "login";

// Verificar el estado de la sesión
if (empty($_SESSION['id']) || empty($_SESSION['usuario'])) {
    $vista = 'login'; // Redirigir a login si la sesión no está activa
}

// Mapa de vistas disponibles
$vistasDisponibles = [
    'login',
    'home',
    'procesar_prestamo',
    'historial',
    'consumibles',
    'consumibles_lista',
    'consumibles_filtrados',
    'consumibles_historial',
    'product_img',
    'procesar_consumible',
    'imprimir_consumible',
    'imprimir_prestamo',
    'user_new',
    'user_list',
    'user_update',
    'user_search',
    'category_new',
    'category_list',
    'category_search',
    'producto_guardar',
    'producto_eliminar',
    'producto_actualizar',
    'product_new',
    'product_update',
    'product_list',
    'product_category',
    'product_search',
    'prestamo',
    'prestamos_filtrados',
    'prestamo_list',
    'devolucion',
    'logout',
];

// Verificar si la vista solicitada es válida
if (in_array($vista, $vistasDisponibles)) {
    include "./vistas/{$vista}.php"; // Asegúrate de que esta ruta sea correcta
} else {
    include "./vistas/404.php"; // Vista 404 si no existe
}

ob_end_flush(); // Envía todo el contenido almacenado en el buffer
