<?php
require 'conexion.php'; //Incluyendo la conexion a la base de datos

// Variables
$nombre = "";
$tipoDeGasto = "";
$valorDelGasto = 0;
$error = "";
$hay_post = "";
$idPersona = null;

// Calcular el total de los valores de los gastos
$stm_totalGastos = $cadena_conexion->prepare("SELECT SUM(valorGasto) as total FROM persona");
$stm_totalGastos->execute();
$resultadoTotal = $stm_totalGastos->fetch();
$totalGastos = $resultadoTotal['total'] ? $resultadoTotal['total'] : 0;

// Filtrar por nombre si se ha enviado un valor de búsqueda
$searchQuery = "";
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
    $searchQuery = "%" . $_REQUEST['search'] . "%";
    $stm = $cadena_conexion->prepare("SELECT * FROM persona WHERE nombrePersona LIKE :search");
    $stm->execute([':search' => $searchQuery]);
} else {
    // Si no hay búsqueda, mostrar todos los registros
    $stm = $cadena_conexion->prepare("SELECT * FROM persona");
    $stm->execute();
}

$resultados = $stm->fetchAll();

if(isset($_REQUEST['submit1'])){
    $hay_post = true;
    $nombre = isset($_REQUEST['txtNombre']) ? $_REQUEST['txtNombre'] : "";
    $tipoDeGasto = isset($_REQUEST['cmbTipoGasto']) ? $_REQUEST['cmbTipoGasto'] : "";
    $valorDelGasto = isset($_REQUEST['txtValorGasto']) ? $_REQUEST['txtValorGasto'] : "";

    // Condicionales de verificación para el nombre
    if(!empty($nombre)){
        $nombre = preg_replace("/[^a-zA-ZáéíóúÁÉÍÓÚ]/u","",$nombre);
    }
    else{
        $error .= "El nombre no puede estar vacío.<br>";
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

// Opción de modificar registros
if(isset($_REQUEST['submit2'])){
    $codigoUsuario = $_REQUEST['id'];
    $hay_post = true;
    $nombre = isset($_REQUEST['txtNombre']) ? $_REQUEST['txtNombre'] : "";
    $tipoDeGasto = isset($_REQUEST['cmbTipoGasto']) ? $_REQUEST['cmbTipoGasto'] : "";
    $valorDelGasto = isset($_REQUEST['txtValorGasto']) ? $_REQUEST['txtValorGasto'] : "";

    // Condicionales de verificación para el nombre
    if(!empty($nombre)){
        $nombre = preg_replace("/[^a-zA-ZáéíóúÁÉÍÓÚ]/u","",$nombre);
    }
    else{
        $error .= "El nombre no puede estar vacío.<br>";
    }

    if($tipoDeGasto == ""){
        $error .= "Seleccione un tipo de Gasto.<br>";
    }

    if($valorDelGasto <= 0){
        $error .= "El valor tiene que ser mayor a 0.<br>";
    }

    if(!$error){
        $stm_modificar = $conexion->prepare("update persona set nombrePersona = :nombre, tipoGasto = :tipoGasto, valorGasto = :valorGasto where idPersona = :id");
        $stm_modificar->execute([':nombre'=>$nombre, ':tipoGasto'=>$tipoDeGasto, ':valorGasto'=>$valorDelGasto, ':id'=> $idPersona]);
        header("Location: index.php?mensaje=registroModificado");
        exit();
    }
}

if(isset($_REQUEST['id']) && isset($_REQUEST['op'])){
    $id = $_REQUEST['id'];
    $op = $_REQUEST['op'];

    if($op == 'm'){
        $stm_seleccionarRegistro = $cadena_conexion->prepare("select * from persona where id_persona=:id");
        $stm_seleccionarRegistro->execute([':id'=>$id]);
        $resultado = $stm_seleccionarRegistro->fetch();
        $idPersona = $resultado['id_persona'];
        $nombre = $resultado['nombrePersona'];
        $tipoDeGasto = $resultado['tipoGasto'];
        $valorDelGasto = $resultado['valorGasto'];
    }
    else if($op == 'e'){
        $stm_eliminar = $cadena_conexion->prepare("delete from persona where id_persona = :id");
        $stm_eliminar->execute([':id'=>$id]);
        header("Location: index.php?mensaje=registroEliminado");
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
        <!-- Formulario de búsqueda -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="mb-3">
            <label for="search" class="form-label">Buscar por Nombre:</label>
            <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_REQUEST['search']) ? $_REQUEST['search'] : ''; ?>">
            <button type="submit" class="btn btn-secondary mt-2">Buscar</button>
        </form>

        <!-- Recuadro para mostrar el total de los gastos -->
        <div class="alert alert-info" role="alert">
            <strong>Total de los gastos:</strong> $<?php echo number_format($totalGastos, 2); ?>
        </div>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

            <!-- FORMULARIO -->
            <label class="form-label" for="nombre">Nombre Completo:</label>
            <input class="form-control" type="text" name="txtNombre" id="nombre" value="<?php echo isset($nombre)? $nombre : "" ?>"><br>

            <label class="form-label" for="nombre">Tipo de Gasto:</label>
            <select class="form-select" name="cmbTipoGasto" id="tipoGasto">
                <option value="">Seleccione el tipo de gasto</option>
                <option value="Educacion" <?php echo ($tipoDeGasto=='Educacion')? 'selected' : '' ?> >Educacion</option>
                <option value="Salud" <?php echo ($tipoDeGasto=='Salud')? 'selected' : '' ?>>Salud</option>
                <option value="Transporte" <?php echo  ($tipoDeGasto=='Transporte')? 'selected' : '' ?>>Transporte</option>
                <option value="Viajes" <?php echo  ($tipoDeGasto=='Viajes')? 'selected' : '' ?>>Viajes</option>
            </select><br>

            <label class="form-label" for="valorGasto">Valor del Gasto</label>
            <input class="form-control" type="text" name="txtValorGasto" id="ValorGasto" value="<?php echo isset($valorDelGasto)? $valorDelGasto : 0 ?>"><br>

            <input class="btn btn-primary" type="submit" value="Enviar" name="submit1">
            <?php
                if($idPersona){
                    echo '<input class="btn btn-dark" type="submit" value="Modificar" name="submit2">';
                }
            ?>
            <a class="btn btn-secondary" href="index.php">Cancelar</a>
        </form>
        <br>
        <?php if($error):  ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo "<p>$error</p>"; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php
            if(isset($_REQUEST['mensaje'])){
                $mensaje = $_REQUEST['mensaje'];
        ?>
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                <?php
                    if($mensaje=='registroGuardado'){
                        echo "<p>Registro guardado.</p>";
                    }
                    elseif($mensaje == 'registroModificado'){
                        echo "<p>Registro modificado.</p>";
                    }
                    elseif($mensaje=='registroEliminado'){
                        echo "<p>Registro eliminado.</p>";
                    }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            }
        ?>

        <table class="table table-bordered table-hover">
            <thead>
                <th>Nombre</th>
                <th>Tipo de Gasto</th>
                <th>Valor del Gasto</th>
                <th colspan="2">Acciones</th>
            </thead>
            <tbody>
                <?php foreach($resultados as $registro): ?>
                    <tr>
                        <td><?php echo $registro['nombrePersona']; ?></td>
                        <td><?php echo $registro['tipoGasto']; ?></td>
                        <td><?php echo $registro['valorGasto']; ?></td>
                        <td><a class="btn btn-primary" href="index.php?id=<?php echo $registro['id_persona'] ?>&op=m">Modificar</a></td>
                        <td><a class="btn btn-danger" href="index.php?id=<?php echo $registro['id_persona'] ?>&op=e" onclick="return confirm('Desea eliminar el registro');">Eliminar</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+e
