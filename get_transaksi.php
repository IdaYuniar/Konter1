<?php
include 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "ID tidak ditemukan"]);
    exit;
}

$id = (int)$_GET['id'];

$q = mysqli_query($conn, "
    SELECT t.*, p.nama AS produk, p.toko, p.harga 
    FROM transaksi t 
    JOIN produk p ON t.produk_id = p.id
    WHERE t.id = $id
");

if (mysqli_num_rows($q) > 0) {
    echo json_encode(mysqli_fetch_assoc($q));
} else {
    echo json_encode(["error" => "Data tidak ditemukan"]);
}
?>
