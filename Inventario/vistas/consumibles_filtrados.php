<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Consumibles Filtrados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>
<body>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; ?>  

        <?php
        require_once dirname(__DIR__) . "/php/main.php";

        // Variables de filtro seleccionadas
        $persona_nombre = isset($_GET['persona_nombre']) ? limpiar_cadena($_GET['persona_nombre']) : '';
        $usuario_nombre = isset($_GET['usuario_nombre']) ? limpiar_cadena($_GET['usuario_nombre']) : '';
        $fecha_inicio = isset($_GET['fecha_inicio']) ? limpiar_cadena($_GET['fecha_inicio']) : '';
        $fecha_fin = isset($_GET['fecha_fin']) ? limpiar_cadena($_GET['fecha_fin']) : '';

        // Consultar las transacciones de consumibles con los filtros seleccionados
        $query = "
            SELECT pc.persona_id, p.producto_nombre, pc.cantidad, pc.persona_nombre, pc.fecha, u.usuario_nombre
            FROM persona_consumible pc
            JOIN producto p ON pc.producto_id = p.producto_id
            JOIN usuario u ON pc.usuario_id = u.usuario_id
            WHERE (:persona_nombre = '' OR pc.persona_nombre = :persona_nombre)
            AND (:usuario_nombre = '' OR u.usuario_nombre = :usuario_nombre)
            " . ($fecha_inicio ? "AND pc.fecha >= :fecha_inicio " : "") . 
            ($fecha_fin ? "AND pc.fecha <= DATE_ADD(:fecha_fin, INTERVAL 1 DAY) " : "");

        try {
            $stmt = conexion()->prepare($query);
            $params = [
                ':persona_nombre' => $persona_nombre,
                ':usuario_nombre' => $usuario_nombre,
            ];

            if ($fecha_inicio) {
                $params[':fecha_inicio'] = $fecha_inicio;
            }
            if ($fecha_fin) {
                $params[':fecha_fin'] = $fecha_fin;
            }

            $stmt->execute($params);
            $transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<div class='notification is-danger'>Error al obtener las transacciones: " . htmlspecialchars($e->getMessage()) . "</div>";
            $transacciones = [];
        }
        ?>

        <!-- Tabla para mostrar las transacciones -->
        <div class="table-container">
            <table class="table is-striped is-fullwidth is-hoverable">
                <thead class="has-background-light">
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Nombre de la Persona</th>
                        <th>Usuario que da el producto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transacciones)): ?>
                        <?php foreach ($transacciones as $transaccion): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaccion['persona_id']); ?></td>
                                <td><?php echo htmlspecialchars($transaccion['producto_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($transaccion['cantidad']); ?></td>
                                <td><?php echo htmlspecialchars($transaccion['persona_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($transaccion['usuario_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($transaccion['fecha']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="has-text-centered">No hay transacciones registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
