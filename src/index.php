<?php
session_start();
require 'conexion.php';
$formularioEnviado = $_SERVER['REQUEST_METHOD'] === 'POST';


$nombre = trim($_POST['nombre'] ?? "");
$apellido1 = trim($_POST['apellido1'] ?? "");
$apellido2 = trim($_POST["apellido2"] ?? "");
$dni = trim(strtoupper($_POST["dni"] ?? ""));
$email = trim($_POST["email"]?? "");

$errores = [];

if($formularioEnviado){

  if($nombre === ""){
      $errores[] = "Falta el nombre";
    }elseif(mb_strlen($nombre) > 50){
      $errores[] = "El nombre es demasiado largo";
    }

    if($apellido1 === ""){
      $errores[] = "Falta el primer apellido";
    }elseif(mb_strlen($apellido1) > 50){
      $errores[] = "El primer apellido es demasiado largo";
    }


    if($apellido2 === ""){
      $errores[] = "Falta el segundo apellido";
    }elseif(mb_strlen($apellido2) > 50){
      $errores[] = "El segundo apellido es demasiado largo";
    }

    
    if($email === ""){
      $errores[] = "Falta el correo electrónico";
    }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $errores[] = "El formato del e-mail no es correcto";
    }

    if($dni === ""){
      $errores[] = "Falta el DNI";
    } elseif (strlen($dni) !== 9 || !ctype_digit(substr($dni, 0, 8))) {
        $errores[] = "El formato del DNI debe ser de 8 números y 1 letra";
    } else {
        $numeroDni = (int) substr($dni, 0, 8);
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $letraCorrecta = $letras[$numeroDni % 23];
        
        if (substr($dni, -1) !== $letraCorrecta) {
            $errores[] = "La letra del DNI no es correcta";
        }
    }

    if (empty($errores)) {        
        $sql = "INSERT INTO formulario (nombre, apellido1, apellido2, dni, email) VALUES (?, ?, ?, ?, ?)";

        try{
        $sentencia = $pdo->prepare($sql); //esta linea crea un objeto de la clase PDOStatement, representando que la sentencia está preparada esperando valores
        $sentencia->execute([$nombre, $apellido1, $apellido2, $dni, $email]);

        
        $_SESSION["id_formulario"] = $pdo->lastInsertId();

        
        header("Location: resultados.php");
        exit; 
        }catch(PDOException $e){
          error_log($e->getMessage());
          $errores[] = "Ha habido un problema con la base de datos";
        }
        
    }

}
    


?>  

<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" media="screen" href="style.css" />
    <title>Landing básica</title>    
  </head>
  <body>
    <header>
      <p class="headerTitulo">Aula<span class="estudio">Estudio</span></p>
      <div class="listaHeader">
        <p>Sobre nosotros</p>
        <p>Ubicación</p>
        <p>Tarifas</p>
      </div>
    </header>
    <div class="hero">
      <h1>
        Estudia desarrollo de software en el mejor centro con la mejor
        <span class="palabra">tecnología</span>
      </h1>
      <p class="subtitulo">
        En Aula Estudio tu bienestar es nuestra prioridad numero uno
      </p>
      <div class="container">
        <div class="caja">
          <div class="circulo">
            <img
              class="icono"
              src="/SVG/microchip.svg"
              alt="Icono de microprocesador"
              width="24"
              height="24"
            />
          </div>
          <p class="titulocaja">Ciclo medio</p>
          <p class="subtitulocaja">Sistemas microinformáticos y redes</p>
          <a href = "#cta" class = "informacion">+ INFORMACION</a>
        </div>
        <div class="caja">
          <div class="circulo">
            <img
              src="SVG/codigo.svg"
              class="icono"
              alt="Linea de código"
              width="24"
              height="24"
            />
          </div>
          <p class="titulocaja">Ciclo superior</p>
          <p class="subtitulocaja">
            Desarrollo de aplicaciones web y multiplataforma
          </p>          
          <a href = "#cta" class = "informacion">+ INFORMACION</a>
        </div>
        <div class="caja">
          <div class="circulo">
            <img
              src="SVG/cerebro.svg"
              class="icono"
              alt="Cerebro"
              width="24"
              height="24"
            />
          </div>
          <p class="titulocaja">Master</p>
          <p class="subtitulocaja">
            Inteligencia artificial, Big Data y videojuegos
          </p>
          <a href = "#cta" class = "informacion">+ INFORMACION</a>
        </div>
      </div>
    </div>
    <div class="cta" id = "cta">
      <h1 class="h1cta">
        Rellena el formulario y empieza a caminar hacia el
        <span class="palabra">futuro</span>
      </h1>
      <div class="ctamap">
        <div class="form">

        <?php if (!empty($errores)): ?>
              <div class="errores-box">
                  <ul>
                      <?php foreach ($errores as $error): ?>
                          <li><?= htmlspecialchars($error) ?></li>
                      <?php endforeach; ?>
                  </ul>
              </div>
          <?php endif; ?>


          <form action="index.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input
              id="nombre"
              name="nombre"
              type="text"
              placeholder="Juan"
              value="<?= htmlspecialchars($nombre) ?>"
              
            />
            <label for="apellido1">Primer apellido:</label>
            <input
              id="apellido1"
              name="apellido1"
              type="text"
              placeholder="Rodriguez"
              value="<?= htmlspecialchars($apellido1) ?>"
              
            />
            <label for="apellido2">Segundo apellido:</label>
            <input
              id="apellido2"
              name="apellido2"
              type="text"
              placeholder="Garrido"
              value="<?= htmlspecialchars($apellido2) ?>"
              
            />
            <label for="dni">DNI:</label>
            <input
              id="dni"
              name="dni"
              type="text"
              placeholder="01234567A"
              value="<?= htmlspecialchars($dni) ?>"              
              maxlength="9"
            />
            <label for="email">Correo electrónico:</label>
            <input
              name="email"
              id="email"
              type="email"
              placeholder="jrodgar@aulaestudio.es"
              value="<?= htmlspecialchars($email) ?>"
              
            />
            <button type="submit" class="btnform">Enviar</button>
          </form>
        </div>
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2954.0564044950515!2d-8.733220323514319!3d42.23460794301751!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd2f621436056833%3A0x6d2905a458b6f2da!2sAula%20Estudio!5e0!3m2!1ses!2ses!4v1790170748656!5m2!1ses!2ses"
          width="600"
          height="450"
          style="border: 0"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="strict-origin-when-cross-origin"
        ></iframe>
      </div>
    </div>
    <footer>
      <p class="headerTitulo">Aula<span class="estudio">Estudio</span></p>
      <div class="partesfooter">
        <div class="partes profesionales">
          <p class="titulofooter">Ciclos profesionales</p>
          <p>Ciclo medio de Sistemas</p>
          <p>Ciclo superior de Desarrollo</p>
          <p>Master en IA y Big Data</p>
        </div>
        <div class="partes quienessomos">
          <p class="titulofooter">Sobre nosotros</p>
          <p>Quienes somos</p>
          <p>Ubicación</p>          
          <p>Tarifas</p>
        </div>
        <div class="partes certificaciones">
          <p class="titulofooter">Certificaciones</p>
          <p>Vigilancia y Seguridad Privada</p>
          <p>Adiestramiento Canino</p>
          <p>Certificados profesionales</p>
        </div>
      </div>
    </footer>
    
  </body>
</html>
