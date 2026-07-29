<?php
session_start();
include "config/db.php"; // Pastikan path ke database sudah benar

if (isset($_POST['btn_login'])) {
    $user = mysqli_real_escape_string($conn, trim($_POST['username']));
$pass = mysqli_real_escape_string($conn, trim($_POST['password']));

$query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$pass'");
    if (mysqli_num_rows($query) > 0) {
    
        $data = mysqli_fetch_assoc($query);
        
        // SIMPAN IDENTITAS KE SESSION
        $_SESSION['id_user']  = $data['id_user']; 
        $_SESSION['username'] = $data['username'];

        $id_current_user = $data['id_user']; 

        // CEK APAKAH USER INI SUDAH PUNYA TOKO DI TABEL TOKO
        $cek_toko = mysqli_query($conn, "SELECT id FROM toko WHERE id_user = '$id_current_user'");
        $data_toko = mysqli_fetch_assoc($cek_toko);
        
        if ($data_toko) {
            // Jika SUDAH punya toko, simpan ID Tokonya ke session
            $_SESSION['id_toko'] = $data_toko['id'];
            // Langsung masuk ke detail toko miliknya
            header("Location: detail_toko.php");
        } else {
            // Jika BELUM punya toko, arahkan ke halaman buat toko baru
            header("Location: registrasi-toko.php"); 
        }
        exit();
    } else {
        echo "<script>alert('Username atau Password salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - LokaMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #ffffff; }
        .login-wrapper { height: 100vh; display: flex; align-items: center; }
        .left { flex: 1; padding: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .brand { display: flex; align-items: center; margin-bottom: 40px; text-decoration: none; }
        .brand i { font-size: 34px; color: #0b63d6; margin-right: 10px; }
        .brand span { font-size: 26px; font-weight: 700; color: #0b63d6; }
        .illustration img { width: 100%; max-width: 450px; }
        .right { flex: 1; height: 100vh; display: flex; justify-content: flex-end; }
        .login-box { background: #ffffff; max-width: 600px; width: 100%; height: 100%; padding: 80px 60px; border-left: 2px solid rgba(11,99,214,.15); box-shadow: -15px 0 35px rgba(11,99,214,.05); display: flex; flex-direction: column; justify-content: center; }
        .form-control { height: 58px; border-radius: 12px; margin-bottom: 20px; }
        .btn-login { height: 58px; border-radius: 12px; background: #0b63d6; color: #fff; font-weight: 700; width: 100%; border: none; }
        h2 { font-weight: 700; color: #333; }
        
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="left d-none d-md-flex">
        <a href="index.php" class="brand"><i class="fas fa-shopping-cart"></i><span>SmartKonter</span></a>
        <div class="illustration"><img src="image/login konter.png"></div>
    </div>
    <div class="right">
        <div class="login-box">
            <h2>Selamat Datang!</h2>
            <p>Masuk untuk mengelola toko Anda.</p>
            <form method="POST" autocomplete="off">
                <input type="text" name="username" class="form-control" placeholder="Nama Pengguna" required>
                <input type="password" name="password" class="form-control" placeholder="Kata Sandi" required>
                <button type="submit" name="btn_login" class="btn-login">Masuk Sekarang</button>
            </form>
            <div class="text-center mt-3">
                Belum punya akun? <a href="register.php" class="text-primary fw-bold">Daftar Akun</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>