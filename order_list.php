<?php
session_start();
include 'koneksi.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id_login = $_SESSION['id_user'];

// Ambil ID Toko berdasarkan User yang login
$q_toko = mysqli_query($conn, "SELECT id FROM toko WHERE id_user = '$user_id_login'");
$dt_toko = mysqli_fetch_assoc($q_toko);
$id_toko_login = $dt_toko['id'];

// ===== PROSES AKSI =====
if (isset($_POST['aksi'])) {
    $transaksi_id = (int) $_POST['transaksi_id'];
    $aksi = $_POST['aksi'];

    if ($aksi === 'setujui') {
        // 1. Ambil data produk_id dan jumlah dari transaksi ini
        $q_cek = mysqli_query($conn, "SELECT produk_id, jumlah FROM transaksi WHERE id = $transaksi_id");
        $data_t = mysqli_fetch_assoc($q_cek);
        $p_id = $data_t['produk_id'];
        $qty  = $data_t['jumlah'];

        // 2. Update Status Transaksi & Kurangi Stok Produk Secara Otomatis
        // Menggunakan transaksi SQL agar aman
        mysqli_begin_transaction($conn);
        try {
            mysqli_query($conn, "UPDATE transaksi SET status='Disetujui' WHERE id=$transaksi_id");
            mysqli_query($conn, "UPDATE produk SET stok = stok - $qty WHERE id = $p_id");
            mysqli_commit($conn);
            echo "<script>alert('Pesanan Disetujui! Stok otomatis berkurang.'); window.location='order_list.php';</script>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Gagal memproses transaksi.');</script>";
        }
    } elseif ($aksi === 'batal') {
        mysqli_query($conn, "UPDATE transaksi SET status='Dibatalkan' WHERE id=$transaksi_id");
        echo "<script>alert('Pesanan Dibatalkan!'); window.location='order_list.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan | LokaMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .navbar-custom { background-color: #007bff; padding: 15px 5%; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: #f8f9fa; color: #555; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; }
        .badge-status { padding: 8px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; }
        .status-menunggu { background-color: #fff4e5; color: #ff9800; }
        .status-disetujui { background-color: #e6fffa; color: #38b2ac; }
        .status-dibatalkan { background-color: #fff5f5; color: #e53e3e; }
        .btn-action { border-radius: 8px; font-weight: 600; transition: 0.3s; }
        .nota-box { padding: 20px; border: 2px dashed #ddd; width: 300px; font-family: Courier, monospace; }
   .nav-btn{
    transition: all 0.3s ease;
}

.nav-btn:hover{
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}
   </style>
    
</head>
<body>

<nav class="navbar navbar-custom mb-5">
    <a href="detail_toko.php" class="text-white text-decoration-none fw-bold fs-4">
        <i class="fa fa-shopping-cart"></i> SmartKonter <span class="fw-normal fs-6">| Seller Center</span>
    <a href="detail_toko.php"
   class="btn btn-light rounded-pill px-4 fw-bold nav-btn me-2">
    <i class="fas fa-user-circle"></i> Profil Toko
</a>

<a href="penjualan_offline.php"
   class="btn btn-warning rounded-pill px-4 fw-bold nav-btn">
    <i class="fas fa-store"></i> Penjualan Offline
</a>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold"><i class="fas fa-clipboard-list text-primary"></i> Daftar Pesanan Masuk</h3>
            <p class="text-muted">Kelola permintaan pelanggan dan pantau pengeluaran stok Anda.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-center">
                            <th class="py-3">No</th>
                            <th>Produk</th>
                            <th>Pembeli</th>
                            <th>Jumlah</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT transaksi.*, produk.nama AS nama_produk, produk.stok 
                                FROM transaksi 
                                JOIN produk ON transaksi.produk_id = produk.id 
                                WHERE produk.id_toko = '$id_toko_login' 
                                ORDER BY transaksi.id DESC";
                        
                        $qTrans = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($qTrans) > 0) {
                            $no = 1;
                            while ($t = mysqli_fetch_assoc($qTrans)) {
                                $st_class = "status-menunggu";
                                if($t['status'] == 'Disetujui') $st_class = "status-disetujui";
                                if($t['status'] == 'Dibatalkan') $st_class = "status-dibatalkan";
                        ?>
                        <tr class="text-center">
                            <td><?php echo $no++; ?></td>
                            <td class="text-start">
                                <span class="fw-bold d-block"><?php echo $t['nama_produk']; ?></span>
                                <small class="text-muted">Sisa Stok: <?php echo $t['stok']; ?></small>
                            </td>
                            <td><?php echo $t['nama_pembeli']; ?></td>
                            <td><span class="badge bg-light text-dark px-3"><?php echo $t['jumlah']; ?> Item</span></td>
                            <td class="fw-bold text-primary">Rp <?php echo number_format($t['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge-status <?php echo $st_class; ?>">
                                    <?php echo $t['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($t['status'] == 'Menunggu Konfirmasi') { ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="transaksi_id" value="<?php echo $t['id']; ?>">
                                        <button name="aksi" value="setujui" class="btn btn-sm btn-success btn-action me-1">Setujui</button>
                                        <button name="aksi" value="batal" class="btn btn-sm btn-outline-danger btn-action">Tolak</button>
                                    </form>
                                <?php } elseif ($t['status'] == 'Disetujui') { ?>
                                    <button class="btn btn-primary btn-sm btn-action" onclick='cetakNota(<?php echo json_encode($t); ?>)'>
                                        <i class="fas fa-print"></i> Nota
                                    </button>
                                <?php } else { ?>
                                    <span class="text-muted small">-</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } } else { ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada pesanan masuk.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="nota" style="display:none;">
    <div class="nota-box">
        <h2 style="text-align:center;">LOKAMART</h2>
        <p style="text-align:center; font-size:10px;">Struk Pembelian Produk UMKM</p>
        <hr>
        <p>No Transaksi: #<span id="n_id"></span></p>
        <p>Pelanggan: <span id="n_nama"></span></p>
        <hr>
        <table style="width:100%">
            <tr>
                <td><span id="n_produk"></span></td>
                <td style="text-align:right">x<span id="n_jumlah"></span></td>
            </tr>
        </table>
        <hr>
        <h4 style="display:flex; justify-content:space-between;">
            TOTAL: <span>Rp <span id="n_total"></span></span>
        </h4>
        <hr>
        <p style="text-align:center; font-size:10px;">Terima kasih telah berbelanja!</p>
    </div>
</div>

<script>
function cetakNota(data) {
    document.getElementById('n_id').innerText = data.id;
    document.getElementById('n_nama').innerText = data.nama_pembeli;
    document.getElementById('n_produk').innerText = data.nama_produk;
    document.getElementById('n_jumlah').innerText = data.jumlah;
    document.getElementById('n_total').innerText = new Intl.NumberFormat('id-ID').format(data.total_harga);

    const content = document.getElementById('nota').innerHTML;
    const win = window.open('', '', 'height=500,width=400');
    win.document.write('<html><head><title>Cetak Nota</title></head><body style="display:flex; justify-content:center; padding-top:20px;">');
    win.document.write(content);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
}
</script>

</body>
</html>