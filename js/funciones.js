function alertar(){
    var eliminar = confirm('¿Esta seguro de cancelar la compra?');
    if(eliminar){
        window.location="../carro_compras/deleteCompraUsuario.php";
    }else{
        window.location="../index.php";
    }
}

function eliminar(compra){
    var eliminar = confirm('¿Esta seguro de eliminar el producto?');

    if (eliminar) {
        window.location = "../carro_compras/delete.php?id=" + compra;
    }else{
        window.location="../index.php";
    }
}