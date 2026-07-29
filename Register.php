<?php
include "config/db.php";

$error = "";
$success = "";

if (isset($_POST['btn_register'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $password_enkripsi = md5($pass);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username Toko sudah ada, cari nama lain!";
    } else {
        $query = "INSERT INTO users (username, password, role) VALUES ('$user', '$password_enkripsi', 'user')";
        if (mysqli_query($conn, $query)) {
            $success = "Toko Berhasil Terdaftar! Mengalihkan ke halaman login...";
            
            // TAMBAHKAN LOGIKA REDIRECT DISINI
            // Menggunakan JavaScript agar pesan sukses muncul dulu selama 2 detik baru pindah
            echo "<script>
                setTimeout(function(){
                    window.location.href = 'login.php';
                }, 2000);
            </script>";
        } else {
            $error = "Gagal mendaftar, coba lagi nanti.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - LokaMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Animasi yang sama dengan Login */
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #ffffff;
            overflow: hidden;
        }

        .login-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
        }

        /* Kolom Kiri - Sama dengan Login */
        .left {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .brand {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .brand i {
            font-size: 34px;
            color: #0b63d6; /* Biru Login */
            margin-right: 10px;
        }

        .brand span {
            font-size: 26px;
            font-weight: 700;
            color: #0b63d6;
        }

        .illustration img {
            width: 100%;
            max-width: 450px;
        }

        /* Kolom Kanan - Sama dengan Login */
        .right {
            flex: 1;
            height: 100vh;
            display: flex;
            justify-content: flex-end;
        }

        .login-box {
            background: #ffffff;
            max-width: 600px;
            width: 100%;
            height: 100%;
            padding: 80px 60px;
            border-left: 2px solid rgba(11,99,214,.4);
            box-shadow: -15px 0 35px rgba(11,99,214,.15);
            animation: slideIn 0.6s ease-out; /* Animasi Masuk */
        }

        /* Fix Warna Kuning Autofill */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            -webkit-text-fill-color: #333 !important;
        }

        .form-control {
            height: 58px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            background-color: white !important;
        }

        .form-control:focus {
            border-color: #0b63d6;
            box-shadow: none;
        }

        .btn-login {
            height: 58px;
            border-radius: 12px;
            background: #0b63d6;
            color: #fff;
            font-weight: 700;
            width: 100%;
            border: none;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #084ca3;
            transform: scale(1.01);
        }

        h2 { font-weight: 700; color: #333; }
        .text-primary-custom { color: #0b63d6; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="left d-none d-md-flex">
        <div class="brand">
            <i class="fas fa-shopping-cart"></i> <span>SmartKonter</span>
        </div>
        <div class="illustration"><img src="image/Register1.png"></div>
    </div>

    <div class="right">
        <div class="login-box">
            <h2>Daftar Akun Baru</h2>
            <p>Bergabunglah dengan LokaMart dan mulai kelola tokomu.</p>

            <?php if($error): ?>
                <div class="alert alert-danger p-2 small"><?= $error ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert alert-success p-2 small"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <input type="text" name="username" class="form-control" placeholder="Pilih Username" required>
                <input type="password" name="password" class="form-control" placeholder="Buat Password" required>
                <button type="submit" name="btn_register" class="btn-login">Daftar Sekarang</button>
            </form>

            <div class="form-links text-center mt-3">
                Sudah punya akun? <a href="login.php" class="text-primary-custom">Masuk di sini</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>