<link rel="stylesheet" href="estilos.css">
<div class="container is-fluid has-text-centered" style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh;">
    <h1 class="title">Home</h1>
    <h2 class="subtitle">¡Bienvenido <?php echo $_SESSION['nombre']." ".$_SESSION['apellido']; ?>!</h2>
    
    <div class="image" style="max-width: 400px; max-height: 400px; margin-top: 20px;">
        <img src="img/logo_sec.png" alt="Logo" style="width: 100%; height: auto;"> 
    </div>
</div>
