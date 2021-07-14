<?php

session_start();

require('../class/conexion.php');
require('../class/rutas.php');

if(isset($_SESSION['autenticado'])){
    //consultar si el usuario autenticado tiene compras en el carro
    $res = $mbd->prepare("SELECT id FROM carro_compras WHERE usuario_id = ?");
    $res->bindParam(1, $_SESSION['usuario_id']);
    $res->execute();
    
    $row = $res->fetchall();

    if ($row) {
        //eliminamos la compra del carro
        $res = $mbd->prepare("DELETE FROM carro_compras WHERE usuario_id = ?");
        $res->bindParam(1, $_SESSION['usuario_id']);
        $res->execute();

        $row = $res->rowCount();

        if($row){
            unset($_SESSION['compra']);
            $_SESSION['success'] = 'Su carro de compra se ha vaciado satisfactoriamente';
            header('Location: ' . BASE_URL);
        }
    }else{
        $_SESSION['danger'] = 'Su carro de compras no ha podido ser vaciado... Intente nuevamente';
    }
}else{
    header('Location: ' . BASE_URL);
}