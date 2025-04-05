<?php

    //Creando las variables de la cadena de conexion
    $servidor = "mysql:dbname=proyectito;host127.0.0.1";
    $user = "root";
    $password = "";


    try{
        $cadena_conexion = new PDO($servidor,$user,$password);
        echo "test good";

    }catch(PDOException $e){

        echo "Fallo en conexion XD".$e->getMessage(); //mensaje en caso de error
    }

?>