<?php

	require_once "main.php";
    
    /*== Almacenando datos ==*/
    $nombre = limpiar_cadena($_POST['categoria_nombre']);
    $ubicacion = limpiar_cadena($_POST['categoria_ubicacion']);

    /*== Verificando campos obligatorios ==*/
    if($nombre == ""){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
        include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php";  
        exit();
    }

    /*== Verificando integridad de los datos ==*/
    if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{2,50}", $nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; 
        exit();
    }

    if($ubicacion != ""){
    	if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{2,150}", $ubicacion)){
	        echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                La UBICACION no coincide con el formato solicitado
	            </div>
	        ';
	        include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php";// Agregar botón de regresar
	        exit();
	    }
    }

    /*== Verificando nombre ==*/
    $check_nombre = conexion();
    $check_nombre = $check_nombre->query("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
    if($check_nombre->rowCount() > 0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE ingresado ya se encuentra registrado, por favor elija otro
            </div>
        ';
        include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php";
        exit();
    }
    $check_nombre = null;

    /*== Guardando datos ==*/
    $guardar_categoria = conexion();
    $guardar_categoria = $guardar_categoria->prepare("INSERT INTO categoria(categoria_nombre, categoria_ubicacion) VALUES(:nombre, :ubicacion)");

    $marcadores = [
        ":nombre" => $nombre,
        ":ubicacion" => $ubicacion
    ];

    $guardar_categoria->execute($marcadores);

    if($guardar_categoria->rowCount() == 1){
        echo '
            <div class="notification is-info is-light">
                <strong>¡CATEGORIA REGISTRADA!</strong><br>
                La categoría se registró con éxito
            </div>
        ';
        include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php";

    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo registrar la categoría, por favor intente nuevamente
            </div>
        ';
        include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php";
    }
    $guardar_categoria = null;
?>
