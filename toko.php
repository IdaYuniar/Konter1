<?php
include 'koneksi.php';

// 1. Logika Filter Kategori
$filter_kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';

$sql = "SELECT * FROM toko";
if ($filter_kategori != '') {
    $sql .= " WHERE kategori = '$filter_kategori'";
}
$sql .= " ORDER BY id DESC";
$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Toko | LokaMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root { --primary-blue: #007bff; --bg-gray: #f8f9fa; --text-dark: #333; --whatsapp-green: #25d366; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-gray); margin: 0; }
        
        /* Header */
        .header { background: var(--primary-blue); color: white; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h2 { margin: 0; font-size: 1.5em; }
        
        /* Filter Bar */
        .filter-container { padding: 20px 50px; display: flex; gap: 10px; overflow-x: auto; background: white; sticky; top: 0; z-index: 100; border-bottom: 1px solid #eee; }
        .filter-btn { padding: 8px 22px; border-radius: 25px; border: 1px solid #ddd; background: white; text-decoration: none; color: #666; font-size: 14px; transition: 0.3s; }
        .filter-btn.active { background: var(--primary-blue); color: white; border-color: var(--primary-blue); font-weight: 600; }
        .filter-btn:hover:not(.active) { background: #f0f0f0; }

        /* Grid Toko */
        .shop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; padding: 40px 50px; }
        
        /* Card Toko */
        .shop-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; display: flex; flex-direction: column; }
        .shop-card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        
        .shop-banner { height: 90px; background: linear-gradient(135deg, #007bff, #00d4ff); } 
        .shop-logo-container { width: 80px; height: 80px; margin: -40px auto 0; border: 5px solid white; border-radius: 50%; overflow: hidden; background: white; }
        .shop-logo-container img { width: 100%; height: 100%; object-fit: cover; }
        
        .shop-content { padding: 20px; text-align: center; flex-grow: 1; }
        .shop-cat { font-size: 10px; color: var(--primary-blue); font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 5px; display: block; }
        .shop-name { font-weight: 700; font-size: 1.2em; margin: 5px 0; color: var(--text-dark); }
        .shop-loc { font-size: 13px; color: #888; margin-bottom: 15px; }
        
        /* Footer Card */
        .shop-footer { display: flex; border-top: 1px solid #f0f0f0; }
        .btn-view { flex: 1; padding: 15px; text-decoration: none; color: var(--text-dark); font-weight: 600; font-size: 14px; text-align: center; transition: 0.2s; }
        .btn-view:hover { background: #f8f9fa; color: var(--primary-blue); }
        .btn-wa { background: var(--whatsapp-green); color: white; padding: 15px 20px; text-decoration: none; font-size: 1.2em; display: flex; align-items: center; justify-content: center; }
        .btn-wa:hover { opacity: 0.9; }
    </style>
</head>
<body>

<div class="header">
    <h2 onclick="location.href='kumpulan-toko.php'" style="cursor:pointer;"><i class="fas fa-store"></i> LokaMart</h2>
    <a href="index.html" style="color:white; text-decoration:none; font-weight:600; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 8px;">Kembali Ke Beranda</a>
</div>

<div class="filter-container">
    <a href="toko.php" class="filter-btn <?php echo $filter_kategori == '' ? 'active' : ''; ?>">Semua</a>
    <a href="?kategori=Kuliner" class="filter-btn <?php echo $filter_kategori == 'Kuliner' ? 'active' : ''; ?>">Kuliner</a>
    <a href="?kategori=Fashion" class="filter-btn <?php echo $filter_kategori == 'Fashion' ? 'active' : ''; ?>">Fashion</a>
    <a href="?kategori=Kerajinan" class="filter-btn <?php echo $filter_kategori == 'Kerajinan' ? 'active' : ''; ?>">Kerajinan</a>
    <a href="?kategori=Elektronik" class="filter-btn <?php echo $filter_kategori == 'Elektronik' ? 'active' : ''; ?>">Elektronik</a>
</div>

<div class="shop-grid">
    <?php if(mysqli_num_rows($query) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($query)): ?>
        <div class="shop-card">
            <div class="shop-banner"></div>
            <div class="shop-logo-container">
                <img src="<?php echo !empty($row['logo']) ? 'uploads/'.$row['logo'] : 'https://via.placeholder.com/80?text=Logo'; ?>" onerror="this.src='https://via.placeholder.com/80'">
            </div>
            <div class="shop-content">
                <span class="shop-cat"><?php echo !empty($row['kategori']) ? $row['kategori'] : 'UMKM'; ?></span>
                <div class="shop-name"><?php echo $row['nama_toko']; ?></div>
                <div class="shop-loc"><i class="fas fa-map-marker-alt"></i> <?php echo $row['alamat']; ?></div>
            </div>
            <div class="shop-footer">
                <a href="katalog produk.php?id_toko=<?php echo $row['id']; ?>" class="btn-view">
                    <i class="fas fa-shopping-bag"></i> Kunjungi Toko
                </a>
                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $row['whatsapp']); ?>" target="_blank" class="btn-wa">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 100px 20px; color: #bbb;">
            <i class="fas fa-store-slash" style="font-size: 4em; margin-bottom: 15px;"></i>
            <h3>Belum ada toko ditemukan</h3>
            <p>Silakan pilih kategori lain atau daftarkan toko Anda sekarang!</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>