<?php
session_start();
include 'koneksi.php';

$user_id_login = $_SESSION['id_user'] ?? 0;

// Ambil ID Toko
$q_toko = mysqli_query($conn, "SELECT id FROM toko WHERE id_user='$user_id_login'");
$toko = mysqli_fetch_assoc($q_toko);
$id_toko = $toko['id'] ?? 0;

// Ambil parameter kategori dari URL
$kategori = $_GET['kategori'] ?? '';

$data_produk = [];

if (!empty($kategori) && $id_toko != 0) {
    // Query menyaring kategori dan mengurutkannya secara alfabet (A-Z) agar rapi
    // Catatan: Pastikan di tabel `produk` kamu memiliki kolom penciri/nama yang mirip (Contoh: "Pulsa Telkomsel", "Pulsa Indosat")
    $q = mysqli_query($conn, "
        SELECT id, nama FROM produk 
        WHERE id_toko = '$id_toko' 
        AND (nama LIKE '$kategori%' OR '$kategori' = 'Perdana/Vocer' AND (nama LIKE 'Perdana%' OR nama LIKE 'Vocer%' OR nama LIKE 'Voucher%'))
        ORDER BY nama ASC
    ");

    // Jika query di atas kurang fleksibel dengan struktur tabel produkmu, gunakan query di bawah ini (hilangkan tanda ulasan komentar `//`):
    // $q = mysqli_query($conn, "SELECT id, nama FROM produk WHERE id_toko='$id_toko' ORDER BY nama ASC");

    while ($row = mysqli_fetch_assoc($q)) {
        $data_produk[] = $row;
    }
}

// Kembalikan data dalam bentuk JSON
header('Content-Type: application/json');
echo json_encode($data_produk);
?>