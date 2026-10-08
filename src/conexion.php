<?php
require "vendor/autoload.php";

$uri = getenv("MONGODB_URI");
$db = getenv("MONGODB_DB");



try{
    $cliente = new MongoDB\Client($uri);
    $coleccion = $cliente->selectCollection($db, "inscripciones");
}catch(MongoDB\Driver\Exception\Exception $e){
    error_log($e->getMessage()); //Así podemos ver el error en el log de Docker con docker compose logs web
    exit("Ha habido un error en la conexión");
}


