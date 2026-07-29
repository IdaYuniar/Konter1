<?php
include 'koneksi.php';
$message = ""; $status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama-toko']);
    $desc = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $wa   = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    $addr = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    // Proses Upload Logo
    $logo_db = "default-logo.png";
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $logo_db = time() . "_" . basename($_FILES["avatar"]["name"]);
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_dir . $logo_db);
    }

    $sql = "INSERT INTO toko (nama_toko, deskripsi, whatsapp, alamat, logo) VALUES ('$nama', '$desc', '$wa', '$addr', '$logo_db')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: kumpulan-toko.php?notif=sukses");
    } else {
        $status = "error"; $message = "Gagal mendaftar: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mendaftarkan Toko | LokaMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root { --primary-blue: #007bff; --light-gray: #f4f4f4; --text-dark: #333; --error-red: #dc3545; --success-green: #28a745; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--light-gray); color: var(--text-dark); display: flex; flex-direction: column; min-height: 100vh; }
        .header { background-color: var(--primary-blue); color: white; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; }
        .logo-text { font-size: 1.5em; font-weight: 700; text-decoration: none; color: white; display: flex; align-items: center; }
        .main-content { flex-grow: 1; padding: 40px 0; display: flex; justify-content: center; }
        .form-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .alert-error { background: #f8d7da; color: var(--error-red); }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 5px; }
        .input-group input, .input-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; }
        .avatar-uploader { text-align: center; margin-bottom: 25px; }
        .avatar-placeholder { width: 100px; height: 100px; background: #eee; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px dashed #ccc; }
        .avatar-placeholder img { width: 100%; height: 100%; object-fit: cover; }
        .btn-submit { width: 100%; padding: 15px; background: var(--primary-blue); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #0056b3; }
        .footer { background: var(--primary-blue); color: white; padding: 20px 50px; display: flex; justify-content: space-between; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="logo-text"><i class="fas fa-shopping-cart" style="margin-right: 8px;"></i> LokaMart</a>
    </div>

    <div class="main-content">
        <div class="form-container">
            <?php if ($message): ?> <div class="alert alert-error"><?php echo $message; ?></div> <?php endif; ?>
            <h2 style="text-align: center; margin-top: 0; color: var(--primary-blue);">Daftarkan Toko UMKM</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="avatar-uploader">
                    <div class="avatar-placeholder" id="avatar-preview"><i class="fas fa-store" style="font-size: 2.5em; color: #aaa;"></i></div>
                    <input type="file" name="avatar" id="avatar-input" style="display:none" accept="image/*">
                    <button type="button" onclick="document.getElementById('avatar-input').click()" style="cursor:pointer; padding: 5px 15px; border-radius: 20px; border: 1px solid #ccc;">Unggah Logo</button>
                </div>
                <div class="input-group"><label>Nama Toko *</label><input type="text" name="nama-toko" required></div>
                <div class="input-group"><label>Deskripsi *</label><textarea name="deskripsi" rows="3" required></textarea></div>
                <div class="input-group"><label>WhatsApp *</label><input type="text" name="whatsapp" placeholder="62812345678" required></div>
                <div class="input-group"><label>Alamat *</label><input type="text" name="alamat" required></div>
                <button type="submit" class="btn-submit">Daftar Sekarang</button>
            </form>
        </div>
    </div>

    <div class="footer"><div>© 2026 LokaMart</div><div><a href="kumpulan-toko.php" style="color:white">Lihat Semua Toko</a></div></div>

    <script>
        document.getElementById('avatar-input').onchange = function (e) {
            const [file] = this.files;
            if (file) { document.getElementById('avatar-preview').innerHTML = `<img src="${URL.createObjectURL(file)}">`; }
        }
    </script>
</body>
</html>