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

    //lista de marcas
    $res = $mbd->query("SELECT id, nombre FROM marcas ORDER BY nombre");
    $marcas = $res->fetchall();

    //lista de productos tipos
    $res = $mbd->query("SELECT id, nombre FROM producto_tipos ORDER BY nombre");
    $tipos = $res->fetchall();

    if (isset($_POST['confirm']) && $_POST['confirm'] == 1) {
        
        //almacena el nombre de la region desde el formulario
        $sku = trim(strip_tags($_POST['sku']));
        $nombre = trim(strip_tags($_POST['nombre']));
        $precio = (int) $_POST['precio'];
        $marca = (int) $_POST['marca'];
        $tipo = (int) $_POST['tipo'];

        if (strlen($sku) < 5) {
            $msg = 'Debe ingresar el sku del producto';
        }elseif (!$nombre) {
            $msg = 'Ingrese el nombre del producto';
        }elseif ($precio <= 0) {
            $msg = 'Ingrese el precio del producto';
        }elseif ($marca <= 0) {
            $msg = 'Seleccione la marca';
        }elseif ($tipo <= 0) {
            $msg = 'Seleccione el tipo de producto';
        }else{

            //preguntar si el producto ingresado existe en la tabla productos
            $res = $mbd->prepare("SELECT id FROM productos WHERE sku = ?");
            $res->bindParam(1, $sku);
            $res->execute();
            $producto = $res->fetch();

            if ($producto) {
                $msg = 'El producto ya existe... intente con otro';
            }else{

                //generamos una consulta con opciones de sanitizacion de datos
                $res = $mbd->prepare("INSERT INTO productos VALUES(null,?, ?, ?, 1, ?, ?, now(),now() )");
                //validamos por cada signo de ? el dato que intentamos enviar a la base de datos
                $res->bindParam(1, $sku);
                $res->bindParam(2, $nombre);
                $res->bindParam(3, $precio);
                $res->bindParam(4, $marca);
                $res->bindParam(5, $tipo);
                //se ejecuta la consulta de insercion de datos
                $res->execute();

                //pregunte si hubo registros ingresados
                $row = $res->rowCount();

                if ($row) {
                    $_SESSION['success'] = 'El producto se ha registrado correctamente';
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
    <title>Productos</title>
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
            <h1 class="text-center mt-3 text-primary">Nuevo Producto</h1>
            <!-- mostrar mensaje de error -->
            <?php if(isset($msg)): ?>
                <p class="alert alert-danger">
                    <?php echo $msg; ?>
                </p>
            <?php endif; ?>

            <form action="" method="post">
                <div class="form-group mb-3">
                    <label for="sku">SKU <span class="text-danger">*</span></label>
                    <input type="text" name="sku" value="<?php if(isset($_POST['sku'])) echo $_POST['sku'] ?>" class="form-control" placeholder="Ingrese SKU del producto">
                </div>
                <div class="form-group mb-3">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" value="<?php if(isset($_POST['sku'])) echo $_POST['nombre'] ?>" class="form-control" placeholder="Ingrese nombre del producto">
                </div>
                <div class="form-group mb-3">
                    <label for="precio">Precio <span class="text-danger">*</span></label>
                    <input type="number" name="precio" value="<?php if(isset($_POST['sku'])) echo $_POST['precio'] ?>" class="form-control" placeholder="Ingrese precio del producto">
                </div>
                <div class="form-group mb-3">
                    <label for="marca">Marca <span class="text-danger">*</span></label>
                    <select name="marca" class="form-control">
                        <option value="">Seleccione...</option>
                        
                        <?php foreach($marcas as $marca): ?>
                            <option value="<?php echo $marca['id']; ?>">
                                <?php echo $marca['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="tipo">Producto tipo <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-control">
                        <option value="">Seleccione...</option>
                        
                        <?php foreach($tipos as $tipo): ?>
                            <option value="<?php echo $tipo['id']; ?>">
                                <?php echo $tipo['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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