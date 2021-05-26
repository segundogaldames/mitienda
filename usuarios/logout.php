<?php
    session_start();

    if (isset($_SESSION['autenticado'])) {
        session_destroy();
        header('Location: ../index.php');
    }else{
        echo "<script>
            alert('Debe iniciar sesión para continuar');
            window.location = 'http://localhost:8888/miTienda/';
        </script>";
    }

    

    