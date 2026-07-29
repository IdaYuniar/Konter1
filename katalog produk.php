<?php 
include 'koneksi.php'; 

$query_count = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keranjang");
$data_count = mysqli_fetch_assoc($query_count);
$jumlah_keranjang = $data_count['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>LokaMart | Belanja Produk UMKM</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
*{font-family:'Poppins',sans-serif;}
body{background-color:#f7f9fc;}

/* NAVBAR */
.navbar-belanja{
    background:white;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    padding:12px 0;
}

/* HEADER */
.shop-header{
    background:linear-gradient(120deg,#1C7ED6,#74c0fc);
    color:white;
    padding:70px 0;
    text-align:center;
    margin-bottom:50px;
    border-radius:0 0 25px 25px;
}

/* PRODUK CARD */
.product-card{
    background:#fff;
    border:2px solid #e3f2ff;
    border-radius:16px;
    transition:all 0.3s ease;
    height:100%;
    overflow:hidden;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    padding-bottom:10px;
}
.product-card:hover{
    transform:translateY(-6px);
    border-color:#1C7ED6;
    box-shadow:0 8px 22px rgba(28,126,214,0.15);
}
.product-card img{
    width:100%;
    height:210px;
    object-fit:cover;
    border-bottom:2px solid #e9ecef;
    border-radius:16px 16px 0 0;
    transition:transform 0.4s ease;
}
.product-card:hover img{transform:scale(1.05);}

/* LOVE ICON */
.love-icon{
    font-size:20px;
    cursor:pointer;
    color:#1C7ED6;
    transition:all 0.3s ease;
}
.love-icon.active{
    color:#ff4d6d; /* merah solid */
    transform:scale(1.15);
}

/* CART ICON (flat, elegan tanpa bulatan) */
.cart-icon{
    font-size:20px;
    cursor:pointer;
    color:#1C7ED6;
    transition:all 0.3s ease;
}
.cart-icon:hover{
    color:#0d6efd;
    transform:scale(1.15);
}
.cart-icon.active{
    color:#0b5ed7; /* biru lebih gelap saat klik */
    transform:scale(1.1);
}

/* PRICE + DETAIL BUTTON */
.price-tag{
    font-size:1.15rem;
    font-weight:700;
    color:#1C7ED6;
    display:block;
    margin-top:10px;
}
.detail-btn{
    background:#1C7ED6;
    color:white;
    padding:8px 0;
    border-radius:10px;
    font-size:14px;
    cursor:pointer;
    transition:0.25s;
    font-weight:500;
    text-align:center;
    margin-top:10px;
}
.detail-btn:hover{
    background:#1864ab;
}

/* CARD BODY */
.card-body{padding:15px 17px;}

/* SPACING ANTAR PRODUK */
.row.g-4{row-gap:2.5rem !important;}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar-belanja">
<div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand" href="belanja.php" style="color:#1C7ED6;font-weight:700;text-decoration:none;font-size:1.5rem;display:flex;align-items:center;gap:10px;">
        <i class="fas fa-shopping-cart"></i> SmartKonter
    </a>

    <div class="d-flex align-items-center">
        <input type="text" id="shopSearch" class="form-control me-3 rounded-pill" placeholder="Cari barang..." onkeyup="cariProduk()" style="max-width:200px;">
        
        <a href="keranjang.php" class="position-relative me-3 text-dark">
            <i class="bi bi-cart3 fs-4" style="color:#1C7ED6;"></i>
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill px-2 py-1"><?php echo $jumlah_keranjang; ?></span>
        </a>

        <a href="index.html" class="btn btn-outline-primary rounded-pill btn-sm fw-bold">Dashboard</a>
    </div>
</div>
</nav>

<!-- HEADER -->
<header class="shop-header">
<div class="container">
    <h1 class="display-5 fw-bold">SmartKonter On The Go</h1>
    <p class="lead">Solusi lengkap kebutuhan digital Anda, cepat, mudah, dan terpercaya.</p>
</div>
</header>

<!-- PRODUK GRID -->
<main class="container mb-5">
<div class="row g-4" id="shopGrid">
<?php
$result=mysqli_query($conn,"SELECT * FROM produk ORDER BY id DESC");
if(mysqli_num_rows($result)>0){
    while($p=mysqli_fetch_assoc($result)){
?>
<div class="col-lg-3 col-md-4 col-sm-6 product-item">
    <div class="product-card">
        <img src="uploads/<?php echo $p['gambar']; ?>" 
             alt="<?php echo $p['nama']; ?>" 
             onerror="this.src='https://via.placeholder.com/300x210?text=No+Image';" 
             onclick="lihatDetail(<?php echo $p['id']; ?>)">
        <div class="card-body">
            <p class="text-muted small mb-1"><i class="bi bi-shop"></i> <?php echo $p['toko']; ?></p>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold text-truncate mb-0"><?php echo $p['nama']; ?></h6>
                <div class="d-flex gap-3">
                    <i class="bi bi-heart love-icon" onclick="toggleLove(this)"></i>
                    <i class="fas fa-cart-shopping cart-icon" onclick="toggleCart(this, <?php echo $p['id']; ?>)"></i>
                </div>
            </div>

            <span class="price-tag">Rp <?php echo number_format($p['harga'],0,',','.'); ?></span>

            <div class="detail-btn" onclick="lihatDetail(<?php echo $p['id']; ?>)">
                <i class="bi bi-eye"></i> Detail
            </div>
        </div>
    </div>
</div>
<?php 
    } 
}else{
    echo '<div class="col-12 text-center"><h3>Belum ada produk tersedia.</h3></div>';
}
?>
</div>
</main>

<script>
function cariProduk(){
    let input=document.getElementById('shopSearch').value.toLowerCase();
    let items=document.querySelectorAll('.product-item');
    items.forEach(item=>{
        let title=item.querySelector('h6').innerText.toLowerCase();
        item.style.display=title.includes(input)?"":"none";
    });
}
function lihatDetail(id){window.location.href='detail.php?id='+id;}

// efek toggle love
function toggleLove(el){
    el.classList.toggle("bi-heart");
    el.classList.toggle("bi-heart-fill");
    el.classList.toggle("active");
}

// efek toggle keranjang
function toggleCart(el, id){
    el.classList.toggle("active");
    el.style.transition = "color 0.3s ease, transform 0.2s ease";
    if(el.classList.contains("active")){
        el.style.color = "#0b5ed7";
        el.style.transform = "scale(1.2)";
        setTimeout(()=>{el.style.transform="scale(1)";},150);
        // redirect ke aksi keranjang
        window.location.href = 'aksi_keranjang.php?id=' + id;
    } else {
        el.style.color = "#1C7ED6";
    }
}
</script>

</body>
</html>
