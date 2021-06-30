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
        $res = $mbd->prepare("SELECT p.id, p.sku, p.nombre, p.precio, p.activo, p.created_at, p.updated_at, m.nombre as marca, tp.nombre as tipo FROM productos p INNER JOIN marcas m ON p.marca_id = m.id INNER JOIN producto_tipos tp ON p.producto_tipo_id = tp.id WHERE p.id = ?");
        $res->bindParam(1, $id);
        $res->execute();
        $producto = $res->fetch();

        //lista de atributos de este producto
        $res = $mbd->prepare("SELECT ap.id, a.nombre, ap.valor FROM atributos a INNER JOIN atributo_producto ap ON a.id = ap.atributo_id WHERE ap.producto_id = ?");
        $res->bindParam(1, $id);
        $res->execute();
        $atributos = $res->fetchall(); 

        //lista de imagenes
        $res = $mbd->prepare("SELECT id FROM imagenes WHERE producto_id = ?");
        $res->bindParam(1, $id);
        $res->execute();
        $imagenes = $res->fetchall();
        //print_r($producto);exit;

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
            <h2 class="text-center mt-3 text-primary">Productos</h2>
            <!-- generacion de mensaje de exito -->
            <?php include('../partials/mensajes.php'); ?>

            <!-- validar que el producto existe     -->
            <?php if($producto): ?>
                
                <table class="table table-hover">
                    <tr>
                        <th>SKU:</th>
                        <td> <?php echo $producto['sku']; ?>  </td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td> <?php echo $producto['nombre']; ?>  </td>
                    </tr>
                    <tr>
                        <th>Precio:</th>
                        <td>$ <?php echo number_format($producto['precio'],0,',','.'); ?>  </td>
                    </tr>
                    <tr>
                        <th>Marca:</th>
                        <td> <?php echo $producto['marca']; ?>  </td>
                    </tr>
                    <tr>
                        <th>Producto Tipo:</th>
                        <td> <?php echo $producto['tipo']; ?>  </td>
                    </tr>
                    <tr>
                        <th>Activo:</th>
                        <td>
                            <?php if($producto['activo'] == 1): ?>
                                Si
                            <?php else: ?>
                                No
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Imágenes:</th>
                        <td>
                            <?php if(count($imagenes)): ?>
                                <a href="showImages.php?id_producto=<?php echo $id; ?>" title="Ver Imágenes">
                                    <?php echo count($imagenes); ?> imagen(es)
                                </a>
                                
                            <?php else: ?>
                                Sin imágenes
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Creado:</th>
                        <td> 
                            <?php 
                                //transformamos la fecha de la tabla regiones en una fecha valida para php
                                $fecha = new DateTime($producto['created_at']);
                                echo $fecha->format('d-m-Y H:i:s'); 
                            ?>  
                        </td>
                    </tr>
                    <tr>
                        <th>Actualizado:</th>
                        <td> 
                            <?php 
                                //transformamos la fecha de la tabla regiones en una fecha valida para php
                                $fecha = new DateTime($producto['updated_at']);
                                echo $fecha->format('d-m-Y H:i:s'); 
                            ?>  
                        </td>
                    </tr>
                </table>
                <p>
                    <a href="index.php" class="btn btn-link">Volver</a>
                    <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary">Editar</a>
                    <a href="../atributo_producto/add.php?id_producto=<?php echo $id; ?>" class="btn btn-secondary">Agregar Atributo</a>
                    <a href="../imagenes/add.php?id_producto=<?php echo $id; ?>" class="btn btn-success">Agregar Imagen</a>
                </p>
            <?php else: ?>
                
                <p class="text-info">El dato no existe</p>
            
            <?php endif; ?>
           
        </div>
        <div class="col-md-6 offset-md-3">
            <h4 class="text-center mt-3 text-primary">Atributos de <?php echo $producto['nombre']; ?></h4>
            <table class="table table-hover">
                <tr>
                    <th>Atributo</th>
                    <th>Valor</th>
                    <th></th>
                </tr>
                <?php foreach($atributos as $atributo): ?>
                    <tr>
                        <td><?php echo $atributo['nombre']; ?></td>
                        <td><?php echo $atributo['valor']; ?></td>
                        <td>
                            <a href="<?php echo ATRIBUTO_PRODUCTO . 'edit.php?id=' . $atributo['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="<?php echo ATRIBUTO_PRODUCTO . 'delete.php?id=' . $atributo['id']; ?>" class="btn btn-warning btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    
</body>
</html>