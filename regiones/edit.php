<?php
//visualizar errores en php en tiempo de ejecucion
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//llamada al archivo conexion para disponer de los datos de la base de datos
require('../class/conexion.php');
require('../class/rutas.php');

//validar la variable GET id
if (isset($_GET['id'])) {
    
    //recuperar el dato que viene en la variable id
    $id = (int) $_GET['id']; //transforma el dato $_GET a entero

    //print_r($id);exit;

    //consultar si hay una region con el id enviado por GET
    $res = $mbd->prepare("SELECT id, nombre, created_at, updated_at FROM regiones WHERE id = ?");
    $res->bindParam(1, $id);
    $res->execute();
    $region = $res->fetch();

    //validar formulario
    if (isset($_POST['confirm']) && $_POST['confirm'] == 1 ) {
        # guardamos en la variable nombre el dato nombre que viene del formulario...
        $nombre = trim(strip_tags($_POST['nombre']));

        if (!$nombre) {
            $msg = 'Debe ingresar el nombre de la región';
        }else{
            //procedemos a actualizar el dato ingresado por el usuario en la tabla regiones
            $res = $mbd->prepare("UPDATE regiones SET nombre = ?, updated_at = now() WHERE id = ?");
            $res->bindParam(1, $nombre);
            $res->bindParam(2, $id);
            $res->execute();

            $row = $res->rowCount();//recuperamos el numero de filas afectadas por la consulta

            if ($row) {
                $msg = 'ok';
                header('Location: show.php?id=' . $id . '&m=' . $msg);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regiones</title>
    <!--Enlaces CDN de Bootstrap-->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script> -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/bootstrap.min.js"></script>
</head>
<body>
    
    <div class="container">
        <!-- seccion de cabecera del sitio -->
        <header>
            <!-- navegador principal -->
            <?php include('../partials/menu.php'); ?>
        </header>

        <!-- seccion de contenido principal -->
        <section>
            <div class="col-md-6 offset-md-3">
                <h1>Editar Región</h1>
                <!-- mensaje de error de roles -->
                <?php if(isset($msg)): ?>
                    <p class="alert alert-danger">
                        <?php echo $msg; ?>
                    </p>
                <?php endif; ?>
                
                <!-- listar los roles que estan registrados -->
                <?php if($region): ?>
                    <form action="" method="post">
                        <div class="form-group mb-3">
                            <label for="">Región <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" value="<?php echo $region['nombre']; ?>" class="form-control" placeholder="Ingrese el nombre de la región">
                        </div>
                        <div class="form-group mb-3">
                            <input type="hidden" name="confirm" value="1">
                            <button type="submit" class="btn btn-primary">Editar</button>
                            <a href="show.php?id=<?php echo $region['id']; ?>" class="btn btn-link">Volver</a>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="text-info">El dato solicitado no existe</p>
                <?php endif; ?>
            </div>
            
        </section>

        <!-- pie de pagina -->
        <footer>
            footer
        </footer>
    </div>
</body>
</html>
