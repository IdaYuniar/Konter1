<?php 
include 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja | LokaMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f8f9fa; }
        .navbar-belanja { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 15px 0; }
        .cart-card { background: white; border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .btn-checkout { background-color: #1C7ED6; color: white; font-weight: 600; width: 100%; padding: 12px; border-radius: 8px; border: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-belanja mb-4">
    <div class="container">
        <a class="navbar-brand text-primary fw-bold text-decoration-none" href="belanja.php">🛒SmartKonter</a>
        <a href="katalog produk.php" class="btn btn-outline-secondary btn-sm rounded-pill">Kembali Belanja</a>
    </div>
</nav>

<div class="container">
    <h3 class="fw-bold mb-4">Keranjang Saya</h3>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="cart-card p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_belanja = 0;
                            $query = mysqli_query($conn, "SELECT keranjang.*, produk.nama, produk.harga, produk.gambar FROM keranjang JOIN produk ON keranjang.id_produk = produk.id");
                            
                            if (mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                                    $subtotal = $row['harga'] * $row['jumlah'];
                                    $total_belanja += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="uploads/<?php echo $row['gambar']; ?>" class="product-img me-3" onerror="this.src='https://via.placeholder.com/80';">
                                        <span class="fw-600"><?php echo $row['nama']; ?></span>
                                    </div>
                                </td>
                                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $row['jumlah']; ?></td>
                                <td class="fw-bold text-primary">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                <td>
                                    <a href="hapus_keranjang.php?id=<?php echo $row['id_keranjang']; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-5'>Keranjang masih kosong</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="cart-card p-4">
                <h5 class="fw-bold mb-3">Ringkasan Belanja</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Harga</span>
                    <span class="fw-bold fs-5">Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                </div>
                <hr>
                <button class="btn-checkout" onclick="checkoutWA()">
    Checkout Sekarang
</button>

<script>
function checkoutWA() {
    if (confirm("Anda akan diarahkan ke WhatsApp untuk melakukan pembayaran. Lanjutkan?")) {
        window.location.href = "https://wa.me/082115329791?text=Halo,%20saya%20ingin%20checkout%20produk%20ini.";
    }
}
</script>
            </div>
        </div>
    </div>
</div>

</body>
</html>