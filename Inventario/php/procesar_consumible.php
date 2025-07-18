<?php
require_once "main.php"; // Asegúrate de que esta ruta sea correcta
session_start();

// Obtener los datos del formulario
$usuario_id = $_POST['usuario_id'];
$producto_id = $_POST['producto_id'];
$cantidad_eliminar = $_POST['cantidad'];
$persona_nombre = $_POST['persona_nombre'];

if (empty($producto_id) || empty($cantidad_eliminar) || $cantidad_eliminar <= 0 || empty($persona_nombre) || empty($usuario_id)) {
    echo "Datos no válidos.";
    exit();
}

try {
    $conexion = conexion();

    // Obtener la información del producto
    $stmt = $conexion->prepare("SELECT producto_stock, producto_nombre FROM producto WHERE producto_id = :producto_id");
    $stmt->bindParam(':producto_id', $producto_id);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo "Producto no encontrado.";
        exit();
    }

    $stock_actual = $producto['producto_stock'];
    if ($cantidad_eliminar > $stock_actual) {
        echo "No hay suficiente stock para realizar la operación.";
        exit();
    }

    $nuevo_stock = $stock_actual - $cantidad_eliminar;

    // Insertar la transacción en persona_consumible
    $stmt = $conexion->prepare("INSERT INTO persona_consumible (producto_id, usuario_id, cantidad, persona_nombre) VALUES (:producto_id, :usuario_id, :cantidad, :persona_nombre)");
    $stmt->bindParam(':producto_id', $producto_id);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':cantidad', $cantidad_eliminar);
    $stmt->bindParam(':persona_nombre', $persona_nombre);
    $stmt->execute();

    if ($nuevo_stock == 0) {
        // Eliminar el producto si el stock llega a 0
        $stmt = $conexion->prepare("DELETE FROM producto WHERE producto_id = :producto_id");
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->execute();
        echo "Producto eliminado del inventario porque el stock ha llegado a 0.";
    } else {
        // Actualizar el stock
        $stmt = $conexion->prepare("UPDATE producto SET producto_stock = :nuevo_stock WHERE producto_id = :producto_id");
        $stmt->bindParam(':nuevo_stock', $nuevo_stock);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->execute();
        echo "Stock actualizado correctamente. Nuevo stock: " . $nuevo_stock;

        // Obtener el nombre del usuario
        $stmt_usuario = $conexion->prepare("SELECT usuario_nombre FROM usuario WHERE usuario_id = :usuario_id");
        $stmt_usuario->bindParam(':usuario_id', $usuario_id);
        $stmt_usuario->execute();
        $usuario_nombre = $stmt_usuario->fetchColumn();

        // Redirigir a la página de impresión de consumibles con los nombres
        header("Location: ../index.php?vista=imprimir_consumible&producto_nombre=" . urlencode($producto['producto_nombre']) . "&usuario_nombre=" . urlencode($usuario_nombre) . "&cantidad=$cantidad_eliminar&persona_nombre=" . urlencode($persona_nombre));
        exit();
    }

} catch (PDOException $e) {
    echo "Error al procesar el producto: " . $e->getMessage();
}
?>
