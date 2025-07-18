<?php
require_once dirname(__DIR__) . "/php/main.php";

// Asegúrate de que la sesión esté activa
if (!isset($_SESSION['id'])) {
    header('Location: iniciar_sesion.php');
    exit();
}

// Obtener las opciones de filtro para las personas y los usuarios
try {
    $personas = conexion()->query("SELECT DISTINCT persona_nombre FROM persona_consumible")->fetchAll(PDO::FETCH_ASSOC);
    $usuarios = conexion()->query("SELECT DISTINCT u.usuario_nombre FROM usuario u JOIN persona_consumible pc ON u.usuario_id = pc.usuario_id")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener las opciones de filtro: " . $e->getMessage();
    $personas = [];
    $usuarios = [];
}

// Variables de filtro seleccionadas
$persona_nombre = isset($_GET['persona_nombre']) ? limpiar_cadena($_GET['persona_nombre']) : '';
$usuario_nombre = isset($_GET['usuario_nombre']) ? limpiar_cadena($_GET['usuario_nombre']) : '';
$fecha_inicio = isset($_GET['fecha_inicio']) ? limpiar_cadena($_GET['fecha_inicio']) : '';
$fecha_fin = isset($_GET['fecha_fin']) ? limpiar_cadena($_GET['fecha_fin']) : '';

// Convertir la fecha de fin a un día después para incluir todo el día seleccionado
if ($fecha_fin) {
    $fecha_fin = date('Y-m-d', strtotime($fecha_fin . ' +1 day'));
}

// Consultar las transacciones de consumibles con los filtros seleccionados
$query = "
    SELECT pc.persona_id, p.producto_nombre, pc.cantidad, pc.persona_nombre, pc.fecha, u.usuario_nombre
    FROM persona_consumible pc
    JOIN producto p ON pc.producto_id = p.producto_id
    JOIN usuario u ON pc.usuario_id = u.usuario_id
    WHERE (:persona_nombre = '' OR pc.persona_nombre = :persona_nombre)
    AND (:usuario_nombre = '' OR u.usuario_nombre = :usuario_nombre)
    " . ($fecha_inicio ? "AND pc.fecha >= :fecha_inicio " : "") . 
    ($fecha_fin ? "AND pc.fecha < :fecha_fin " : "");

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
    echo "Error al obtener las transacciones: " . $e->getMessage();
    $transacciones = [];
}
?>

<div class="container py-6">
    <h1 class="title has-text-centered">Lista de Consumibles</h1>
    
    <!-- Formulario de filtros -->
    <form method="GET" action="vistas/consumibles_filtrados.php" class="box my-5">
        <h2 class="subtitle has-text-centered">Filtrar Transacciones</h2>
        <div class="columns is-multiline">
            <div class="column is-half">
                <div class="field">
                    <label class="label">Persona</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select name="persona_nombre">
                                <option value="">Seleccionar Persona</option>
                                <?php foreach ($personas as $persona): ?>
                                    <option value="<?php echo htmlspecialchars($persona['persona_nombre']); ?>"
                                        <?php echo ($persona['persona_nombre'] == $persona_nombre) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($persona['persona_nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-half">
                <div class="field">
                    <label class="label">Usuario</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select name="usuario_nombre">
                                <option value="">Seleccionar Usuario</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?php echo htmlspecialchars($usuario['usuario_nombre']); ?>"
                                        <?php echo ($usuario['usuario_nombre'] == $usuario_nombre) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($usuario['usuario_nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-half">
                <div class="field">
                    <label class="label">Fecha de Inicio</label>
                    <div class="control">
                        <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>" class="input is-rounded" />
                    </div>
                </div>
            </div>
            <div class="column is-half">
                <div class="field">
                    <label class="label">Fecha de Fin</label>
                    <div class="control">
                        <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" class="input is-rounded" />
                    </div>
                </div>
            </div>
            <div class="column is-full has-text-centered">
                <button type="submit" class="button is-link is-medium is-fullwidth">Aplicar Filtros</button>
            </div>
        </div>
    </form>

    <!-- Tabla para mostrar las transacciones -->
    <div class="table-container box">
        <table class="table is-striped is-fullwidth is-hoverable">
            <thead class="has-background-info has-text-white">
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
