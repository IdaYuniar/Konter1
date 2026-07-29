<?php 
include 'koneksi.php';

// --- LOGIKA SIMPAN KE DATABASE ---
if (isset($_POST['proses_ulasan'])) {
    $id_p = intval($_POST['id_produk']);
    $nama_p = mysqli_real_escape_string($conn, $_POST['nama']);
    $rating_p = intval($_POST['rating']);
    $isi_p = mysqli_real_escape_string($conn, $_POST['ulasan']);

    mysqli_query($conn, "INSERT INTO ulasan (id_produk, nama_pembeli, rating, komentar) VALUES ('$id_p', '$nama_p', '$rating_p', '$isi_p')");
    exit; 
}

// Ambil ID produk
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM produk WHERE id = $id");
    $produk = mysqli_fetch_assoc($query);
    if (!$produk) { die("Produk tidak ditemukan!"); }
} else {
    die("ID produk tidak diberikan!");
}

// Ambil ulasan lama dari database
$tampil_ulasan = mysqli_query($conn, "SELECT * FROM ulasan WHERE id_produk = $id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk | LokaMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
        header{background:linear-gradient(90deg, #1C7ED6, #1C7ED6);color:white;padding:15px 40px;display:flex;justify-content:space-between;align-items:center;}
        header a{color:white;text-decoration:none;font-weight:600;}
        .container{display:flex;gap:30px;padding:40px;max-width:1200px;margin:auto;}
        .main-img{width:100%;height:400px;border-radius:10px;object-fit:cover; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
        .product-info h2{font-size:28px;margin-bottom:5px;}
        .price{font-size:24px;color:#1C7ED6;font-weight:bold;margin-bottom:10px;}
        .store-name { color: #555; display: block; margin-bottom: 20px; } 
        .btn-primary{background:#1C7ED6;color:white;padding:10px 20px;border:none;border-radius:6px;cursor:pointer; transition: background 0.3s;}
        .btn-primary:hover { background: #145da8; }
        .btn-outline{border:2px solid #1C7ED6;background:white;color:#1C7ED6;padding:8px 15px;border-radius:6px;cursor:pointer; margin-left: 5px; transition: all 0.3s;}
        .btn-outline:hover { background: #f0f8ff; }
        .btn-whatsapp { background-color: #25D366; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-top: 10px; transition: background 0.3s; }
        .btn-whatsapp:hover { background-color: #1EBE5D; }
        .review-item{border-top:1px solid #ddd;padding:15px 0;}
        .avatar{width:35px;height:35px;border-radius:50%;background:#00712d;color:white;display:flex;align-items:center;justify-content:center;margin-right:10px;font-weight:600;}
        .review-header{display:flex;align-items:center; margin-bottom: 5px;}
        #popup { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:1000; }
        #popup > div { background:white; padding:30px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.2); max-width:450px; width:90%; }
        #popup input[type="text"], #popup textarea { width:100%; margin:8px 0; padding:10px; border:1px solid #ddd; border-radius:4px; }
        .rating-input { margin-bottom:15px; text-align:center; }
        .rating-input .bi-star-fill { font-size:2rem; color:#ccc; cursor:pointer; transition:color 0.2s; }
        .rating-input .bi-star-fill.active { color:gold; }
        .review-rating { color:gold; font-size:1.1rem; margin-bottom:5px; }
        @media (max-width: 768px) {
            .container { flex-direction: column; padding: 20px; }
            .main-img { height: 300px; }
            section { padding: 0 20px !important; }
        }
        
    </style>
    
</head>
<body>
    <header>
        <div class="logo">DETAIL PRODUK</div>
        <a href="index.HTML">Kembali ke Beranda</a>
    </header>

    <div class="container">
        <div style="flex:1;">
            <img id="mainImage" class="main-img" src="uploads/<?php echo $produk['gambar']; ?>" alt="<?php echo $produk['nama']; ?>">
        </div>
        <div style="flex:1;" class="product-info">
            <h2><?php echo $produk['nama']; ?></h2>
            <div class="price">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></div>
            <small class="store-name">Dari Toko: <strong><?php echo $produk['toko']; ?></strong></small>
            <button class="btn-primary" id="addReview">Tulis Ulasan</button>
            <button class="btn-whatsapp" id="btnWa"><i class="bi bi-whatsapp"></i> Pesan via WhatsApp</button>
            <button class="btn-outline" onclick="window.location.href='transaksi.php?id=<?php echo $produk['id']; ?>'">
                <i class="bi bi-cash-coin"></i> Lanjutkan Pembelian
            </button>
        </div>
    </div>

    <section style="padding:0 40px; max-width:1200px; margin:auto;">
        <h3>Ulasan Pembeli</h3>
        <div id="reviewList">
            <?php while($row = mysqli_fetch_assoc($tampil_ulasan)): 
                $nama_dpn = explode(' ', $row['nama_pembeli']);
                $inisial = strtoupper(substr($nama_dpn[0], 0, 1) . (isset($nama_dpn[1]) ? substr($nama_dpn[1], 0, 1) : ''));
            ?>
            <div class="review-item">
                <div class="review-rating">
                    <?php for($i=1; $i<=5; $i++) {
                        echo ($i <= $row['rating']) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star-fill" style="color:#ccc;"></i>';
                    } ?>
                </div>
                <div class="review-header"><div class="avatar"><?php echo $inisial; ?></div><strong><?php echo htmlspecialchars($row['nama_pembeli']); ?></strong></div>
                <p><?php echo htmlspecialchars($row['komentar']); ?></p>
                <button class="btn-outline" style="margin-top:5px;" onclick="this.parentElement.remove()">Hapus</button>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <div id="popup">
        <div>
            <h3>Tulis Ulasan</h3>
            <div class="rating-input" id="ratingInput">
                <i class="bi bi-star-fill" data-rating="1"></i>
                <i class="bi bi-star-fill" data-rating="2"></i>
                <i class="bi bi-star-fill" data-rating="3"></i>
                <i class="bi bi-star-fill" data-rating="4"></i>
                <i class="bi bi-star-fill" data-rating="5"></i>
            </div>
            <input type="text" id="nama" placeholder="Nama Anda" required>
            <textarea id="ulasan" placeholder="Tulis ulasan di sini..." required></textarea>
            <input type="file" id="imgReview" accept="image/*">
            <button class="btn-primary" id="kirim">Kirim</button>
            <button class="btn-outline" id="batal">Batal</button>
        </div>
    </div>

    <script>
    let selectedRating = 0;
    const popup = document.getElementById('popup');
    const reviewList = document.getElementById('reviewList');
    const ratingStars = document.querySelectorAll('#ratingInput .bi-star-fill');

    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            selectedRating = rating;
            ratingStars.forEach((s, index) => {
                if (index < rating) s.classList.add('active');
                else s.classList.remove('active');
            });
        });
    });

    document.getElementById('addReview').onclick = () => {
        popup.style.display = 'flex';
        selectedRating = 0;
        ratingStars.forEach(star => star.classList.remove('active'));
    };
    document.getElementById('batal').onclick = () => popup.style.display = 'none';

    // FUNGSI KIRIM: SIMPAN KE DB + TAMPIL DI LAYAR (SESUAI FITUR ASLIMU)
    document.getElementById('kirim').onclick = () => {
        const nama = document.getElementById('nama').value.trim();
        const ulasan = document.getElementById('ulasan').value.trim();
        const file = document.getElementById('imgReview').files[0];

        if (selectedRating === 0) return alert("Mohon berikan rating bintang!");
        if (!nama || !ulasan) return alert("Mohon isi Nama dan Ulasan Anda!");

        // AJAX Simpan ke Database
        const fd = new FormData();
        fd.append('proses_ulasan', '1');
        fd.append('id_produk', '<?php echo $id; ?>');
        fd.append('nama', nama);
        fd.append('rating', selectedRating);
        fd.append('ulasan', ulasan);
        fetch(window.location.href, { method: 'POST', body: fd });

        // Tampilkan di layar (Logika Asli Kamu)
        const avatar = nama.split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2);
        let imgHTML = '';
        if (file) {
            const imgURL = URL.createObjectURL(file);
            imgHTML = `<img src="${imgURL}" alt="Foto Ulasan" style="width:80px;height:80px;border-radius:8px;object-fit:cover;margin-top:8px;display:block;">`;
        }

        const getStarHTML = (rating) => {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star-fill" style="color:#ccc;"></i>';
            }
            return stars;
        };

        const item = document.createElement('div');
        item.className = 'review-item';
        item.innerHTML = `
            <div class="review-rating">${getStarHTML(selectedRating)}</div> 
            <div class="review-header">
                <div class="avatar">${avatar}</div>
                <strong>${nama}</strong>
            </div>
            <p>${ulasan}</p>
            ${imgHTML}
            <button class="btn-outline" style="margin-top:5px;" onclick="this.parentElement.remove()">Hapus</button>
        `;
        reviewList.prepend(item);
        
        popup.style.display = 'none';
        document.getElementById('nama').value = '';
        document.getElementById('ulasan').value = '';
        document.getElementById('imgReview').value = '';
    };

    // Tombol WhatsApp
    document.getElementById("btnWa").onclick = function() {
        const nomor = "082115329791"; 
        const pesan = `Halo, saya tertarik memesan produk *<?php echo $produk['nama']; ?>* di toko <?php echo $produk['toko']; ?>.`;
        window.open(`https://wa.me/${nomor}?text=${encodeURIComponent(pesan)}`, "_blank"); 
        alert("Pesanan Anda sudah dikirim lewat WhatsApp ✅");
    };

    </script>
</body>
</html>