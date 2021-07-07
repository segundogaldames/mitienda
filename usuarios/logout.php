<?php
    session_start();

    require('../class/conexion.php');

    if (isset($_SESSION['autenticado'])) {

        //consultamos por productos en el carro de compras
        $res = $mbd->prepare("SELECT id FROM carro_compras WHERE usuario_id = ?");
        $res->bindParam(1, $_SESSION['usuario_id']);
        $res->execute();

        $compras = $res->fetchall();

        if(count($compras)){
            //eliminar las compras registradas del usuario
            $res = $mbd->prepare("DELETE FROM carro_compras WHERE usuario_id = ?");
            $res->bindParam(1, $_SESSION['usuario_id']);
            $res->execute();
        }

        session_destroy();
        header('Location: ../index.php');
    }else{
        echo "<script>
            alert('Debe iniciar sesión para continuar');
            window.location = 'http://localhost:8888/miTienda/';
        </script>";
    }

    

    