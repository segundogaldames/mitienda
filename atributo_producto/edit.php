<!-- comienza el codigo del servidor -->
<?php
    // codigo que permite mostrar errores en tiempo de ejecucion
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();
    
    require('../class/conexion.php');
    require('../class/rutas.php');

    //validar la existencia de la variable id que viene via GET (url)
    if (isset($_GET['id'])) {
        
        $id = (int) $_GET['id']; //parsear la variable id a numero entero

        //preguntamos si existe el id enviado via GET en la tabla regiones
        $res = $mbd->prepare("SELECT tp.id, tp.valor, tp.producto_id, a.nombre FROM atributo_producto tp INNER JOIN atributos a ON tp.atributo_id = a.id WHERE tp.id = ?");
        $res->bindParam(1, $id);
        $res->execute();
        $atrib_prod = $res->fetch();

        //validar que el formulario viene via POST
        if (isset($_POST['confirm']) && $_POST['confirm'] == 1) {
            
            $valor = trim(strip_tags($_POST['valor']));

            if (!$valor) {
                $msg = 'Ingrese el valor del atributo';
            }else{
                $res = $mbd->prepare("UPDATE atributo_producto SET valor = ? WHERE id = ?");
                $res->bindParam(1, $valor);
                $res->bindParam(2, $id);
                $res->execute();

                $row = $res->rowCount();

                if ($row) {
                    $_SESSION['success'] = 'El valor del atributo se ha modificado correctamente';
                    header('Location: ../productos/show.php?id=' . $atrib_prod['producto_id'] );
                    // header('Location: ' . PRODUCTOS . 'show.php?id=' . $atrib_prod['producto_id']);
                }
            }
            //print_r($_POST);exit;
        }

        //print_r($marca);exit;
    }

?>
<!-- aqui comienza el codigo del cliente -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atributo-Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <!-- llamada a naveador del sitio -->
        <?php include('../partials/menu.php'); ?>
    </header>
    <div class="container">
        <div class="col-md-6 offset-md-3">
            <h2 class="text-center mt-3 text-primary">Editar Atributo</h2>

            <!-- validar que el rol existe     -->
            <?php if($atrib_prod): ?>
                <form action="" method="post">
                    <div class="form-group mb-3">
                        <label for="valor">Valor <span class="text-danger">*</span></label>
                        <input type="text" name="valor" value="<?php echo $atrib_prod['valor']; ?>" class="form-control" placeholder="Ingrese valor del atributo">
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="confirm" value="1">
                        <button type="submit" class="btn btn-primary">Editar</button>
                        <a href="../productos/show.php?id=<?php echo $$atrib_prod['producto_id']; ?>" class="btn btn-link">Volver</a>
                    </div>
                </form>
            <?php else: ?>
                
                <p class="text-info">El dato no existe</p>
            
            <?php endif; ?>
           
        </div>
        
    </div>
    
</body>
</html>