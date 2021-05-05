<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../class/conexion.php');
require('../class/rutas.php');

//lista de roles
$res = $mbd->query("SELECT id, nombre FROM roles ORDER BY nombre");
$roles = $res->fetchall();

//lista de comunas
$res = $mbd->query("SELECT id, nombre FROM comunas ORDER BY nombre");
$comunas = $res->fetchall();

//validamos el formulario
if (isset($_POST['confirm']) && $_POST['confirm'] == 1) {
   
    $nombre = trim(strip_tags($_POST['nombre']));
    $rut = trim(strip_tags($_POST['rut']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $direccion = trim(strip_tags($_POST['direccion']));
    $comuna = (int) $_POST['comuna'];
    $fecha_nac = trim(strip_tags($_POST['fecha_nac']));
    $telefono = (int) $_POST['telefono'];
    $rol = (int) $_POST['rol'];

    //print_r($_POST);exit;

    if (!$nombre || strlen($nombre) < 5) {
       $msg = 'Ingrese al menos 5 caracteres en el nombre de la persona';
    }elseif(!$rut || strlen($rut) < 9){
        $msg = 'Ingrese un RUT no es válido';
    }elseif (!$email) {
        $msg = 'Ingrese un email válido';
    }elseif (!$direccion) {
        $msg = 'Ingrese la dirección';
    }elseif ($comuna <= 0) {
        $msg = 'Seleccione una comuna';
    }elseif (!$fecha_nac) {
        $msg = 'Ingrese una fecha de nacimiento';
    }elseif ($telefono <= 0 || strlen($telefono) < 9) {
        $msg = 'Ingrese un número de telefono de al menos 9 dígitos';
    }elseif ($rol <= 0) {
        $msg = 'Seleccione un rol';
    }else {
        //verificar que la persona ingresada no este en la tabla personas
        //validar registro de email
        $res = $mbd->prepare("SELECT id FROM personas WHERE email = ?");
        $res->bindParam(1, $email);
        $res->execute();

        $persona = $res->fetch();

        if ($persona) {
            $msg = 'Esta persona ya está registrada en sistema... intente con otra';
        }else {
            //procedemos a registrar los datos de la persona
            $res = $mbd->prepare("INSERT INTO personas VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?, now(), now() ) ");
            $res->bindParam(1, $nombre);
            $res->bindParam(2, $rut);
            $res->bindParam(3, $email);
            $res->bindParam(4, $direccion);
            $res->bindParam(5, $fecha_nac);
            $res->bindParam(6, $telefono);
            $res->bindParam(7, $rol);
            $res->bindParam(8, $comuna);
            $res->execute();

            $row = $res->rowCount();

            if($row){
                $msg = 'ok';
                header('Location: index.php?m=' . $msg);
            }
        }
    }


    /* echo '<pre>';
    print_r($_POST);exit;
    echo '</pre>'; */
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personas</title>
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
                <h1>Nueva Persona</h1>

                <!-- mensajes de validacion y errores -->
                <?php if(isset($msg)): ?>
                    <p class="alert alert-danger">
                        <?php echo $msg; ?>
                    </p>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="form-group mb-3">
                        <label for="">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" value="<?php if(isset($_POST['nombre'])) echo $_POST['nombre']; ?>" class="form-control" placeholder="Ingrese el nombre de la persona">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">RUT <span class="text-danger">*</span></label>
                        <input type="text" name="rut" value="<?php if(isset($_POST['rut'])) echo $_POST['rut']; ?>" class="form-control" placeholder="Ingrese el RUT de la persona">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" class="form-control" placeholder="Ingrese el email de la persona">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Dirección <span class="text-danger">*</span></label>
                        <input type="text" name="direccion" value="<?php if(isset($_POST['direccion'])) echo $_POST['direccion']; ?>" class="form-control" placeholder="Ingrese la dirección de la persona (calle y número)">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Comuna <span class="text-danger">*</span></label>
                        <select name="comuna" class="form-control">
                            <option value="">Seleccione...</option>

                            <?php foreach($comunas as $comuna): ?>
                                <option value="<?php echo $comuna['id']; ?>">
                                    <?php echo $comuna['nombre']; ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Fecha de nacimiento <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_nac" value="<?php if(isset($_POST['fecha_nac'])) echo $_POST['fecha_nac']; ?>" class="form-control" placeholder="Ingrese la fecha de nacimiento de la persona">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Teléfono <span class="text-danger">*</span></label>
                        <input type="number" name="telefono" value="<?php if(isset($_POST['telefono'])) echo $_POST['telefono']; ?>" class="form-control" placeholder="Ingrese el teléfono de la persona (solo números)">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Rol <span class="text-danger">*</span></label>
                        <select name="rol" class="form-control">
                            <option value="">Seleccione...</option>

                            <?php foreach($roles as $rol): ?>
                                <option value="<?php echo $rol['id']; ?>">
                                    <?php echo $rol['nombre']; ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <input type="hidden" name="confirm" value="1">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="index.php" class="btn btn-link">Volver</a>
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
