<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi.php sudah benar

// 1. PROTEKSI: Cek login agar id_user tersedia
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit();
}

// Ambil ID User yang sedang login secara otomatis (Contoh: Dela = 14)
$current_user_id = $_SESSION['id_user']; 

// 2. INISIALISASI VARIABEL
$id_toko = isset($_GET['id']) ? intval($_GET['id']) : 0;
$nama_toko = ""; $deskripsi = ""; $whatsapp = ""; $alamat = ""; $logo = "";
$mode_edit = false;

// 3. MODE EDIT: Ambil data lama berdasarkan ID Toko
if ($id_toko > 0) {
    $query = mysqli_query($conn, "SELECT * FROM toko WHERE id = '$id_toko'");
    $data = mysqli_fetch_assoc($query);
    if ($data) {
        $nama_toko = $data['nama_toko'];
        $deskripsi = $data['deskripsi'];
        $whatsapp  = $data['whatsapp'];
        $alamat    = $data['alamat'];
        $logo      = $data['logo'];
        $mode_edit = true;
    }
}

// 4. LOGIKA SIMPAN DATA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_toko']);
    $desc = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $wa   = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    $loc  = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Handle Upload Gambar
    $nama_file_simpan = $logo; 
    if (!empty($_FILES['logo']['name'])) {
        $nama_file_simpan = time() . '_' . $_FILES['logo']['name'];
        if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
        move_uploaded_file($_FILES['logo']['tmp_name'], 'uploads/' . $nama_file_simpan);
    }

    if ($mode_edit) {
        // QUERY UPDATE
        $sql = "UPDATE toko SET 
                nama_toko='$nama', deskripsi='$desc', whatsapp='$wa', alamat='$loc', logo='$nama_file_simpan' 
                WHERE id='$id_toko'";
    } else {
        // QUERY INSERT: id_user menggunakan $current_user_id dari Session
        $sql = "INSERT INTO toko (nama_toko, deskripsi, whatsapp, alamat, logo, id_user) 
                VALUES ('$nama', '$desc', '$wa', '$loc', '$nama_file_simpan', '$current_user_id')";
    }

    if (mysqli_query($conn, $sql)) {
        // Ambil ID toko yang baru saja dibuat atau diupdate
        $last_id = $mode_edit ? $id_toko : mysqli_insert_id($conn);
        
        // Simpan ke session toko agar detail_toko.php tahu toko mana yang dibuka
        $_SESSION['id_toko'] = $last_id; 
        
        echo "<script>alert('Berhasil disimpan!'); window.location.href='detail_toko.php';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $mode_edit ? 'Edit Toko' : 'Daftar Toko'; ?> | LokaMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root { --primary-blue: #007bff; --light-gray: #f4f4f4; --text-dark: #333; --error-red: #dc3545; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--light-gray); color: var(--text-dark); min-height: 100vh; }
        .header { background-color: var(--primary-blue); color: white; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; }
        .logo-text { font-size: 1.5em; font-weight: 700; cursor: pointer; text-decoration: none; color: white; }
        .main-content { padding: 40px 0; display: flex; justify-content: center; }
        .form-container { background-color: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); width: 100%; max-width: 500px; text-align: center; }
        .input-group { text-align: left; margin-bottom: 20px; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 5px; }
        .input-group input, .input-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-family: inherit; }
        .avatar-placeholder { width: 120px; height: 120px; background-color: #e0e0e0; border-radius: 50%; margin: 0 auto 15px; display: flex; justify-content: center; align-items: center; overflow: hidden; border: 2px solid #ddd; }
        .avatar-placeholder img { width: 100%; height: 100%; object-fit: cover; }
        .btn-submit { width: 100%; padding: 15px; background-color: var(--primary-blue); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 16px; }
        .btn-submit:hover { background-color: #0056b3; transform: translateY(-2px); }
        .btn-pilih { background: #eee; border: 1px solid #ccc; padding: 8px 15px; border-radius: 5px; margin-bottom: 20px; cursor: pointer; font-size: 13px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="logo-text"><i class="fas fa-shopping-cart"></i> LokaMart</a>
        <span><i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?></span>
    </div>

    <div class="main-content">
        <div class="form-container">
            <h1><?php echo $mode_edit ? 'Edit Toko' : 'Buat Toko Baru'; ?></h1>
            <p style="color: #666; margin-bottom: 30px;">Isi data toko Anda dengan benar untuk mulai berjualan.</p>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="avatar-uploader">
                    <div class="avatar-placeholder" id="avatar-preview">
                        <?php if($logo): ?>
                            <img src="uploads/<?php echo $logo; ?>">
                        <?php else: ?>
                            <i class="fas fa-store" style="font-size: 3em; color: #aaa;"></i>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="logo" id="avatar-file-input" accept="image/*" style="display:none"> 
                    <button type="button" class="btn-pilih" onclick="document.getElementById('avatar-file-input').click()">Pilih Logo Toko</button>
                </div>
                
                <div class="input-group">
                    <label>Nama Toko</label>
                    <input type="text" name="nama_toko" value="<?php echo $nama_toko; ?>" required>
                </div>
                <div class="input-group">
                    <label>Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="3" required><?php echo $deskripsi; ?></textarea>
                </div>
                <div class="input-group">
                    <label>Nomor WhatsApp</label>
                    <input type="text" name="whatsapp" value="<?php echo $whatsapp; ?>" required>
                </div>
                <div class="input-group">
                    <label>Alamat Toko</label>
                    <input type="text" name="alamat" value="<?php echo $alamat; ?>" required>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> <?php echo $mode_edit ? 'Simpan Perubahan' : 'Daftarkan Toko Sekarang'; ?>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('avatar-file-input').addEventListener('change', function(e) {
            const preview = document.getElementById('avatar-preview');
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.innerHTML = `<img src="${event.target.result}">`;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>