<?php
  
  require 'conexion.php'; //Incluyendo la conexion a la base de datis
  $nombre = "";
  $tipoDeGasto = "";
  $valorDelGasto = 0;
  $error = "";
  $hay_post = "";
  $idPersona = null;

  if(isset($_REQUEST['submit1'])){
    $hay_post = true;
    $nombre = isset($_REQUEST['txtNombre']) ? $_REQUEST['txtNombre'] : "";
    $tipoDeGasto = isset($_REQUEST['cmbTipoGasto']) ? $_REQUEST['cmbTipoGasto'] : "";
    $valorDelGasto = isset($_REQUEST['txtValorGasto']) ? $_REQUEST['txtValorGasto'] : "";

    //Condicionales de verificacion para el nombre
    if(!empty($nombre)){
        $nombre = preg_replace("/[^a-zA-ZáéíóúÁÉÍÓÚ]/u","",$nombre);
    }
    else{
        $error .= "El nombre no puede esta vácio.<br>";
    }


    if($tipoDeGasto == ""){
        $error .= "Seleccione un tipo de Gasto.<br>";
    }
    
    if($valorDelGasto <= 0){
        $error .= "El valor tiene que ser mayor a 0.<br>";
    }

    if(!$error){
        $stm_insertarRegistro = $cadena_conexion->prepare("insert into persona(nombrePersona, tipoGasto, valorGasto) values(:nombre, :tipoGasto, :valorGasto)");
        $stm_insertarRegistro->execute([':nombre'=>$nombre, ':tipoGasto'=>$tipoDeGasto, ':valorGasto'=>$valorDelGasto]);
        header("Location: index.php?mensaje=registroGuardado");
        exit();
    }
}
 

  

   

  


?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  </head>
  <body>


        <h1>FORMULARIO GASTOS FAMILIARES</h1>

        
        <div class="container">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" >




            <!--LINEA DE NOMBRE -->
            <label class="form-label" for="nombre">Nombre Completo:</label>
            <input class="form-control" type="text" name="txtNombre" id="nombre" value="<?php echo isset($nombre)? $nombre : "" ?>"><br>

            <!--LINEA DE TIPO DE GASTO -->
            <select class="form-select" name="cmbTipoGasto" id="tipoGasto">
                <option value="">Seleccione un país</option>
                <option value="Educacion" <?php echo ($tipoDeGasto=='Educacion')? 'selected' : '' ?> >Educacion</option>
                <option value="Salud" <?php echo ($tipoDeGasto=='Salud')? 'selected' : '' ?>>Salud</option>
                <option value="Transporte" <?php echo  ($tipoDeGasto=='Transporte')? 'selected' : '' ?>>Transporte</option>
            </select><br>

             <!--LINEA DEL VALOR DEL GASTO -->
            <label class="form-label" for="valorGasto">valorDelGasto</label>
            <input class="form-control" type="text" name="txtValorGasto" id="ValorGasto" value="<?php echo isset($valorDelGasto)? $valorDelGasto : 0 ?>"><br>


            <input class="btn btn-primary" type="submit" value="Enviar" name="submit1">
            <?php
                if($idPersona){
                    echo '<input class="btn btn-dark" type="submit" value="Modificar" name="submit2">';
                }
                ?>
            <a class="btn btn-secondary" href="index.php">Cancelar</a>



            </form>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
  </body>
</html>