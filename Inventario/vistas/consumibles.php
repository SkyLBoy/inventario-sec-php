<?php
require_once "./php/main.php";
verificarRol('admin');


// Consultar todos los productos
try {
    $productos = conexion()->query("SELECT producto_id, producto_nombre, producto_stock FROM producto")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener los productos: " . $e->getMessage();
    $productos = [];
}

// Consultar todos los usuarios
try {
    $usuarios = conexion()->query("SELECT usuario_id, usuario_nombre FROM usuario")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener los usuarios: " . $e->getMessage();
    $usuarios = [];
}
?>

<div class="container">
    <h1 class="title">Gestión de Productos Consumibles</h1>
    <form action="./php/procesar_consumible.php" method="POST"> <!-- Ruta al archivo de procesamiento -->
        <!-- Campo para seleccionar Producto -->
        <div class="field">
            <label class="label" for="producto">Producto:</label>
            <div class="control">
                <div class="select is-fullwidth">
                    <select name="producto_id" required>
                        <option value="" disabled selected>Seleccione un producto</option>
                        <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?php echo htmlspecialchars($producto['producto_id']); ?>">
                                    <?php echo htmlspecialchars($producto['producto_nombre']) . " (Stock: " . htmlspecialchars($producto['producto_stock']) . ")"; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay productos disponibles</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Campo para seleccionar Usuario -->
        <div class="field">
            <label class="label" for="usuario_id">Usuario que da el producto:</label>
            <div class="control">
                <div class="select is-fullwidth">
                    <select name="usuario_id" required>
                        <option value="" disabled selected>Seleccione un usuario</option>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo htmlspecialchars($usuario['usuario_id']); ?>">
                                    <?php echo htmlspecialchars($usuario['usuario_nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay usuarios disponibles</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Campo para ingresar Cantidad -->
        <div class="field">
            <label class="label" for="cantidad">Cantidad a consumir:</label>
            <div class="control">
                <input class="input" type="number" name="cantidad" required min="1" placeholder="Ingrese la cantidad a consumir">
            </div>
        </div>

        <!-- Campo para ingresar Nombre de la Persona -->
        <div class="field">
            <label class="label" for="persona_nombre">Nombre de la persona que recibe el producto:</label>
            <div class="control">
                <input class="input" type="text" name="persona_nombre" required placeholder="Ingrese el nombre de la persona">
            </div>
        </div>

        <!-- Botón para enviar el formulario -->
        <div class="field">
            <div class="control">
                <button class="button is-link is-fullwidth" type="submit">Consumir Producto</button>
            </div>
        </div>
    </form>
</div>

</body>
</html>