<?php
function conexion(){
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=db_inventario', 'root', '1234', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => true // Conexión persistente para mejorar el rendimiento
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
    }
}

try {
    // Ejecutar la consulta
    $stmt = conexion()->query("
        SELECT 
            p.prestamo_id,
            pr.producto_nombre,
            u.usuario_nombre,
            p.cantidad,
            p.persona_nombre,   -- Incluye el nombre de la persona que recibe el préstamo
            p.fecha_prestamo,
            p.fecha_devolucion
        FROM prestamos p
        JOIN producto pr ON p.producto_id = pr.producto_id
        JOIN usuario u ON p.usuario_id = u.usuario_id
    ");
    
    $prestamos = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al obtener los préstamos: " . $e->getMessage();
    $prestamos = [];
}
?>

<div class="container">
    <h1 class="title">Lista de Préstamos</h1>
    
    <table class="table is-fullwidth">
        <thead>
            <tr>
                <th>ID del Préstamo</th>
                <th>Producto</th>
                <th>Usuario</th>
                <th>Cantidad</th>
                <th>Nombre de la Persona</th> <!-- Nueva columna para el nombre de la persona -->
                <th>Fecha de Préstamo</th>
                <th>Fecha de Devolución</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($prestamos)): ?>
                <?php foreach ($prestamos as $prestamo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prestamo['prestamo_id']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['producto_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['usuario_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($prestamo['persona_nombre']); ?></td> <!-- Mostrar el nombre de la persona -->
                        <td><?php echo htmlspecialchars($prestamo['fecha_prestamo']); ?></td>
                        <td><?php echo $prestamo['fecha_devolucion'] ? htmlspecialchars($prestamo['fecha_devolucion']) : 'Pendiente'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="has-text-centered">No hay préstamos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
