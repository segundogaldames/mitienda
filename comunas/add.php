<?php
require('../class/conexion.php');
require('../class/rutas.php');

//validar que el id de la region exista
if (isset($_GET['id'])) {
   //guardamos este id en un variable
   $id_region = (int) $_GET['id'];

   //validamos el formulario si viene via POST
   if (isset($_POST['confirm']) && $_POST['confirm'] == 1) {
       
        //guardamos y sanitizamos la variable nombre
        $nombre = trim(strip_tags($_POST['nombre']));

        if (!$nombre) {
            $msg = 'Ingrese el nombre de la comuna';
        }else{
            //verificar que la comuna ingresada no exista
            $res = $mbd->prepare("SELECT id FROM comunas WHERE nombre = ?");
            $res->bindParam(1, $nombre);
            $res->execute();

            $comuna = $res->fetch();

            if ($comuna) {
                $msg = 'La comuna ingresada ya existe... intente con otra';
            }else{

                //guardamos los datos en la tabla comunas
                $res = $mbd->prepare("INSERT INTO comunas VALUES(null, ?, ?, now(), now() )");
                $res->bindParam(1, $nombre);
                $res->bindParam(2, $id_region);
                $res->execute();

                //rescatar el numero de filas afectadas
                $row = $res->rowCount();

                if ($row) {
                    $msg = 'ok';
                    header('Location: index.php?m=' . $msg);
                }
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
                <h1>Nueva Comuna</h1>

                <!-- mensajes de validacion y errores -->
                <?php if(isset($msg)): ?>
                    <p class="alert alert-danger">
                        <?php echo $msg; ?>
                    </p>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="form-group mb-3">
                        <label for="">Comuna <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ingrese el nombre de la comuna">
                    </div>
                    <div class="form-group mb-3">
                        <input type="hidden" name="confirm" value="1">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="../regiones/show.php?id=<?php echo $id_region; ?>" class="btn btn-link">Volver</a>
                    </div>
                </form>
            </div>
            

        </section>

        <!-- pie de pagina -->
        <footer>
            footer
        </footer>
    </div>
</body>
</html>
