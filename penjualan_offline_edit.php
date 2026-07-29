<?php
session_start();
include 'koneksi.php';

$id = $_GET['id'];

$q = mysqli_query($conn,"
SELECT *
FROM penjualan_offline
WHERE id='$id'
");

$data = mysqli_fetch_assoc($q);

if(isset($_POST['update'])){

    $tanggal = $_POST['tanggal'];
    $nama_produk = $_POST['nama_produk'];
    $jumlah = $_POST['jumlah'];

    mysqli_query($conn,"
    UPDATE penjualan_offline
    SET
        tanggal='$tanggal',
        nama_produk='$nama_produk',
        jumlah='$jumlah'
    WHERE id='$id'
    ");

    echo "
    <script>
    alert('Data berhasil diupdate');
    location='penjualan_offline.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Penjualan Offline</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h3>Edit Penjualan Offline</h3>

<form method="POST">

<div class="mb-3">
<label>Tanggal</label>
<input
type="date"
name="tanggal"
class="form-control"
value="<?= $data['tanggal']; ?>"
required>
</div>

<div class="mb-3">
<label>Nama Produk</label>
<input
type="text"
name="nama_produk"
class="form-control"
value="<?= htmlspecialchars($data['nama_produk']); ?>"
required>
</div>

<div class="mb-3">
<label>Jumlah</label>
<input
type="number"
name="jumlah"
class="form-control"
value="<?= $data['jumlah']; ?>"
required>
</div>

<button
type="submit"
name="update"
class="btn btn-success">
Update
</button>

<a
href="penjualan_offline.php"
class="btn btn-secondary">
Kembali
</a>

</form>

</div>

</body>
</html>