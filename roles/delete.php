<?php
require('../class/conexion.php');
require('../class/rutas.php');

if (isset($_GET['id'])) {
   
    $id = (int) $_GET['id']; //guardamos el id que viene por GET en la variable id, obligandola a que sea numero entero

    //consultar a la tabla roles si existe un registro (fila) asociado al id recibido
    $res = $mbd->prepare("SELECT id FROM roles WHERE id = ?");
    $res->bindParam(1, $id); //sanitizamos la variable id antes de ejecutarse la consulta
    $res->execute(); //ejecutamos la consulta
    $rol = $res->fetch();//recuperamos la fila si es que existe

    //validamos la existencia del rol que se desea eliminar
    if($rol){
        //procedemos a eliminar el rol solicitado
        $res = $mbd->prepare("DELETE FROM roles WHERE id = ?");
        $res->bindParam(1, $id);//sanitizamos la variable id
        $res->execute(); //ejecutamos la consulta

        $row = $res->rowCount(); //recuperamos el numero de filas afectados (=1)

        if($row){
            $msg = 'ok';
            header('Location: index.php?e=' . $msg);
        }else{
            $error = 'error';
            header('Location: index.php?error=' . $error);
        }
    }
}