<?php
session_start();
include "config/db.php";

// 1. Keamanan Level 1: Harus Login Akun
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user_login = $_SESSION['id_user'];

// 2. Ambil ID Toko dari URL atau Session
if (isset($_GET['id'])) {
    $id_toko = intval($_GET['id']);
} elseif (isset($_SESSION['id_toko'])) {
    $id_toko = $_SESSION['id_toko'];
} else {
    // Jika tidak ada ID toko sama sekali, lempar ke pilih toko
    header("Location: pilih_toko.php");
    exit();
}

// 3. Keamanan Level 2: Validasi Kepemilikan (Cegah orang intip toko lain)
$query = mysqli_query($conn, "SELECT * FROM toko WHERE id = '$id_toko' AND id_user = '$id_user_login'");
$data_toko = mysqli_fetch_assoc($query);

if (!$data_toko) {
    echo "<script>alert('Anda tidak memiliki akses ke toko ini!'); window.location.href='pilih_toko.php';</script>";
    exit();
}

// Set session toko aktif supaya sinkron
$_SESSION['id_toko'] = $id_toko;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo $data_toko['nama_toko']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root { --primary-blue: #007bff; --light-gray: #f4f7f6; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); margin: 0; }
        .header { background: var(--primary-blue); color: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .page-container { background: white; padding: 40px; border-radius: 20px; margin: 30px auto; width: 90%; max-width: 1000px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        /* Profile Section */
        .dash-content { display: flex; gap: 40px; border-bottom: 2px solid #f0f0f0; padding-bottom: 35px; align-items: center; }
        .dash-left { text-align: center; }
        .dash-avatar-wrapper { width: 140px; height: 140px; border-radius: 30px; overflow: hidden; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .dash-avatar-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        
        /* Info */
        .dash-info h2 { margin: 0 0 10px 0; font-size: 1.8rem; color: #333; }
        .dash-info p { color: #777; margin-bottom: 20px; line-height: 1.5; }
        
        .btn-edit-profil { background: var(--primary-blue); color: white; padding: 12px 25px; border-radius: 12px; text-decoration: none; display: inline-block; font-weight: 600; transition: 0.3s; }
        .btn-edit-profil:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,123,255,0.3); }
        
        /* Product Grid */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; margin-top: 30px; }
        .product-card { background: white; border: 1px solid #eee; border-radius: 15px; overflow: hidden; transition: 0.3s; }
        .product-card:hover { transform: scale(1.03); }
        .product-card img { width: 100%; height: 160px; object-fit: cover; }
        .add-product-card { border: 2px dashed #ccc; background: #fafafa; display: flex; flex-direction: column; justify-content: center; align-items: center; cursor: pointer; min-height: 230px; color: #888; }
        .add-product-card:hover { border-color: var(--primary-blue); color: var(--primary-blue); }
    </style>
</head>
<body>

    <div class="header">
        <div style="font-weight:700; font-size:1.3rem;">🛒 LokaMart</div>
        <div style="display:flex; gap:20px; align-items:center;">
            <a href="pilih_toko.php" style="color:white; text-decoration:none; font-size:0.9rem;"><i class="fas fa-exchange-alt"></i> Ganti Toko</a>
            <a href="logout.php" style="background:rgba(255,255,255,0.2); padding:5px 15px; border-radius:20px; color:white; text-decoration:none; font-size:0.9rem;">Keluar</a>
        </div>
    </div>

    <div class="page-container">
        <div class="dash-content">
            <div class="dash-left">
                <div class="dash-avatar-wrapper">
                    <img id="logo-preview" src="uploads/<?php echo !empty($data_toko['logo']) ? $data_toko['logo'] : 'default.png'; ?>">
                </div>
                <input type="file" id="logoInput" accept="image/*" style="display:none">
                <button onclick="document.getElementById('logoInput').click()" style="border:none; background:none; color:var(--primary-blue); font-weight:600; cursor:pointer; font-size:0.8rem;">
                    <i class="fas fa-camera"></i> Ganti Logo
                </button>
            </div>

            <div class="dash-info">
                <h2><?php echo $data_toko['nama_toko']; ?></h2>
                <p><?php echo $data_toko['deskripsi']; ?></p>
                <div style="font-size: 0.85rem; color: #555; background: #f8f9fa; padding: 15px; border-radius: 10px; display: inline-block;">
                    <i class="fab fa-whatsapp"></i> <b>WhatsApp:</b> <?php echo $data_toko['whatsapp']; ?><br>
                    <i class="fas fa-map-marker-alt"></i> <b>Alamat:</b> <?php echo $data_toko['alamat']; ?>
                </div>
                <div style="margin-top:20px;">
                    <a href="pengaturan_toko.php?id=<?php echo $id_toko; ?>" class="btn-edit-profil">
                        <i class="fas fa-cog"></i> Pengaturan Toko
                    </a>
                </div>
            </div>
        </div>

        <div class="products-section">
            <h3 style="margin-top:35px; color:#333;">Katalog Produk</h3>
            <div class="product-grid">
                
                <input type="file" id="prodInput" accept="image/*" style="display:none">
                <div class="product-card add-product-card" onclick="document.getElementById('prodInput').click()">
                    <i class="fas fa-plus-circle" style="font-size: 2.5rem; margin-bottom:10px;"></i>
                    <span style="font-weight:600;">Tambah Produk</span>
                </div>

                <?php
                // Ambil produk HANYA milik toko ini
                $q_prod = mysqli_query($conn, "SELECT * FROM produk WHERE id_toko = '$id_toko' ORDER BY id DESC");
                while($p = mysqli_fetch_assoc($q_prod)):
                ?>
                <div class="product-card">
                    <img src="uploads/<?php echo $p['gambar']; ?>" onerror="this.src='https://via.placeholder.com/180'">
                    <div style="padding:15px;">
                        <div style="font-weight:600; color:#333; margin-bottom:5px;"><?php echo $p['nama']; ?></div>
                        <div style="color:var(--primary-blue); font-weight:700;">Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?></div>
                    </div>
                </div>
                <?php endwhile; ?>

            </div>
        </div>
    </div>

    <script>
        // SCRIPT GANTI LOGO (AJAX)
        document.getElementById('logoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const formData = new FormData();
                    formData.append('id_toko', '<?php echo $id_toko; ?>');
                    formData.append('logo_base64', event.target.result);

                    fetch('update_logo_toko.php', { method: 'POST', body: formData })
                    .then(res => res.text())
                    .then(data => {
                        alert("Logo berhasil diperbarui!");
                        window.location.reload();
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        // SCRIPT TAMBAH PRODUK (AJAX)
        document.getElementById('prodInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const nama = prompt("Nama Produk:");
                    const harga = prompt("Harga (Angka saja):");
                    if (nama && harga) {
                        const formData = new FormData();
                        formData.append('id_toko', '<?php echo $id_toko; ?>');
                        formData.append('nama', nama);
                        formData.append('harga', harga);
                        formData.append('gambar', event.target.result);

                        fetch('simpan_produk.php', { method: 'POST', body: formData })
                        .then(res => res.text())
                        .then(data => {
                            alert("Produk Berhasil Ditambahkan!");
                            window.location.reload();
                        });
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>