<!-- comienza el codigo del servidor -->
<?php
    // codigo que permite mostrar errores en tiempo de ejecucion
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    session_start();

    require('../class/conexion.php');
    require('../class/rutas.php');

    //consultar por la lista de regiones registrada en la tabla roles
    $res = $mbd->query("SELECT p.id, p.sku, p.nombre, p.activo, m.nombre as marca, tp.nombre as tipo FROM productos p INNER JOIN marcas m ON p.marca_id = m.id INNER JOIN producto_tipos tp ON p.producto_tipo_id = tp.id ORDER BY p.nombre");
    $productos = $res->fetchall();

    //print_r($roles);

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
        <div class="col-md-10 offset-md-1">
            <h2 class="text-center mt-3 text-primary">Productos</h2>
            <!-- generacion de mensajes de exito o error -->
           <?php include('../partials/mensajes.php') ?>

            <table class="table table-hover">
                <tr>
                    <th>SKU</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Producto Tipo</th>
                    <th>Estado</th>
                </tr>
                <?php foreach($productos as $producto): ?>
                    <tr>
                        <td> <a href="show.php?id=<?php echo $producto['id'] ?>"><?php echo $producto['sku'] ?></a> </td>
                        <td> <?php echo $producto['nombre'] ?></td>
                        <td> <?php echo $producto['marca'] ?></td>
                        <td> <?php echo $producto['tipo'] ?></td>
                        <td>
                            <?php if($producto['activo'] == 1): ?>
                                Activo
                            <?php else: ?>
                                Inactivo
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <a href="add.php" class="btn btn-primary">Nuevo Producto</a>
        </div>
        
    </div>
    
</body>
</html>