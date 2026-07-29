<?php
include 'koneksi.php';

if(isset($_POST['id_produk']) && isset($_POST['stok_baru'])) {
    $id = $_POST['id_produk'];
    $stok = $_POST['stok_baru'];

    $query = mysqli_query($conn, "UPDATE produk SET stok = '$stok' WHERE id = '$id'");
    
    if($query) {
        echo "success";
    } else {
        echo "error";
    }
}
?>