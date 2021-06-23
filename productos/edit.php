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
        $res = $mbd->prepare("SELECT p.id, p.sku, p.nombre, p.precio, p.activo, p.marca_id, p.producto_tipo_id, p.created_at, p.updated_at, m.nombre as marca, tp.nombre as tipo FROM productos p INNER JOIN marcas m ON p.marca_id = m.id INNER JOIN producto_tipos tp ON p.producto_tipo_id = tp.id WHERE p.id = ?");
        $res->bindParam(1, $id);
        $res->execute();
        $producto = $res->fetch();

        //lista de marcas
        $res = $mbd->query("SELECT id, nombre FROM marcas ORDER BY nombre");
        $marcas = $res->fetchall();

        //lista de productos tipos
        $res = $mbd->query("SELECT id, nombre FROM producto_tipos ORDER BY nombre");
        $tipos = $res->fetchall();

        //validar que el formulario viene via POST
        if (isset($_POST['confirm']) && $_POST['confirm'] == 1) {
            
            //almacena el nombre de la region desde el formulario
            $sku = trim(strip_tags($_POST['sku']));
            $nombre = trim(strip_tags($_POST['nombre']));
            $precio = (int) $_POST['precio'];
            $marca = (int) $_POST['marca'];
            $tipo = (int) $_POST['tipo'];
            $activo = (int) $_POST['activo'];

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
            }elseif ($activo <= o) {
                $msg = 'Seleccione un estado';
            }else{
                //generamos una consulta con opciones de sanitizacion de datos
                $res = $mbd->prepare("UPDATE productos SET sku = ?, nombre = ?, precio = ?, activo = ?, marca_id = ?, producto_tipo_id = ?, updated_at = now() WHERE id = ?");
                    //validamos por cada signo de ? el dato que intentamos enviar a la base de datos
                $res->bindParam(1, $sku);
                $res->bindParam(2, $nombre);
                $res->bindParam(3, $precio);
                $res->bindParam(4, $activo);
                $res->bindParam(5, $marca);
                $res->bindParam(6, $tipo);
                $res->bindParam(7, $id);
                //se ejecuta la consulta de insercion de datos
                $res->execute();
                //pregunte si hubo registros ingresados
                $row = $res->rowCount();

                if ($row) {
                    $_SESSION['success'] = 'El producto se ha modificado correctamente';
                    header('Location: show.php?id=' . $id);
                }
            }

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
    <title>Productos</title>
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
            <h2 class="text-center mt-3 text-primary">Editar Producto</h2>

            <!-- validar que el rol existe     -->
            <?php if($producto): ?>
                <form action="" method="post">
                    <div class="form-group mb-3">
                        <label for="sku">SKU <span class="text-danger">*</span></label>
                        <input type="text" name="sku" value="<?php echo $producto['sku']; ?>" class="form-control" placeholder="Ingrese SKU del producto">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" class="form-control" placeholder="Ingrese nombre del producto">
                    </div>
                    <div class="form-group mb-3">
                        <label for="precio">Precio <span class="text-danger">*</span></label>
                        <input type="number" name="precio" value="<?php echo $producto['precio']; ?>" class="form-control" placeholder="Ingrese precio del producto">
                    </div>
                    <div class="form-group mb-3">
                        <label for="marca">Marca <span class="text-danger">*</span></label>
                        <select name="marca" class="form-control">
                            <option value="<?php echo $producto['marca_id']; ?>">
                                <?php echo $producto['marca']; ?>
                            </option>
                            
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
                            <option value="<?php echo $producto['producto_tipo_id'] ?>">
                                <?php echo $producto['tipo'] ?>
                            </option>
                            
                            <?php foreach($tipos as $tipo): ?>
                                <option value="<?php echo $tipo['id']; ?>">
                                    <?php echo $tipo['nombre']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="activo">Activo <span class="text-danger">*</span></label>
                        <select name="activo" class="form-control">
                            <option value="<?php echo $producto['activo'] ?>">
                                <?php if($producto['activo'] == 1): ?>
                                    Activo
                                <?php else: ?>
                                    Inactivo
                                <?php endif; ?>
                            </option>
                            
                            <option value="1">Activar</option>
                            <option value="2">Desactivar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="confirm" value="1">
                        <button type="submit" class="btn btn-primary">Editar</button>
                        <a href="show.php?id=<?php echo $id; ?>" class="btn btn-link">Volver</a>
                    </div>
                </form>
            <?php else: ?>
                
                <p class="text-info">El dato no existe</p>
            
            <?php endif; ?>
           
        </div>
        
    </div>
    
</body>
</html>