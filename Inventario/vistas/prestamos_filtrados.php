<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Préstamos Filtrados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>
<body>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/Inventario/inc/btn_back.php"; ?>  

        <?php
        require_once dirname(__DIR__) . "/php/main.php";

        // Variables de filtro seleccionadas
        $persona_id = isset($_GET['persona_id']) && $_GET['persona_id'] !== "" ? (int)$_GET['persona_id'] : null;
        $usuario_id = isset($_GET['usuario_id']) && $_GET['usuario_id'] !== "" ? (int)$_GET['usuario_id'] : null;

        // Consultar las transacciones de préstamos con los filtros seleccionados
        $query = "
            SELECT 
                p.prestamo_id,
                p.fecha_prestamo,
                p.fecha_devolucion,
                p.cantidad,
                pp.persona_nombre,
                u.usuario_nombre,
                pr.producto_nombre,
                CASE 
                    WHEN p.fecha_devolucion IS NULL THEN 'Préstamo'
                    ELSE 'Devolución'
                END AS accion
            FROM prestamos p
            JOIN usuario u ON p.usuario_id = u.usuario_id
            JOIN producto pr ON p.producto_id = pr.producto_id
            JOIN persona_prestamo pp ON p.persona_prestamo_id = pp.persona_id
            WHERE (:persona_id IS NULL OR pp.persona_id = :persona_id)
              AND (:usuario_id IS NULL OR u.usuario_id = :usuario_id)
            ORDER BY p.fecha_prestamo DESC
        ";

        try {
            $stmt = conexion()->prepare($query);
            $stmt->bindValue(':persona_id', $persona_id, $persona_id !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(':usuario_id', $usuario_id, $usuario_id !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->execute();
            $prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "<div class='notification is-danger'>Error al obtener los préstamos: " . htmlspecialchars($e->getMessage()) . "</div>";
            $prestamos = [];
        }
        ?>

        <!-- Tabla para mostrar los préstamos filtrados -->
        <div class="table-container">
            <table class="table is-striped is-fullwidth is-hoverable">
                <thead class="has-background-light">
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Nombre de la Persona</th>
                        <th>Usuario</th>
                        <th>Fecha de Préstamo</th>
                        <th>Fecha de Devolución</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prestamos)): ?>
                        <?php foreach ($prestamos as $prestamo): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prestamo['prestamo_id']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['producto_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['cantidad']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['persona_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['usuario_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['fecha_prestamo']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['fecha_devolucion'] ?? 'No devuelto'); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['accion']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="has-text-centered">No hay movimientos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
