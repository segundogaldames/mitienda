<?php
session_start();

require('../class/conexion.php');
require('../class/rutas.php');

if(isset($_SESSION['autenticado'])){
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];

        $res = $mbd->prepare("SELECT id, usuario_id FROM carro_compras WHERE id = ?");
        $res->bindParam(1, $id);
        $res->execute();

        $compra = $res->fetch();

        if ($compra) {
            $res = $mbd->prepare("DELETE FROM carro_compras WHERE id = ?");
            $res->bindParam(1, $id);
            $res->execute();

            $row = $res->rowCount();

            if ($row) {
                $_SESSION['success'] = 'El producto se ha eliminado correctamente';
                header('Location: ' . CARRO_COMPRAS . 'show.php');
            }
        }
    }

}else{
    header('Location: ../');
}