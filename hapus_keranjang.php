<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Seringkali di tabel keranjang nama kolomnya adalah 'id_keranjang'
    // Silakan ganti 'id_keranjang' di bawah ini sesuai nama kolom di database kamu
    $query = mysqli_query($conn, "DELETE FROM keranjang WHERE id_keranjang = '$id'");

    if ($query) {
        header("Location: keranjang.php?pesan=hapus_berhasil");
    } else {
        // Jika masih error, kita tampilkan pesan errornya
        echo "Gagal menghapus: " . mysqli_error($conn);
    }
} else {
    header("Location: keranjang.php");
}
?>