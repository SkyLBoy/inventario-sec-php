<?php
require_once "main.php";

// Obtener el ID del préstamo desde el formulario
$prestamo_id = isset($_POST['prestamo_id']) ? $_POST['prestamo_id'] : null;

if ($prestamo_id) {
    try {
        $pdo = conexion();

        // Obtener detalles del préstamo seleccionado
        $query = $pdo->prepare("SELECT producto_id, cantidad FROM prestamos WHERE prestamo_id = :prestamo_id AND fecha_devolucion IS NULL");
        $query->execute([':prestamo_id' => $prestamo_id]);
        $prestamo = $query->fetch();

        // Verificar si el préstamo existe y no ha sido devuelto aún
        if ($prestamo) {
            $producto_id = $prestamo['producto_id'];
            $cantidad_devuelta = $prestamo['cantidad'];

            // Marcar el préstamo como devuelto
            $update_prestamo = $pdo->prepare("UPDATE prestamos SET fecha_devolucion = NOW() WHERE prestamo_id = :prestamo_id");
            $update_prestamo->execute([':prestamo_id' => $prestamo_id]);

            // Actualizar el stock del producto sumando la cantidad devuelta
            $update_producto = $pdo->prepare("UPDATE producto SET producto_stock = producto_stock + :cantidad_devuelta WHERE producto_id = :producto_id");
            $update_producto->execute([
                ':cantidad_devuelta' => $cantidad_devuelta,
                ':producto_id' => $producto_id
            ]);

            // Registrar la devolución en el historial de movimientos
            $insert_historial = $pdo->prepare("INSERT INTO historial_movimientos (prestamo_id, accion, cantidad, fecha) VALUES (:prestamo_id, 'devolución', :cantidad_devuelta, NOW())");
            $insert_historial->execute([
                ':prestamo_id' => $prestamo_id,
                ':cantidad_devuelta' => $cantidad_devuelta
            ]);

            echo "Devolución realizada exitosamente.";
            include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php";

        } else {
            echo "El préstamo seleccionado no existe o ya ha sido devuelto.";
        }
    } catch (PDOException $e) {
        echo "Error al procesar la devolución: " . $e->getMessage();
    }
} else {
    echo "No se ha seleccionado ningún préstamo para devolver.";
}
?>
