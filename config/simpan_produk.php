<?php
include "config/db.php";

if (isset($_POST['nama'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar']; // Data Base64

    $query = "INSERT INTO produk (nama_produk, harga, gambar) VALUES ('$nama', '$harga', '$gambar')";
    mysqli_query($conn, $query);
    echo "Sukses";
}
?>