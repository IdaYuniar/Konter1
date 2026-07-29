<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id_login = $_SESSION['id_user'];

// ambil toko
$q_toko = mysqli_query($conn, "
SELECT id FROM toko WHERE id_user='$user_id_login'
");
$toko = mysqli_fetch_assoc($q_toko);
$id_toko = $toko['id'] ?? 0;

// =======================
// PROSES SIMPAN (FIX 1X)
// =======================
if (isset($_POST['simpan'])) {

    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);

    $produk_id = $_POST['produk_id'] ?? '';
    $produk_manual = trim($_POST['produk_manual'] ?? '');

    $nama_produk = '';

    // kalau pilih produk dari dropdown
    if (!empty($produk_id)) {

        $q = mysqli_query($conn, "
        SELECT nama FROM produk WHERE id='$produk_id' LIMIT 1
        ");

        if ($row = mysqli_fetch_assoc($q)) {
            $nama_produk = $row['nama'];
        }

    } else {
        // kalau manual
        $nama_produk = mysqli_real_escape_string($conn, $produk_manual);
        $produk_id = NULL;
    }

    // validasi biar tidak kosong
    if ($nama_produk != '') {

        $produk_id_sql = ($produk_id == NULL) ? "NULL" : "'$produk_id'";

        mysqli_query($conn, "
INSERT INTO penjualan_offline
(id_toko, tanggal, kategori, produk_id, nama_produk, jumlah)
VALUES
('$id_toko', '$tanggal', '$kategori', $produk_id_sql, '$nama_produk', '$jumlah')
");

        echo "<script>
        alert('Data berhasil disimpan');
        location='penjualan_offline.php';
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Penjualan Offline</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f5f7fa;
    font-family:Poppins,sans-serif;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.table th{
    background:#f8f9fa;
}

.form-control{
    border-radius:10px;
}

.btn{
    border-radius:10px;
}
</style>

</head>
<body>

<div class="container py-4">

<h2 class="mb-4">📒 Catatan Penjualan Offline</h2>

<!-- FORM -->
 <div class="card mb-4">
<div class="card-body">

<form method="POST">

<div class="row g-3 align-items-end">

    <!-- Tanggal -->
    <div class="col-md-2">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control" required>
    </div>

    <!-- Kategori -->
    <div class="col-md-2">
        <label>Kategori</label>
        <select name="kategori" id="kategori" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="Pulsa">Pulsa</option>
            <option value="Elektronik">Elektronik</option>
            <option value="Perdana/Vocer">Perdana/Vocer</option>
            <option value="Acecoris/Service">Acecoris/Service</option>
            <option value="Transfer">Transfer</option>
        </select>
    </div>

    <!-- Produk -->
    <div class="col-md-4">
        <label>Produk</label>

        <select name="produk_id" class="form-control">
            <option value="">-- Pilih Produk --</option>

            <?php
            $qProduk = mysqli_query($conn,"
            SELECT * FROM produk
            WHERE id_toko='$id_toko'
            ORDER BY nama
            ");

            while($p=mysqli_fetch_assoc($qProduk)){
            ?>
                <option value="<?= $p['id']; ?>">
                    <?= $p['nama']; ?>
                </option>
            <?php } ?>
        </select>

        <input type="text"
               name="produk_manual"
               class="form-control mt-2"
               placeholder="atau ketik manual">
    </div>

    <!-- Jumlah -->
    <div class="col-md-2">
        <label>Jumlah</label>
        <input type="number" name="jumlah" class="form-control" min="1" required>
    </div>

    <!-- Tombol -->
    <div class="col-md-2">
        <button type="submit" name="simpan" class="btn btn-success w-100">
            Simpan
        </button>
    </div>

</div>

</div>

</form>

</div>
</div>

<!-- TABLE -->
<div class="card">
<div class="card-body">

<div class="text-end mb-3">
<a href="proses_cluster_produk.php" class="btn btn-primary">
Keuangan
</a>
<a href="K-Mans.php" class="btn btn-primary">
K-Mans
</a>
</div>

<h5>Riwayat Penjualan Offline</h5>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>
<tr>
<th>No</th>
<th>Tanggal</th>
<th>Kategori</th>
<th>Produk</th>
<th>Jumlah</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php
$no = 1;

$q = mysqli_query($conn, "
SELECT * FROM penjualan_offline
WHERE id_toko='$id_toko'
ORDER BY id DESC
");

while($d = mysqli_fetch_assoc($q)){
?>

<tr>
<td><?= $no++; ?></td>
<td><?= $d['tanggal']; ?></td>
<td><?= $d['kategori']; ?></td>
<td><?= htmlspecialchars($d['nama_produk']); ?></td>
<td><?= $d['jumlah']; ?></td>
<td>
<a href="penjualan_offline_edit.php?id=<?= $d['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
<a href="penjualan_offline_hapus.php?id=<?= $d['id']; ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Yakin?')">Hapus</a>
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>
</div>

</div>

</body>
</html>