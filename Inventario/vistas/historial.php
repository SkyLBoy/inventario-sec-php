<?php
require_once "./php/main.php";

// Consultar historial de préstamos y devoluciones
try {
    $historial = conexion()->query("
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
        LEFT JOIN persona_prestamo pp ON p.persona_prestamo_id = pp.persona_id
        ORDER BY 
            CASE WHEN p.fecha_devolucion IS NULL THEN 0 ELSE 1 END, 
            p.fecha_prestamo DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener el historial: " . $e->getMessage();
    $historial = [];
}

// Función para obtener personas
function obtener_personas() {
    $query = "SELECT persona_id, persona_nombre FROM persona_prestamo";
    try {
        $stmt = conexion()->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error al obtener personas: " . htmlspecialchars($e->getMessage());
        return [];
    }
}

// Función para obtener usuarios
function obtener_usuarios() {
    $query = "SELECT usuario_id, usuario_nombre FROM usuario";
    try {
        $stmt = conexion()->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error al obtener usuarios: " . htmlspecialchars($e->getMessage());
        return [];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Préstamos y Devoluciones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="title">Historial de Préstamos y Devoluciones</h1>
            
            <!-- Formulario de Filtros -->
            <form action="vistas/prestamos_filtrados.php" method="GET" class="box">
                <div class="columns">
                    <div class="column">
                        <label class="label">Persona</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="persona_id">
                                    <option value="">Selecciona una persona</option>
                                    <?php foreach (obtener_personas() as $persona): ?>
                                        <option value="<?= htmlspecialchars($persona['persona_id']); ?>">
                                            <?= htmlspecialchars($persona['persona_nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <label class="label">Usuario</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="usuario_id">
                                    <option value="">Selecciona un usuario</option>
                                    <?php foreach (obtener_usuarios() as $usuario): ?>
                                        <option value="<?= htmlspecialchars($usuario['usuario_id']); ?>">
                                            <?= htmlspecialchars($usuario['usuario_nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="column is-narrow">
                        <label class="label">&nbsp;</label>
                        <button type="submit" class="button is-primary is-fullwidth">Filtrar</button>
                    </div>
                </div>
            </form>

            <!-- Tabla de Historial -->
            <div class="table-container">
                <table class="table is-striped is-fullwidth">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Persona</th>
                            <th>Usuario</th>
                            <th>Fecha de Préstamo</th>
                            <th>Fecha de Devolución</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($historial)): ?>
                            <?php foreach ($historial as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['prestamo_id']); ?></td>
                                    <td><?= htmlspecialchars($row['producto_nombre']); ?></td>
                                    <td><?= htmlspecialchars($row['cantidad']); ?></td>
                                    <td><?= htmlspecialchars($row['persona_nombre']); ?></td>
                                    <td><?= htmlspecialchars($row['usuario_nombre']); ?></td>
                                    <td><?= htmlspecialchars($row['fecha_prestamo']); ?></td>
                                    <td><?= htmlspecialchars($row['fecha_devolucion'] ?? 'No devuelto'); ?></td>
                                    <td class="<?= $row['accion'] == 'Préstamo' ? 'has-text-info' : 'has-text-success' ?>">
                                        <?= htmlspecialchars($row['accion']); ?>
                                    </td>
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
    </section>
</body>
</html>
