<?php
session_start(); // Asegúrate de que la sesión esté activa al inicio

// Destruir la sesión
session_destroy();

// Redirigir a la vista de login
if (headers_sent()) {
    echo "<script> window.location.href='index.php?vista=login'; </script>";
    exit(); 
} else {
    header("Location: index.php?vista=login");
    exit(); 
}
