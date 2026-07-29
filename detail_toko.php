<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id_login = $_SESSION['id_user']; 

// Ambil data toko sesuai database
$query = mysqli_query($conn, "SELECT *, DATE_FORMAT(tanggal_daftar, '%d %M %Y') as tgl_gabung FROM toko WHERE id_user = '$user_id_login'");
$data_toko = mysqli_fetch_assoc($query);

if (!$data_toko) {
    echo "<script>alert('Anda belum memiliki toko!'); window.location.href='registrasi-toko.php';</script>";
    exit();
}

$id_toko      = $data_toko['id']; 
$nama_toko    = $data_toko['nama_toko'];
$tanggal_buat = $data_toko['tgl_gabung'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo $nama_toko; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root { --primary-blue: #007bff; --bg-gray: #f4f7f6; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-gray); margin: 0; color: #333; }

        header {
            background-color: var(--primary-blue);
            height: 70px; display: flex; align-items: center; padding: 0 5%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;
        }
        .header-left, .header-center, .header-right { flex: 1; display: flex; align-items: center; }
        .header-center { justify-content: center; }
        .header-right { justify-content: flex-end; gap: 15px; }
        .brand-logo { color: white !important; text-decoration: none; font-weight: 700; font-size: 1.4rem; display: flex; align-items: center; gap: 8px; }
        .neon-text { color: #fff; font-weight: 700; text-transform: uppercase; text-shadow: 0 0 8px rgba(255,255,255,0.5); }
        .btn-logout { background: white; color: var(--primary-blue) !important; padding: 6px 18px; border-radius: 20px; font-weight: 600; text-decoration: none; font-size: 0.85rem; }

        .page-container { max-width: 1000px; margin: 30px auto; padding-bottom: 50px; }
        .main-white-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

        .profile-section { display: flex; gap: 40px; padding-bottom: 40px; border-bottom: 2px solid #f1f1f1; align-items: flex-start; }
        .avatar-wrapper { width: 150px; height: 150px; border-radius: 50%; overflow: hidden; border: 4px solid #f8f9fa; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 12px; }
        .avatar-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .btn-change-logo { background: #eee; border: none; padding: 7px 15px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; width: 150px; }

        .shop-name { font-size: 2.2rem; font-weight: 700; margin: 0; color: #222; }
        .shop-date { font-size: 0.85rem; color: #888; margin-bottom: 15px; }
        
        .contact-box { background: #f8f9fa; padding: 18px; border-radius: 12px; border-left: 5px solid var(--primary-blue); margin: 20px 0; display: flex; flex-direction: column; gap: 10px; }
        .contact-item { display: flex; align-items: flex-start; gap: 10px; font-size: 0.95rem; color: #444; }

        .products-title { margin: 40px 0 20px 0; font-weight: 700; font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; }
        .product-card { background: white; border: 1px solid #eee; border-radius: 12px; overflow: hidden; transition: 0.3s; text-align: center; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); border-color: var(--primary-blue); }
        .product-card img { width: 100%; height: 150px; object-fit: cover; }
        
        .add-btn-card { border: 2px dashed #ccc; background: #fafafa; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 250px; cursor: pointer; color: #aaa; }
        
        .stok-info { margin-top: 10px; padding-top: 10px; border-top: 1px dashed #eee; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; }
        .btn-plus-stok { background: #e7f3ff; border: none; color: #007bff; border-radius: 4px; padding: 2px 8px; cursor: pointer; font-weight: 600; }
        .btn-plus-stok:hover { background: #007bff; color: white; }

        .btn-edit-main { background: var(--primary-blue); color: white; padding: 10px 25px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
    </style>
</head>
<body>

<header>
    <div class="header-left">
        <a href="index.html" class="brand-logo"><i class="fa fa-shopping-cart"></i>  SmartKonter </a>
    </div>
    <div class="header-center">
        <div class="neon-text">HALO, <?php echo $_SESSION['username']; ?></div>
    </div>
    <div class="header-right">
        <a href="katalog produk.php" style="color:white; text-decoration:none; margin-right:20px; font-size:0.9rem;">Produk</a>
        <a href="order_list.php" style="color:white; text-decoration:none; margin-right:20px; font-size:0.9rem;">Transaksi</a>
         <a href="K-Mans.php" style="color:white; text-decoration:none; margin-right:20px; font-size:0.9rem;">K-Mans</a>
               <a href="penjualan_offline.php" style="color:white; text-decoration:none; margin-right:20px; font-size:0.9rem;">Produk Offline</a>
<a href="rencana_belanja.php" title="Rencana Belanja" style="color:white; text-decoration:none; margin-right:20px; font-size:1.2rem;">
    <i class="fas fa-clipboard-list"></i>
</a>
        <a href="index.html" class="btn-logout">Logout</a>
    </div>
</header>

<div class="page-container">
    <div class="main-white-card">
        
        <div class="profile-section">
            <div style="text-align: center;">
                <div class="avatar-wrapper">
                    <img id="logo-preview" src="uploads/<?php echo !empty($data_toko['logo']) ? $data_toko['logo'] : 'default.png'; ?>" onerror="this.src='https://via.placeholder.com/150'">
                </div>
                <input type="file" id="logoInput" accept="image/*" style="display:none">
                <button class="btn-change-logo" onclick="document.getElementById('logoInput').click()"><i class="fas fa-camera"></i> Ganti Logo</button>
            </div>

            <div style="flex: 1;">
                <h1 class="shop-name"><?php echo $nama_toko; ?></h1>
                <div class="shop-date"><i class="far fa-calendar-alt"></i> Terdaftar: <?php echo $tanggal_buat; ?></div>
                <p style="color: #666; font-size: 0.95rem; line-height: 1.6; margin: 0;"><?php echo $data_toko['deskripsi']; ?></p>
                
                <div class="contact-box">
                    <div class="contact-item">
                        <i class="fab fa-whatsapp" style="color:#25d366; margin-top:3px; width:20px;"></i> 
                        <span><strong>WhatsApp:</strong> <?php echo $data_toko['whatsapp']; ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt" style="color:#ff4d4d; margin-top:3px; width:20px;"></i> 
                        <span><strong>Alamat:</strong> <?php echo $data_toko['alamat']; ?></span>
                    </div>
                </div>

                <a href="registrasi-toko.php?id=<?php echo $id_toko; ?>" class="btn-edit-main"><i class="fas fa-edit"></i> Edit Profil</a>
            </div>
        </div>

        <div class="products-title">
            <i class="fas fa-box-open" style="color: var(--primary-blue);"></i> Produk Anda
        </div>

        <div class="product-grid">
            <input type="file" id="prodInput" accept="image/*" style="display:none">
            <div class="product-card add-btn-card" onclick="document.getElementById('prodInput').click()">
                <i class="fas fa-plus-circle" style="font-size: 2.5rem; margin-bottom: 10px;"></i>
                <span style="font-weight: 600;">Tambah Produk</span>
            </div>

            <?php
            $q_prod = mysqli_query($conn, "SELECT * FROM produk WHERE id_toko = '$id_toko' ORDER BY id DESC");
            while($p = mysqli_fetch_assoc($q_prod)):
            ?>
            <div class="product-card">
                <img src="uploads/<?php echo $p['gambar']; ?>" onerror="this.src='https://via.placeholder.com/150'">
                <div style="padding:15px; flex-grow: 1;">
                    <div style="font-weight:600; font-size: 0.9rem;"><?php echo $p['nama']; ?></div>
                    <div style="color: var(--primary-blue); font-weight: 700; margin-top:5px;">Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?></div>
                    
                    <div class="stok-info">
                        <span>Stok: <strong><?php echo $p['stok']; ?></strong></span>
                        <button class="btn-plus-stok" onclick="updateStok(<?php echo $p['id']; ?>, <?php echo $p['stok']; ?>)">+ Stok</button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
    const currentTokoId = "<?php echo $id_toko; ?>";

    // Ganti Logo
    document.getElementById('logoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const formData = new FormData();
                formData.append('id_toko', currentTokoId);
                formData.append('logo_base64', event.target.result);
                fetch('update_logo_toko.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(() => { alert("Logo diperbarui!"); window.location.reload(); });
            };
            reader.readAsDataURL(file);
        }
    });

    // Tambah Produk Baru dengan Stok
    document.getElementById('prodInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const nama = prompt("Nama Produk:");
                const harga = prompt("Harga (Angka saja):");
                const stok = prompt("Jumlah Stok Awal:");
                if (nama && harga && stok) {
                    const formData = new FormData();
                    formData.append('id_toko', currentTokoId);
                    formData.append('nama', nama);
                    formData.append('harga', harga);
                    formData.append('stok', stok);
                    formData.append('gambar', event.target.result);
                    fetch('simpan_produk.php', { method: 'POST', body: formData })
                    .then(res => res.text())
                    .then(data => { alert(data); window.location.reload(); });
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Tambah Stok Produk Lama
    function updateStok(idProduk, stokSekarang) {
        const tambah = prompt("Masukkan jumlah stok yang ingin ditambah:", "0");
        if (tambah !== null && tambah !== "") {
            const totalBaru = parseInt(stokSekarang) + parseInt(tambah);
            const formData = new FormData();
            formData.append('id_produk', idProduk);
            formData.append('stok_baru', totalBaru);

            fetch('update_stok.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(() => { alert("Stok berhasil diperbarui!"); window.location.reload(); });
        }
    }
</script>

</body>
</html>