<?php
include 'koneksi.php';

// Tampilkan error PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ambil id produk dari URL
$id = 0;
$p = null;

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $qProduk = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id");
    $p = mysqli_fetch_assoc($qProduk);
} else {
    $qProduk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC LIMIT 1");
    $p = mysqli_fetch_assoc($qProduk);
    if ($p) {
        $id = $p['id'];
    }
}

if (!$p) {
    echo "<h3 style='text-align:center;margin-top:50px;'>❌ Tidak ada produk ditemukan!</h3>";
    exit;
}

// Proses kirim form transaksi
if (isset($_POST['buat_transaksi'])) {
    $nama_pembeli = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $jumlah = (int) $_POST['jumlah'];
    $total_harga = $p['harga'] * $jumlah;

    $insert = mysqli_query($conn, "INSERT INTO transaksi (produk_id, nama_pembeli, jumlah, total_harga)
                                   VALUES ('$id', '$nama_pembeli', '$jumlah', '$total_harga')");
    if ($insert) {
        echo "<script>alert('Transaksi berhasil dicatat!');window.location='transaksi.php?id=$id';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaksi Pembelian | SmartKonter</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    :root { --primary-blue: #0084ff; --text-dark: #333; }
    * { font-family: 'Poppins', sans-serif; }
    body { background-color: #f8f9fa; color: var(--text-dark); margin: 0; }

    /* Header (Sesuai index.html) */
    header {
      background-color: var(--primary-blue);
      padding: 12px 50px;
      position: sticky;
      top: 0;
      z-index: 1000;
      display: flex;
      align-items: center;
    }
    .brand-logo { color: white; font-weight: 700; font-size: 22px; text-decoration: none; }
    
    /* PENGGANTI SEARCH BOX */
    .page-title-header { 
        flex-grow: 1; 
        text-align: center; 
        color: white; 
        font-weight: 600; 
        font-size: 18px; 
        letter-spacing: 1px;
    }

    nav a { color: white; text-decoration: none; margin-left: 20px; font-size: 14px; opacity: 0.9; }

    /* Footer (Sesuai index.html) */
    footer { background-color: var(--primary-blue); color: white; padding: 50px 0 20px; margin-top: 50px;}
    .footer-link { color: #ffffff !important; text-decoration: none; display: block; margin-bottom: 10px; }
    .footer-link:hover { text-decoration: underline; }

    .card { border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .table-container { background: white; border-radius: 15px; padding: 20px; }
  </style>
</head>
<body>

<header class="d-flex justify-content-between align-items-center px-4">
    <a href="index.html" class="brand-logo"><i class="fa fa-shopping-cart"></i>  SmartKonter </a>
    
    <div class="page-title-header d-none d-md-block">
        FORM TRANSAKSI PEMBELIAN
    </div>

    <nav class="d-flex align-items-center">
        <a href="index.html">Beranda</a>
        <a href="katalog produk.php#Fitur">Produk</a>
        <a href="index.html" class="btn btn-sm btn-light text-primary fw-bold ms-4" style="border-radius: 15px; padding: 5px 20px;">Logout</a>
    </nav>
</header>

<div class="container my-5">
  <div class="row g-4">
      <div class="col-lg-4">
          <div class="card">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Detail Produk</h5>
            </div>
            <div class="card-body p-4">
              <h6 class="text-primary fw-bold"><?php echo $p['nama']; ?></h6>
              <p class="text-muted small">Toko: <?php echo $p['toko']; ?></p>
              <h5 class="fw-bold">Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?></h5>
              <hr>
              <form method="POST">
                <div class="mb-3">
                  <label class="form-label small fw-bold">Nama Pembeli</label>
                  <input type="text" name="nama_pembeli" class="form-control rounded-3" required>
                </div>
                <div class="mb-3">
                  <label class="form-label small fw-bold">Jumlah</label>
                  <input type="number" name="jumlah" class="form-control rounded-3" min="1" value="1" required>
                </div>
                <button type="submit" name="buat_transaksi" class="btn btn-primary w-100 fw-bold rounded-3 py-2">Simpan Transaksi</button>
              </form>
            </div>
          </div>
      </div>

      <div class="col-lg-8">
          <div class="table-container shadow-sm">
            <h5 class="fw-bold mb-4"><i class="fa fa-history text-primary"></i> Riwayat Transaksi</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>No</th>
                      <th>Pembeli</th>
                      <th>Qty</th>
                      <th>Total</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $qTrans = mysqli_query($conn, "SELECT * FROM transaksi WHERE produk_id=$id ORDER BY id DESC");
                    if (mysqli_num_rows($qTrans) > 0) {
                        $no = 1;
                        while ($t = mysqli_fetch_assoc($qTrans)) {
                            echo "<tr>
                                    <td>$no</td>
                                    <td class='fw-bold'>{$t['nama_pembeli']}</td>
                                    <td>{$t['jumlah']}</td>
                                    <td class='text-primary fw-bold'>Rp ".number_format($t['total_harga'],0,',','.')."</td>
                                    <td><span class='badge rounded-pill bg-success-subtle text-success border border-success'>{$t['status']}</span></td>
                                  </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4 opacity-50'>Belum ada transaksi</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
            </div>
          </div>
      </div>
  </div>
</div>

<footer>
    <div class="container">
      <div class="row">
        <div class="col-md-5 mb-4">
          <h4 class="fw-bold"><i class="fa fa-shopping-cart"></i> LokaMart</h4>
          <p class="small mt-3 mb-1"><i class="fa fa-map-marker-alt"></i> Ciamis, Jawa Barat</p>
          <p class="small mb-4"><i class="fa fa-phone"></i> +62 812 3456 7890</p>
          <div class="mt-3">
            <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
        <div class="col-md-3 mb-4">
          <h6 class="fw-bold mb-3">Sitemap</h6>
          <a href="index.html" class="footer-link">Beranda</a>
          <a href="katalog produk.php #Fitur" class="footer-link">Produk</a>
        </div>
        <div class="col-md-4">
          <h6 class="fw-bold mb-3">Informasi</h6>
          <p class="small opacity-75">LokaMart mendukung pertumbuhan ekonomi lokal melalui pemberdayaan UMKM digital.</p>
        </div>
      </div>
      <hr class="mt-5 mb-4 opacity-25">
      <p class="text-center small opacity-75">© 2025 LokaMart UMKM — Ciamis</p>
    </div>
</footer>

</body>
</html>