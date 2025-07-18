<?php

require_once "main.php";

/*== Conexion a la base de datos ==*/
$guardar_usuario = conexion();

if ($guardar_usuario === null) {
    echo "Error: No se pudo establecer la conexión a la base de datos.";
    exit();
}

/*== Almacenando datos ==*/
$nombre = limpiar_cadena($_POST['usuario_nombre']);
$apellido = limpiar_cadena($_POST['usuario_apellido']);
$usuario = limpiar_cadena($_POST['usuario_usuario']);
$email = limpiar_cadena($_POST['usuario_email']);
$clave_1 = limpiar_cadena($_POST['usuario_clave_1']);
$clave_2 = limpiar_cadena($_POST['usuario_clave_2']);
$usuario_rol = limpiar_cadena($_POST['usuario_tipo']);

/*== Verificando campos obligatorios ==*/
if ($nombre == "" || $apellido == "" || $usuario == "" || $clave_1 == "" || $clave_2 == "" || $usuario_rol == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    exit();
}

/*== Validando el rol ==*/
if (!in_array($usuario_rol, ['admin', 'visualizador'])) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El rol seleccionado no es válido
        </div>
    ';
    include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    exit();
}

/*== Verificando integridad de los datos ==*/
if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El NOMBRE no coincide con el formato solicitado
        </div>
    ';
    include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    exit();
}

if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El APELLIDO no coincide con el formato solicitado
        </div>
    ';
    include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    exit();
}

if (verificar_datos("[a-zA-Z0-9]{4,20}", $usuario)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El USUARIO no coincide con el formato solicitado
        </div>
    ';
    include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    exit();
}

if (verificar_datos("[a-zA-Z0-9$@.-]{3,100}", $clave_1) || verificar_datos("[a-zA-Z0-9$@.-]{3,100}", $clave_2)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            Las CLAVES no coinciden con el formato solicitado
        </div>
    ';
    include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    exit();
}

/*== Verificando email ==*/
if ($email != "") {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $check_email = conexion();
        $check_email = $check_email->query("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
        if ($check_email->rowCount() > 0) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrió un error inesperado!</strong><br>
                    El correo electrónico ingresado ya se encuentra registrado, por favor elija otro
                </div>
            ';
            include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
            exit();
        }
        $check_email = null;
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                Ha ingresado un correo electrónico no válido
            </div>
        ';
        include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
        exit();
    }
}

/*== Verificando usuario ==*/
$check_usuario = conexion();
$check_usuario = $check_usuario->query("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
if ($check_usuario->rowCount() > 0) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            El USUARIO ingresado ya se encuentra registrado, por favor elija otro
        </div>
    ';
    include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    exit();
}
$check_usuario = null;

/*== Verificando claves ==*/
if ($clave_1 != $clave_2) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            Las CLAVES que ha ingresado no coinciden
        </div>
    ';
    include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    exit();
} else {
    $clave = password_hash($clave_1, PASSWORD_BCRYPT, ["cost" => 10]);
}

/*== Guardando datos ==*/
try {
    $guardar_usuario = $guardar_usuario->prepare("INSERT INTO usuario(usuario_nombre, usuario_apellido, usuario_usuario, usuario_clave, usuario_email, usuario_rol) VALUES(:nombre, :apellido, :usuario, :clave, :email, :rol)");

    $marcadores = [
        ":nombre" => $nombre,
        ":apellido" => $apellido,
        ":usuario" => $usuario,
        ":clave" => $clave,
        ":email" => $email,
        ":rol" => $usuario_rol 
    ];

    $guardar_usuario->execute($marcadores);

    if ($guardar_usuario->rowCount() == 1) {
        echo '
            <div class="notification is-info is-light">
                <strong>¡USUARIO REGISTRADO!</strong><br>
                El usuario se registró con éxito
            </div>
        ';
        include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                No se pudo registrar el usuario, por favor intente nuevamente
            </div>
        ';
        include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
    }
} catch (PDOException $e) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            Error al registrar el usuario: ' . $e->getMessage() . '
        </div>
    ';
} finally {
    $guardar_usuario = null;
}
