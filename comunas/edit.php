<?php
require('../class/conexion.php');
require('../class/rutas.php');

//validar que el id de la comuna exista
if (isset($_GET['id'])) {
   //guardamos este id en un variable
   $id = (int) $_GET['id'];

   //consultar por la comuna de acuerdo al id recibido
   //creamos la consulta a la tabla comunas de acuerdo al id ingresado por el sistema
   $res = $mbd->prepare("SELECT c.id, c.nombre as comuna, c.region_id, r.nombre as region FROM comunas as c INNER JOIN regiones as r ON c.region_id = r.id WHERE c.id = ?");
   $res->bindParam(1, $id);
   $res->execute();
   $comuna = $res->fetch(); //pido a PDO que disponibilice la comuna consultada por id

   //lista de regiones
   $res = $mbd->query("SELECT id, nombre FROM regiones ORDER BY nombre");
   $regiones = $res->fetchall();

   //validamos el formulario si viene via POST
   if (isset($_POST['confirm']) && $_POST['confirm'] == 1) {
       
        //guardamos y sanitizamos la variable nombre
        $nombre = trim(strip_tags($_POST['nombre']));
        $region = (int) $_POST['region'];

        if (!$nombre) {
            $msg = 'Ingrese el nombre de la comuna';
        }elseif($region <= 0){
            $msg = 'Seleccione una región';
        }else {
            //procedemos a actualizar los datos de la tabla comunas segun id
            $res = $mbd->prepare("UPDATE comunas SET nombre = ?, region_id = ?, updated_at = now() WHERE id = ?");
            $res->bindParam(1, $nombre);
            $res->bindParam(2, $region);
            $res->bindParam(3, $id);
            $res->execute();

            $row = $res->rowCount();

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
    <title>Comunas</title>
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
                <h1>Editar Comuna</h1>

                <!-- mensajes de validacion y errores -->
                <?php if(isset($msg)): ?>
                    <p class="alert alert-danger">
                        <?php echo $msg; ?>
                    </p>
                <?php endif; ?>

                <?php if($comuna): ?>
                    <form action="" method="post">
                        <div class="form-group mb-3">
                            <label for="">Comuna <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" value="<?php echo $comuna['comuna']; ?>" class="form-control" placeholder="Ingrese el nombre de la comuna">
                        </div>
                        <div class="form-group mb-3">
                            <label for="">Región <span class="text-danger">*</span></label>
                            <select name="region" class="form-control">
                                <option value="<?php echo $comuna['region_id']; ?>">
                                    <?php echo $comuna['region']; ?>
                                    </option>

                                <?php foreach($regiones as $region): ?>
                                    <option value="<?php echo $region['id']; ?>">
                                        <?php echo $region['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <input type="hidden" name="confirm" value="1">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="show.php?id=<?php echo $id; ?>" class="btn btn-link">Volver</a>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="text-info">El dato no existe</p>
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
