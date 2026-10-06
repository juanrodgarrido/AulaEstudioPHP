<?php


$host = getenv("DB_HOST");
$database = getenv("DB_DATABASE");
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");

$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";


$opciones = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];


try{
    $pdo = new PDO($dsn, $user, $password, $opciones);
}catch(PDOException $e){
    error_log($e->getMessage()); //Así podemos ver el error en el log de Docker con docker compose logs web
    exit("Ha habido un error en la conexión");
}


