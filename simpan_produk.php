<?php
include "config/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_toko = $_POST['id_toko'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = $_POST['harga'];
    $gambarRaw = $_POST['gambar']; // Ini dalam format Base64 dari JS

    // Ambil nama toko untuk disimpan di kolom 'toko' katalog
    $q_toko = mysqli_query($conn, "SELECT nama_toko FROM toko WHERE id = '$id_toko'");
    $d_toko = mysqli_fetch_assoc($q_toko);
    $nama_toko = $d_toko['nama_toko'];

    // Proses Gambar Base64 menjadi file .png
    $img = str_replace('data:image/png;base64,', '', $gambarRaw);
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    
    $nama_file = "prod_" . time() . ".png";
    $path = "uploads/" . $nama_file;
    file_put_contents($path, $data);

    // INSERT ke tabel produk (sesuaikan nama kolom dengan katalogmu)
    $query = "INSERT INTO produk (id_toko, nama, harga, gambar, toko) 
              VALUES ('$id_toko', '$nama', '$harga', '$nama_file', '$nama_toko')";

    if (mysqli_query($conn, $query)) {
        echo "Berhasil menambah ke katalog!";
    } else {
        echo "Gagal: " . mysqli_error($conn);
    }
}
?>