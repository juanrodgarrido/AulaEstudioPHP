<?php
session_start();
require 'conexion.php';


if (!isset($_SESSION['id_formulario'])) {
    header("Location: index.php");
    exit;
}

$id = $_SESSION['id_formulario'];
$sql = "SELECT nombre, apellido1, apellido2, dni, email FROM formulario WHERE id = ?";

try{
$sentencia = $pdo->prepare($sql);
$sentencia->execute([$id]);
$fila = $sentencia->fetch(); //Guardamos los datos en fila para luego acceder a ellos
}catch(PDOException $e){
    error_log($e->getMessage()); //Así podemos ver el error en el log de Docker con docker compose logs web
    exit("Ha habido un error en la base de datos");
}




if(!$fila){
  header("Location: index.php");
    exit;
}



$nombre = $fila['nombre'];
$apellido1 = $fila['apellido1'];
$apellido2 = $fila['apellido2'];
$dni = $fila['dni'];
$email = $fila['email'];





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