<?php
include "main.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];
    $persona_nombre = $_POST['persona_nombre'];
    $productos_ids = $_POST['producto_id'];
    $cantidades = $_POST['cantidad'];

    try {
        $db = conexion();
        $db->beginTransaction();

        $productoNombres = [];

        foreach ($productos_ids as $index => $producto_id) {
            $cantidad = (int)$cantidades[$index];

            // Verifica si la persona ya existe
            $personaQuery = $db->prepare("SELECT persona_id FROM persona_prestamo WHERE persona_nombre = ?");
            $personaQuery->execute([$persona_nombre]);
            $persona = $personaQuery->fetch(PDO::FETCH_ASSOC);

            if (!$persona) {
                // Si la persona no existe, la inserta
                $insertPersona = $db->prepare("INSERT INTO persona_prestamo (persona_nombre) VALUES (?)");
                $insertPersona->execute([$persona_nombre]);
                $persona_id = $db->lastInsertId();
            } else {
                // Si ya existe, usa el ID existente
                $persona_id = $persona['persona_id'];
            }

            // Inserta el préstamo
            $stmt = $db->prepare("INSERT INTO prestamos (producto_id, usuario_id, cantidad, fecha_prestamo, persona_prestamo_id) VALUES (?, ?, ?, NOW(), ?)");
            $stmt->execute([$producto_id, $usuario_id, $cantidad, $persona_id]);

            // Restar del stock
            $stmtStock = $db->prepare("UPDATE producto SET producto_stock = producto_stock - ? WHERE producto_id = ?");
            $stmtStock->execute([$cantidad, $producto_id]);

            // Obtener el nombre del producto
            $productoQuery = $db->prepare("SELECT producto_nombre FROM producto WHERE producto_id = ?");
            $productoQuery->execute([$producto_id]);
            $producto = $productoQuery->fetch(PDO::FETCH_ASSOC);
            $productoNombres[] = $producto['producto_nombre'] ?? 'Producto no especificado';
        }

        $nombreProductos = implode(', ', $productoNombres);
        $usuarioQuery = $db->prepare("SELECT usuario_nombre FROM usuario WHERE usuario_id = ?");
        $usuarioQuery->execute([$usuario_id]);
        $usuario = $usuarioQuery->fetch(PDO::FETCH_ASSOC);
        $usuarioNombre = $usuario['usuario_nombre'] ?? 'Usuario no especificado';

        $db->commit();
        header("Location: /inventario/vistas/imprimir_prestamo.php?producto_nombre=" . urlencode($nombreProductos) . "&usuario_nombre=" . urlencode($usuarioNombre) . "&cantidad=" . urlencode(implode(', ', $cantidades)) . "&persona_nombre=" . urlencode($persona_nombre));
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        echo "Error al procesar el préstamo: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php?vista=prestamos");
    exit;
}
?>
