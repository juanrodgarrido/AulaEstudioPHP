<?php
session_start();

if (!isset($_SESSION['datos_usuario'])) {
    header("Location: index.php");
    exit;
}

$datos = $_SESSION['datos_usuario'];

$nombre = $datos['nombre'];
$apellido1 = $datos['apellido1'];
$apellido2 = $datos['apellido2'];
$dni = $datos['dni'];
$email = $datos['email'];

unset($_SESSION['datos_usuario'])



?>  


<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" media="screen" href="style.css" />
    <title>Resultados</title>    
  </head>
  <body>
    <div class = "resultados">
        <h1>Resumen del formulario</h1>        
        <div class="formResultado">          
            <label for="nombre">Nombre:</label>
            <input
              id="nombre"              
              name="nombre"
              type="text" 
              value="<?=htmlspecialchars($nombre)?>"          
              readonly
            />
            <label for="apellido1">Primer apellido:</label>
            <input
              id="apellido1"              
              name="apellido1"
              type="text"
              value="<?=htmlspecialchars($apellido1)?>"
              readonly
            />
            <label for="apellido2">Segundo apellido:</label>
            <input
              id="apellido2"              
              name="apellido2"
              type="text"
              value="<?=htmlspecialchars($apellido2)?>"
              readonly
            />
            <label for="dni">DNI</label>
            <input
              id="dni"              
              name="dni"
              type="text"
              value="<?=htmlspecialchars($dni)?>"
              readonly
              maxlength="9"
            />
            <label for="email">Correo electrónico</label>
            <input
              name="email"              
              id="email"
              type="email"
              value="<?=htmlspecialchars($email)?>"
              readonly
            />
            <button type="button" class="btnform2" onclick="window.location.href='index.php'">Volver atrás</button>
          
        </div>
    </div>    
  </body>
</html>