<?php
    //echo uniqid();exit;
    //visualizar errores en php en tiempo de ejecucion
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    //print_r($_SESSION);
    
    require('class/conexion.php');
    require('class/rutas.php');

    //lista de productos con sus imagenes con productos activos e imagenes activas y en portada
    $res = $mbd->query("SELECT i.imagen, p.id, p.nombre, p.precio, m.nombre as marca, tp.nombre as tipo FROM imagenes i INNER JOIN productos p ON i.producto_id = p.id INNER JOIN marcas m ON p.marca_id = m.id INNER JOIN producto_tipos tp ON p.producto_tipo_id = tp.id WHERE i.activo = 1 AND i.portada = 1 AND p.activo = 1 ORDER BY p.precio");
    $productos = $res->fetchall();

    //lista de marcas
    $res = $mbd->query("SELECT id, nombre FROM marcas ORDER BY nombre");
    $marcas = $res->fetchall();

    //lista de tipos
    $res = $mbd->query("SELECT id, nombre FROM producto_tipos ORDER BY nombre");
    $tipos = $res->fetchall();

    //print_r($productos);exit;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Primera Tienda</title>
    <!--Enlaces CDN de Bootstrap-->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script> -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
    
    <div class="container">
        <!-- seccion de cabecera del sitio -->
        <header>
            <!-- navegador principal -->
            <?php include('partials/menu.php'); ?>
        </header>

        <!-- seccion de contenido principal -->
        <section>
            <?php include('partials/mensajes.php'); ?>
            
            <div class="row">
                <div class="col-md-3">
                    <h5 class="text-info">Marcas</h5>
                    <div class="list-group">
                        <?php foreach($marcas as $marca): ?>
                            <a href="<?php echo PRODUCTOS . 'productoMarcas.php?marca=' . $marca['id']; ?>" class="list-group-item list-group-item-action">
                                <?php echo $marca['nombre']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <hr class="text-info">
                    <h5 class="text-info">Categorías</h5>
                    <div class="list-group">
                        <?php foreach($tipos as $tipo): ?>
                            <a href="<?php echo PRODUCTOS . 'productoTipos.php?tipo=' . $tipo['id']; ?>" class="list-group-item list-group-item-action">
                                <?php echo $tipo['nombre']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <?php foreach($productos as $producto): ?>
                            <div class="col-md-3 text-center m-2">
                                <p class="h6 text-primary">
                                    <?php echo $producto['nombre']; ?>
                                </p>
                                <img src="<?php echo PRODUCTOS . 'img/' . $producto['imagen']; ?>" alt="" width="190" height="120">
                                <p class="h4 text-primary mt-2">
                                    $ <?php echo number_format($producto['precio'],0,',','.'); ?>
                                </p>
                                <p class="h5 text-primary">
                                    <?php echo $producto['marca']; ?>
                                </p>
                                <p class="h5 text-primary">
                                    <?php echo $producto['tipo']; ?>
                                </p>
                                <p>
                                    <a href="<?php echo PRODUCTOS . 'cotizar.php?id=' . $producto['id']; ?>" class="btn btn-secondary">Cotizar</a>
                                </p>
                            </div>
                            
                        <?php endforeach; ?>
                    </div>
                </div>
                
            </div>
            
        </section>

        <!-- pie de pagina -->
        <footer>
            footer
        </footer>
    </div>
</body>
</html>
