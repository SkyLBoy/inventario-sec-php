<?php
require_once "./php/main.php";
verificarRol('admin');

// Obtener préstamos activos
$query = conexion()->prepare("
    SELECT 
        p.prestamo_id, 
        u.usuario_nombre, 
        pr.producto_nombre, 
        p.cantidad, 
        pp.persona_nombre
    FROM prestamos p
    JOIN usuario u ON p.usuario_id = u.usuario_id
    JOIN producto pr ON p.producto_id = pr.producto_id
    LEFT JOIN persona_prestamo pp ON p.persona_prestamo_id = pp.persona_id
    WHERE p.fecha_devolucion IS NULL
");
$query->execute();
$prestamos = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="title">Devoluciones</h1>

<!-- Barra de búsqueda -->
<div class="field">
    <label class="label">Buscar préstamo:</label>
    <div class="control">
        <input type="text" id="buscar" class="input" placeholder="Buscar por persona, usuario o producto...">
    </div>
</div>

<!-- Tabla de préstamos activos -->
<form action="./php/procesar_devolucion.php" method="POST" class="box">
    <table class="table is-fullwidth is-striped">
        <thead>
            <tr>
                <th>Persona</th>
                <th>Usuario</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="tabla-prestamos">
            <?php foreach ($prestamos as $prestamo): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prestamo['persona_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['usuario_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['producto_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prestamo['cantidad']); ?></td>
                    <td>
                        <button type="submit" name="prestamo_id" value="<?php echo htmlspecialchars($prestamo['prestamo_id']); ?>" class="button is-link">
                            Devolver
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

<script>
    // Filtrar préstamos en la tabla
    document.getElementById('buscar').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#tabla-prestamos tr');
        rows.forEach(row => {
            const persona = row.children[0].textContent.toLowerCase();
            const usuario = row.children[1].textContent.toLowerCase();
            const producto = row.children[2].textContent.toLowerCase();
            row.style.display = persona.includes(filter) || usuario.includes(filter) || producto.includes(filter) ? '' : 'none';
        });
    });
</script>
