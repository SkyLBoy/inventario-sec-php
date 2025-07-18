<?php

require_once 'main.php';

verificarRol('admin');

$user_id_del = limpiar_cadena($_GET['user_id_del']);

/*== Verificando usuario ==*/
$check_usuario = conexion();
$check_usuario = $check_usuario->query("SELECT usuario_id FROM usuario WHERE usuario_id='$user_id_del'");

if ($check_usuario->rowCount() == 1) {
    // Eliminar todos los registros relacionados en la tabla prestamos
    $eliminar_prestamos = conexion();
    $eliminar_prestamos = $eliminar_prestamos->prepare("DELETE FROM prestamos WHERE usuario_id=:id");
    $eliminar_prestamos->execute([":id" => $user_id_del]);

    // Ahora eliminar el usuario
    $eliminar_usuario = conexion();
    $eliminar_usuario = $eliminar_usuario->prepare("DELETE FROM usuario WHERE usuario_id=:id");
    $eliminar_usuario->execute([":id" => $user_id_del]);

    if ($eliminar_usuario->rowCount() == 1) {
        echo '
            <div class="notification is-info is-light">
                <strong>¡USUARIO ELIMINADO!</strong><br>
                Los datos del usuario se eliminaron con éxito
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo eliminar el usuario, por favor intente nuevamente
            </div>
        ';
    }
    $eliminar_usuario = null;
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El USUARIO que intenta eliminar no existe
        </div>
    ';
}
$check_usuario = null;
