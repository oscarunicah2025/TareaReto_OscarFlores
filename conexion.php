<?php

    //Creando las variables de la cadena de conexion
    $servidor = "mysql:dbname=proyectito;host127.0.0.1";
    $user = "root";
    $password = "";


    try{ //en el TRY  se hara la cadena de conexion, 
        $cadena_conexion = new PDO($servidor,$user,$password);
        echo "test good";

    }catch(PDOException $e){// el catch sera un mensaje en caso de errores al momento de conectar con la base de datos

        echo "Fallo en conexion XD".$e->getMessage(); //mensaje en caso de error
    }

?>