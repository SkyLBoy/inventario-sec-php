<?php
require_once "./php/main.php";

// Obtener los datos de la URL
$producto_nombre = $_GET['producto_nombre'] ?? 'Producto no especificado';
$usuario_nombre = $_GET['usuario_nombre'] ?? 'Usuario no especificado';
$cantidad = $_GET['cantidad'] ?? 'Cantidad no especificada';
$persona_nombre = $_GET['persona_nombre'] ?? 'Persona no especificada';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Consumible</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }
        h2{
            text-align: center;
            color: #333;
            
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 150px;
        }

        .product-details, .user-info {
            margin-bottom: 20px;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }

        .signature div {
            width: 45%;
            border-top: 1px solid #333;
            text-align: center;
            padding-top: 10px;
            margin-top: 40px; /* Ajuste para no juntar las firmas */
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background: none;
            }
            .container {
                box-shadow: none;
            }
            .no-print {
                display: none; 
            }
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="logo">
                <img src="/Inventario/img/logo_sec.png" alt="Logo de la Empresa">
            </div>

            <h1 class="title">Entrega de Producto</h1>
            <h2 class="subtittle">Recibo</h2>
            
            <div class="product-details">
                <p><strong>Producto:</strong> <?php echo htmlspecialchars($producto_nombre); ?></p>
                <p><strong>Cantidad:</strong> <?php echo htmlspecialchars($cantidad); ?></p>
                <p><strong>Nombre de la Persona:</strong> <?php echo htmlspecialchars($persona_nombre); ?></p>
            </div>

            <div class="user-info">
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario_nombre); ?></p>
            </div>

            <div class="signature">
                <div>Firma de Entrega</div> 
                <div>Firma de Recibido</div>
            </div>

            <button class="button is-primary no-print" onclick="printPage()">Imprimir</button>
        </div>
    </section>
</body>
</html>
