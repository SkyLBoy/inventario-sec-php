<?php
require_once "./php/main.php";
verificarRol('admin');


// Consultar productos
try {
    $productos = conexion()->query("SELECT producto_id, producto_nombre, producto_stock FROM producto")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener los productos: " . $e->getMessage();
    $productos = [];
}

// Consultar usuarios
try {
    $usuarios = conexion()->query("SELECT usuario_id, usuario_nombre FROM usuario")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener los usuarios: " . $e->getMessage();
    $usuarios = [];
}
?>

<div class="container">
    <h1 class="title">Realizar Préstamo</h1>
    <form action="./php/procesar_prestamo.php" method="POST">
        <div class="field">
            <label class="label" for="producto">Productos:</label>
            <div id="productos-container">
                <div class="control producto-item">
                    <div class="product-search-container">
                        <input class="input" type="text" placeholder="Buscar producto" oninput="filterProducts(this)">
                        <div class="product-suggestions" style="display:none;"></div>
                        <div class="select">
                            <select name="producto_id[]" required>
                                <option value="" disabled selected>Seleccione un producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?php echo htmlspecialchars($producto['producto_id']); ?>" data-stock="<?php echo htmlspecialchars($producto['producto_stock']); ?>">
                                        <?php echo htmlspecialchars($producto['producto_nombre']) . " (Stock: " . htmlspecialchars($producto['producto_stock']) . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <input class="input mt-2" type="number" name="cantidad[]" placeholder="Cantidad" min="1" required>
                </div>
            </div>
            <button type="button" class="button is-link is-outlined mt-2" onclick="addProducto()">Agregar otro producto</button>
        </div>

        <div class="field">
            <label class="label" for="usuario">Usuario:</label>
            <div class="control">
                <div class="select">
                    <select name="usuario_id" required>
                        <option value="" disabled selected>Seleccione un usuario</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo htmlspecialchars($usuario['usuario_id']); ?>">
                                <?php echo htmlspecialchars($usuario['usuario_nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="field">
            <label class="label" for="persona_nombre">Nombre de la Persona quien recibe el préstamo:</label>
            <div class="control">
                <input class="input" type="text" name="persona_nombre" required placeholder="Nombre de la persona que recibe el préstamo">
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button class="button is-link" type="submit">Realizar préstamo</button>
            </div>
        </div>
    </form>
</div>

<script>
function addProducto() {
    const productosContainer = document.getElementById('productos-container');
    const productoItem = document.createElement('div');
    productoItem.classList.add('control', 'producto-item', 'mt-2');
    productoItem.innerHTML = `
        <div class="product-search-container">
            <input class="input" type="text" placeholder="Buscar producto" oninput="filterProducts(this)">
            <div class="product-suggestions" style="display:none;"></div>
            <div class="select">
                <select name="producto_id[]" required>
                    <option value="" disabled selected>Seleccione un producto</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?php echo htmlspecialchars($producto['producto_id']); ?>" data-stock="<?php echo htmlspecialchars($producto['producto_stock']); ?>">
                            <?php echo htmlspecialchars($producto['producto_nombre']) . " (Stock: " . htmlspecialchars($producto['producto_stock']) . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <input class="input mt-2" type="number" name="cantidad[]" placeholder="Cantidad" min="1" required>
        <button type="button" class="button is-danger is-outlined ml-2 remove-button" onclick="removeProducto(this)">Eliminar</button>
    `;
    productoItem.style.border = '1px solid #ccc'; // Agregar borde
    productoItem.style.padding = '10px'; // Agregar padding
    productoItem.style.borderRadius = '5px'; // Bordes redondeados
    productoItem.style.marginTop = '10px'; // Margen superior
    productoItem.style.backgroundColor = '#f9f9f9'; // Fondo ligero
    productosContainer.appendChild(productoItem);
}

function removeProducto(button) {
    const productoItem = button.closest('.producto-item');
    productoItem.remove();
}

function filterProducts(input) {
    const filter = input.value.toLowerCase();
    const suggestionsContainer = input.nextElementSibling;
    const select = input.nextElementSibling.nextElementSibling.querySelector('select');
    const options = select.querySelectorAll('option');

    // Limpiar las sugerencias anteriores
    suggestionsContainer.innerHTML = '';

    // Resetear la selección si el campo de búsqueda está vacío
    if (filter.length === 0) {
        select.selectedIndex = 0; // Deseleccionar el producto
        suggestionsContainer.style.display = 'none'; // Ocultar las sugerencias
        return; // Salir de la función
    }

    options.forEach(option => {
        const text = option.textContent.toLowerCase();
        if (text.includes(filter)) {
            const suggestionItem = document.createElement('div');
            suggestionItem.textContent = option.textContent;
            suggestionItem.classList.add('suggestion-item');
            suggestionItem.onclick = function() {
                // Agregar el producto seleccionado al select
                select.value = option.value; // Establecer el valor del select
                input.value = option.textContent; // Establecer el texto en el input
                suggestionsContainer.innerHTML = ''; // Limpiar las sugerencias
                suggestionsContainer.style.display = 'none'; // Ocultar el contenedor de sugerencias
            };
            suggestionsContainer.appendChild(suggestionItem);
        }
    });

    // Mostrar u ocultar el contenedor de sugerencias
    suggestionsContainer.style.display = suggestionsContainer.innerHTML ? 'block' : 'none';
}
</script>

<style>
.product-search-container {
    display: flex;
    align-items: center; /* Centrar verticalmente */
    position: relative; /* Para posicionar las sugerencias de productos */
}

.product-suggestions {
    border: 1px solid #ccc;
    border-top: none;
    position: absolute;
    background-color: white;
    z-index: 10;
    width: 100%; /* Para que las sugerencias ocupen el mismo ancho que el input */
    max-height: 200px; /* Altura máxima de las sugerencias */
    overflow-y: auto; /* Agregar scroll si hay muchas sugerencias */
}

.suggestion-item {
    padding: 8px;
    cursor: pointer;
}

.suggestion-item:hover {
    background-color: #f0f0f0;
}

.producto-item {
    border: 1px solid #ccc; /* Agregar borde a cada producto */
    padding: 10px; /* Agregar espacio interno */
    border-radius: 5px; /* Bordes redondeados */
    margin-top: 10px; /* Espacio superior entre productos */
    background-color: #f9f9f9; /* Fondo claro para mayor contraste */
}

.producto-item input {
    margin-bottom: 10px; /* Espacio inferior para la barra de cantidad */
}

.remove-button {
    margin-left: 10px; /* Espacio a la izquierda del botón de eliminar */
    margin-top: 10px; /* Margen superior para separar del input de cantidad */
}

hr {
    margin-top: 10px; /* Espacio arriba de la línea */
    margin-bottom: 10px; /* Espacio abajo de la línea */
}
</style>
