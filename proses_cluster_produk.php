<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id_login = $_SESSION['id_user'];
$q_toko = mysqli_query($conn, "SELECT id FROM toko WHERE id_user='$user_id_login'");
$toko = mysqli_fetch_assoc($q_toko);
$id_toko = $toko['id'] ?? 0;

// --- PROSES SIMPAN PENJUALAN ---
if (isset($_POST['simpan'])) {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $jumlah = (int)$_POST['jumlah'];
    
    $query = mysqli_query($conn, "INSERT INTO penjualan_offline (id_toko, tanggal, nama_produk, jumlah, kategori, modal, hasil) VALUES ('$id_toko', '$tanggal', '$nama_produk', '$jumlah', '$kategori', 0, '$jumlah')");
    
    if($query){ echo "<script>alert('Data berhasil disimpan'); location='proses_cluster_produk.php';</script>"; }
}
    
// --- PROSES SIMPAN PENGELUARAN ---
if (isset($_POST['simpan_pengeluaran'])) {
    $tgl_p = mysqli_real_escape_string($conn, $_POST['tgl']);
    $nom = (int)$_POST['nominal'];
    $sql_in = "INSERT INTO pengeluaran_pribadi(id_toko, tanggal, nominal) VALUES('$id_toko', '$tgl_p', '$nom')";
    if(mysqli_query($conn, $sql_in)){ echo "<script>alert('Pengeluaran berhasil disimpan'); location='proses_cluster_produk.php';</script>"; }
}

// UPDATE MODAL
if(isset($_POST['update_modal'])){

    $kategori = mysqli_real_escape_string($conn,$_POST['kategori']);
    $nama = mysqli_real_escape_string($conn,$_POST['nama_produk']);
    $modal = (int)$_POST['modal'];

    mysqli_query($conn,"
        UPDATE penjualan_offline
        SET
            modal='$modal',
            hasil=(jumlah-$modal)
        WHERE
            id_toko='$id_toko'
        AND kategori='$kategori'
        AND nama_produk='$nama'
    ");

    echo json_encode([
        "status"=>"success"
    ]);

    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penjualan & Laporan Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">📒 Catatan Penjualan & Keuangan</h2>

    <table class="table table-bordered">
        <thead><tr><th>Tanggal</th><th>Kategori</th><th>Nama Produk</th><th>Omset</th><th>Modal</th><th>Hasil</th></tr></thead>
        <tbody>
            <?php 
            $q = mysqli_query($conn, "SELECT MIN(tanggal) as tanggal, kategori, nama_produk, SUM(jumlah) as omset, SUM(modal) as modal FROM penjualan_offline WHERE id_toko='$id_toko' GROUP BY kategori, nama_produk");
            while($d = mysqli_fetch_assoc($q)){
                $hasil = $d['omset'] - $d['modal'];
                echo "<tr data-omset='".$d['omset']."'>
                    <td>".$d['tanggal']."</td><td>".$d['kategori']."</td><td>".$d['nama_produk']."</td>
                    <td>Rp ".number_format($d['omset'],0,',','.')."</td>
                    <td>
                        <form class='modalForm'>
                            <input type='hidden' name='kategori' value='".$d['kategori']."'>
                            <input type='hidden' name='nama_produk' value='".$d['nama_produk']."'>
                            <input type='number' name='modal' value='".$d['modal']."' class='form-control form-control-sm'>
                            <button name='update_modal' class='btn btn-sm btn-primary mt-1'>Simpan</button>
                        </form>
                    </td>
                    <td class='hasilCell' data-hasil='$hasil'>Rp ".number_format($hasil,0,',','.')."</td>
                </tr>";
            } ?>
        </tbody>
    </table>

    <div class="card"><div class="card-body">
        <h5>💰 Input Pengeluaran</h5>
        <form method="POST" class="row g-3 mb-3">
            <div class="col-md-3"><input type="date" name="tgl" class="form-control" required></div>
            <div class="col-md-3"><input type="number" name="nominal" class="form-control" placeholder="Nominal" required></div>
            <div class="col-md-3"><button name="simpan_pengeluaran" class="btn btn-danger">Simpan Pengeluaran</button></div>
        </form>
        
        <table class="table table-hover">
            <thead><tr><th>Tanggal</th><th>Hasil Keseluruhan</th><th>Pengeluaran Peribadi</th><th>Hasil Bersih</th></tr></thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT tanggal, SUM(jumlah - modal) AS hasil_keseluruhan FROM penjualan_offline WHERE id_toko='$id_toko' GROUP BY tanggal ORDER BY tanggal DESC");
                while($r = mysqli_fetch_assoc($res)){
                    $tgl = $r['tanggal'];
                    $q_ex = mysqli_query($conn, "SELECT SUM(nominal) as total FROM pengeluaran_pribadi WHERE id_toko='$id_toko' AND tanggal='$tgl'");
                    $pengeluaran = mysqli_fetch_assoc($q_ex)['total'] ?? 0;
                    $bersih = $r['hasil_keseluruhan'] - $pengeluaran;
                    echo "<tr><td>$tgl</td><td>Rp ".number_format($r['hasil_keseluruhan'],0,',','.')."</td><td>Rp ".number_format($pengeluaran,0,',','.')."</td><td><strong>Rp ".number_format($bersih,0,',','.')."</strong></td></tr>";
                } ?>
            </tbody>
        </table>
    </div></div>
</div>

<script>
document.addEventListener('submit', function(e){

    if(e.target.classList.contains('modalForm')){

        e.preventDefault();

        const form = e.target;

        const data = new FormData(form);

        data.append('update_modal', '1');

        fetch(window.location.href,{
            method:'POST',
            body:data
        })
        .then(res=>res.json())
        .then(res=>{

            if(res.status=="success"){

                alert("Modal berhasil diupdate");

                location.reload();

            }else{

                alert("Gagal update");

            }

        });
    }
});
</script>
</body>
</html>