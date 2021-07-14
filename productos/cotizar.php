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

        //preguntamos si existe el id enviado via GET en la tabla productos
        $res = $mbd->prepare("SELECT p.id, p.sku, p.nombre, p.precio, m.nombre as marca, tp.nombre as tipo FROM productos p INNER JOIN marcas m ON p.marca_id = m.id INNER JOIN producto_tipos tp ON p.producto_tipo_id = tp.id WHERE p.id = ?");
        $res->bindParam(1, $id);
        $res->execute();
        $producto = $res->fetch();

        //print_r($producto);exit;

        //lista de atributos de este producto
        $res = $mbd->prepare("SELECT ap.id, a.nombre, ap.valor FROM atributos a INNER JOIN atributo_producto ap ON a.id = ap.atributo_id WHERE ap.producto_id = ?");
        $res->bindParam(1, $id);
        $res->execute();
        $atributos = $res->fetchall(); 

        //lista de imagenes
        $res = $mbd->prepare("SELECT id, imagen FROM imagenes WHERE producto_id = ? AND activo = 1");
        $res->bindParam(1, $id);
        $res->execute();
        $imagenes = $res->fetchall();
        //print_r($producto);exit;

        if (isset($_POST['confirm']) && $_POST['confirm'] == 1) {
            if (isset($_SESSION['autenticado'])) {
                $prod = (int) $_POST['producto'];
                $cantidad = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);

                if (!$cantidad || $cantidad <= 0) {
                    $msg = 'Ingrese una cantidad válida';
                }else {
                    //verificar que el usuario no tenga ya cotizado un mismo producto
                    $res = $mbd->prepare("SELECT id FROM carro_compras WHERE producto_id = ? AND usuario_id = ?");
                    $res->bindParam(1, $prod);
                    $res->bindParam(2, $_SESSION['usuario_id']);
                    $res->execute();

                    $compra = $res->fetch();

                    if($compra){
                        $msg = 'Este producto ya ha sido cotizado por usted... intente con otro';
                    }else{
                        //registramos la cotizacion
                        //estados: 1 => pendiente, 2 => confirmado
                        $res = $mbd->prepare("INSERT INTO carro_compras(producto_id, usuario_id, cantidad, estado, created_at, updated_at) VALUES(?, ?, ?, 1, now(), now() ) ");
                        $res->bindParam(1, $prod);
                        $res->bindParam(2, $_SESSION['usuario_id']);
                        $res->bindParam(3, $cantidad);
                        $res->execute();

                        $row = $res->rowCount();

                        if ($row) {
                            $_SESSION['compra'] = true;
                            $_SESSION['success'] = 'El producto fue agregado correctamente';
                            header('Location: ' . BASE_URL);
                        }
                    }
                }       
            }else{
                $msg = 'Debes iniciar sesion o registrarte para continuar';
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
    <style>
        .zoom:hover{
            -webkit-transform:scale(3.0);
            transform:scale(3.0);
            transition-duration: 1s;
        }
        .zoom {
            overflow:hidden;
        }
    </style>
</head>
<body>
    <header>
        <!-- llamada a navegador del sitio -->
        <?php include('../partials/menu.php'); ?>
    </header>
    <div class="container">
        <div class="col-md-10 offset-md-1">
            <h2 class="text-center mt-3 text-primary">Cotizar</h2>

            <!-- validar que el producto existe     -->
            <?php if(!empty($producto)): ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <?php foreach($imagenes as $imagen): ?>
                                <div class="col-md-4">
                                    <img src="<?php echo PRODUCTOS . 'img/' . $imagen['imagen'];?>" alt="" class="img-thumbnail zoom">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                    </div>
                    <!-- seccion de muestra de datos -->
                    <div class="col-md-8">
                        <table class="table table-hover">
                        <tr>
                            <th>Precio:</th>
                            <td>$ <?php echo number_format($producto['precio'],0,',','.'); ?>  </td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td> <?php echo $producto['nombre']; ?>  </td>
                        </tr>
                        
                        <tr>
                            <th>Marca:</th>
                            <td> <?php echo $producto['marca']; ?>  </td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td> <?php echo $producto['tipo']; ?>  </td>
                        </tr>
                    </table>
                    </div>
                </div>
 
                <div class="col-md-12">
                    <?php if(isset($msg)): ?>
                        <p class="alert alert-danger"><?php echo $msg; ?></p>
                    <?php endif; ?>

                    <form action="" class="form-inline" method="post">
                        <div class="form-group mb-2">
                            <input type="hidden" name="producto" value="<?php echo $producto['id']; ?>">
                            <input type="hidden" name="confirm" value="1">
                            <input type="number" class="" name="cantidad" value="1"> 
                            <button type="submit" class="btn btn-success btn-sm">Agregar</button>
                        </div>
                    </form>
                    <a href="<?php echo BASE_URL; ?>" class="btn btn-link">Volver</a>
                </div>
                   
                
            <?php else: ?>
                
                <p class="text-info">El dato no existe</p>
            
            <?php endif; ?>
           <hr>
        </div>
        <div class="col-md-4 offset-md-2">
            <h4 class="text-center mt-3 text-primary">Ficha Técnica</h4>
            <hr>
            <table class="table table-hover">
                <?php foreach($atributos as $atributo): ?>
                    <tr>
                        <td><?php echo $atributo['nombre']; ?>:</td>
                        <td><?php echo $atributo['valor']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    
</body>
</html>