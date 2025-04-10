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
    $stm = $cadena_conexion->prepare("SELECT * FROM persona");
    $stm->execute();
}

$resultados = $stm->fetchAll();

// Opción de modificar registros: carga de datos antes del POST
if (isset($_REQUEST['id']) && isset($_REQUEST['op'])) {
    $id = $_REQUEST['id'];
    $op = $_REQUEST['op'];

    if ($op == 'm') {
        $stm_seleccionarRegistro = $cadena_conexion->prepare("SELECT * FROM persona WHERE id_persona = :id");
        $stm_seleccionarRegistro->execute([':id' => $id]);
        $resultado = $stm_seleccionarRegistro->fetch();
        $idPersona = $resultado['id_persona'];
        $nombre = $resultado['nombrePersona'];
        $tipoDeGasto = $resultado['tipoGasto'];
        $valorDelGasto = $resultado['valorGasto'];
    } elseif ($op == 'e') {
        $stm_eliminar = $cadena_conexion->prepare("DELETE FROM persona WHERE id_persona = :id");
        $stm_eliminar->execute([':id' => $id]);
        header("Location: index.php?mensaje=registroEliminado");
        exit();
    }
}

if (isset($_REQUEST['submit1'])) {
    $hay_post = true;
    $nombre = isset($_REQUEST['txtNombre']) ? $_REQUEST['txtNombre'] : "";
    $tipoDeGasto = isset($_REQUEST['cmbTipoGasto']) ? $_REQUEST['cmbTipoGasto'] : "";
    $valorDelGasto = isset($_REQUEST['txtValorGasto']) ? $_REQUEST['txtValorGasto'] : "";

    if (!empty($nombre)) {
        $nombre = preg_replace("/[^a-zA-ZáéíóúÁÉÍÓÚ]/u", "", $nombre);
    } else {
        $error .= "El nombre no puede estar vacío.<br>";
    }

    if ($tipoDeGasto == "") {
        $error .= "Seleccione un tipo de Gasto.<br>";
    }

    if ($valorDelGasto <= 0) {
        $error .= "El valor tiene que ser mayor a 0.<br>";
    }

    if (!$error) {
        $stm_insertarRegistro = $cadena_conexion->prepare("INSERT INTO persona(nombrePersona, tipoGasto, valorGasto) VALUES(:nombre, :tipoGasto, :valorGasto)");
        $stm_insertarRegistro->execute([':nombre' => $nombre, ':tipoGasto' => $tipoDeGasto, ':valorGasto' => $valorDelGasto]);
        header("Location: index.php?mensaje=registroGuardado");
        exit();
    }
}

if (isset($_REQUEST['submit2'])) {
    $idPersona = $_REQUEST['id'];
    $hay_post = true;
    $nombre = isset($_REQUEST['txtNombre']) ? $_REQUEST['txtNombre'] : "Editado#1";
    $tipoDeGasto = isset($_REQUEST['cmbTipoGasto']) ? $_REQUEST['cmbTipoGasto'] : "Editado#2";
    $valorDelGasto = isset($_REQUEST['txtValorGasto']) ? $_REQUEST['txtValorGasto'] : 10;

    if (!empty($nombre)) {
        $nombre = preg_replace("/[^a-zA-ZáéíóúÁÉÍÓÚ]/u", "", $nombre);
    } else {
        $error .= "El nombre no puede estar vacío.<br>";
    }

    if ($tipoDeGasto == "") {
        $error .= "Seleccione un tipo de Gasto.<br>";
    }

    if ($valorDelGasto <= 0) {
        $error .= "El valor tiene que ser mayor a 0.<br>";
    }

    if (!$error) {
        $stm_modificar = $cadena_conexion->prepare("UPDATE persona SET nombrePersona = :nombre, tipoGasto = :tipoGasto, valorGasto = :valorGasto WHERE id_persona = :id");
        $stm_modificar->execute([
            ':nombre' => $nombre,
            ':tipoGasto' => $tipoDeGasto,
            ':valorGasto' => $valorDelGasto,
            ':id' => $idPersona
        ]);
        header("Location: index.php?mensaje=registroModificado");
        exit();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Formulario Gastos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-3">FORMULARIO GASTOS FAMILIARES</h1>

    <div class="alert alert-info">
        <strong>Total de los gastos:</strong> $<?php echo number_format($totalGastos, 2); ?>
    </div>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <!-- Campo oculto para enviar el ID al modificar -->
        <?php if($idPersona): ?>
            <input type="hidden" name="id" value="<?php echo $idPersona; ?>">
        <?php endif; ?>

        <label class="form-label">Nombre Completo:</label>
        <input class="form-control" type="text" name="txtNombre" value="<?php echo $nombre ?>"><br>

        <label class="form-label">Tipo de Gasto:</label>
        <select class="form-select" name="cmbTipoGasto">
            <option value="">Seleccione el tipo de gasto</option>
            <option value="Educacion" <?php echo ($tipoDeGasto == 'Educacion') ? 'selected' : '' ?>>Educacion</option>
            <option value="Salud" <?php echo ($tipoDeGasto == 'Salud') ? 'selected' : '' ?>>Salud</option>
            <option value="Transporte" <?php echo ($tipoDeGasto == 'Transporte') ? 'selected' : '' ?>>Transporte</option>
            <option value="Viajes" <?php echo ($tipoDeGasto == 'Viajes') ? 'selected' : '' ?>>Viajes</option>
        </select><br>

        <label class="form-label">Valor del Gasto:</label>
        <input class="form-control" type="text" name="txtValorGasto" value="<?php echo $valorDelGasto ?>"><br>

        <?php
            if ($idPersona) {
                echo '<input class="btn btn-dark" type="submit" value="Modificar" name="submit2">';
            } else {
                echo '<input class="btn btn-primary" type="submit" value="Enviar" name="submit1">';
            }
        ?>
        <a class="btn btn-secondary" href="index.php">Cancelar</a>
    </form>

    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo "<p>$error</p>"; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_REQUEST['mensaje'])): ?>
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <?php
                $mensaje = $_REQUEST['mensaje'];
                if ($mensaje == 'registroGuardado') echo "<p>Registro guardado.</p>";
                elseif ($mensaje == 'registroModificado') echo "<p>Registro modificado.</p>";
                elseif ($mensaje == 'registroEliminado') echo "<p>Registro eliminado.</p>";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Búsqueda -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="mb-3">
        <label for="search" class="form-label">Buscar por Nombre:</label>
        <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_REQUEST['search']) ? $_REQUEST['search'] : ''; ?>">
        <button type="submit" class="btn btn-secondary mt-2">Buscar</button>
    </form>

    <!-- Tabla -->
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo de Gasto</th>
                <th>Valor del Gasto</th>
                <th colspan="2">Acciones</th>
            </tr>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
