<?php
//visualizar errores en php en tiempo de ejecucion
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

//llamada al archivo conexion para disponer de los datos de la base de datos
require('../class/conexion.php');
require('../class/rutas.php');

//creamos la consulta a la tabla roles ordenados por nombre de manera ascendente para usar esos datos
$res = $mbd->query("SELECT id, nombre FROM roles ORDER BY nombre");
$roles = $res->fetchall(); //pido a PDO que disponibilice todos los roles registrados

//print_r($roles);

?>
<?php if(isset($_SESSION['autenticado']) && $_SESSION['usuario_rol'] == 3): ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles</title>
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
                <h1>Roles</h1>
                <!-- mensaje de registro de roles -->
                <?php include('../partials/mensajes.php'); ?>
                
                <!-- listar los roles que estan registrados -->
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($roles as $rol): ?>
                            <tr>
                                <td> <?php echo $rol['id']; ?> </td>
                                <td> 
                                    <a href="show.php?id=<?php echo $rol['id']; ?>"> 
                                        <?php echo $rol['nombre']; ?> 
                                    </a> 
                                </td>
                            </tr>
                            
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- lista de roles -->
                <a href="add.php" class="btn btn-success">Nuevo Rol</a>
            </div>
            
        </section>

        <!-- pie de pagina -->
        <footer>
            footer
        </footer>
    </div>
</body>
</html>
<?php else: ?>
    <script>
        alert('Acceso indebido');
        window.location = "<?php echo BASE_URL; ?>";
    </script>
<?php endif; ?>
