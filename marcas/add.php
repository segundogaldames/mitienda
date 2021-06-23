<!-- comienza el codigo del servidor -->
<?php
    // codigo que permite mostrar errores en tiempo de ejecucion
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    // $_POST = es un arreglo asociativo que permite recibir y/o enviar varias variables de un formulario

    session_start();
    //validar que los datos del formulario vienen via POST

    #llamar al archivo de conexion para usar los datos para registrar roles en la tabla roles
    require('../class/conexion.php');
    require('../class/rutas.php');

    if (isset($_POST['confirm']) && $_POST['confirm'] == 1) {
        
        //almacena el nombre de la region desde el formulario
        $nombre = trim(strip_tags($_POST['nombre']));

        if (!$nombre) {
            $msg = 'Debe ingresar el nombre de la marca';
        }else{

            //preguntar si la region ingresada existe en la tabla regiones
            $res = $mbd->prepare("SELECT id FROM marcas WHERE nombre = ?");
            $res->bindParam(1, $nombre);
            $res->execute();
            $marca = $res->fetch();

            if ($marca) {
                $msg = 'La marca ya existe... intente con otra';
            }else{

                //generamos una consulta con opciones de sanitizacion de datos
                $res = $mbd->prepare("INSERT INTO marcas VALUES(null,?,now(),now())");
                //validamos por cada signo de ? el dato que intentamos enviar a la base de datos
                $res->bindParam(1, $nombre);
                //se ejecuta la consulta de insercion de datos
                $res->execute();

                //pregunte si hubo registros ingresados
                $row = $res->rowCount();

                if ($row) {
                    $_SESSION['success'] = 'La marca se ha registrado correctamente';
                    header('Location: index.php');
                }
            }
        }
    }

?>
<!-- aqui comienza el codigo del cliente -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marcas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <!-- llamada a navegador del sitio -->
        <?php include('../partials/menu.php'); ?>
    </header>
    <div class="container">
        <div class="col-md-6 offset-md-3">
            <h1 class="text-center mt-3 text-primary">Nueva Marca</h1>
            <!-- mostrar mensaje de error -->
            <?php if(isset($msg)): ?>
                <p class="alert alert-danger">
                    <?php echo $msg; ?>
                </p>
            <?php endif; ?>

            <form action="" method="post">
                <div class="form-group mb-3">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ingrese nombre de la marca">
                </div>
                <div class="form-group">
                    <input type="hidden" name="confirm" value="1">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                    <a href="index.php" class="btn btn-link">Volver</a>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>