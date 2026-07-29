<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_produk = (int)$_GET['id'];

    // Cek apakah barang sudah ada di keranjang
    $cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_produk = '$id_produk'");
    
    if (mysqli_num_rows($cek) > 0) {
        // Jika sudah ada, tambah jumlahnya
        mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id_produk = '$id_produk'");
    } else {
        // Jika belum ada, masukkan data baru
        mysqli_query($conn, "INSERT INTO keranjang (id_produk, jumlah) VALUES ('$id_produk', 1)");
    }

    header("Location: keranjang.php");
}
?>