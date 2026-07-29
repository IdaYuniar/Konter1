<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id_login = $_SESSION['id_user'];
$query = mysqli_query($conn, "SELECT nama_toko FROM toko WHERE id_user = '$user_id_login'");
$data_toko = mysqli_fetch_assoc($query);
$nama_toko = $data_toko['nama_toko'] ?? "Toko Saya";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rencana Belanja Stok | LokaMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #007bff; --bg-gray: #f4f7f6; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-gray); margin: 0; padding: 20px; }
        .container { max-width: 850px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 25px; }
        .btn-back { text-decoration: none; color: #666; font-size: 0.9rem; font-weight: 600; }
        
        /* Style Baris Input */
        .form-row { display: flex; gap: 10px; margin-bottom: 15px; align-items: center; animation: fadeIn 0.3s ease; }
        input { padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-family: 'Poppins'; }
        input:focus { border-color: var(--primary-blue); box-shadow: 0 0 5px rgba(0,123,255,0.2); }
        .in-nama { flex: 4; }
        .in-stok { flex: 1.5; text-align: center; }
        .in-harga { flex: 2; }
        .btn-del { background: #ff4d4d; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; }

        .btn-add { background: #e7f3ff; color: var(--primary-blue); border: 2px dashed var(--primary-blue); width: 100%; padding: 12px; border-radius: 10px; cursor: pointer; font-weight: 600; margin-top: 10px; }
        .btn-print { background: #28a745; color: white; border: none; padding: 15px 30px; border-radius: 10px; cursor: pointer; font-weight: 700; width: 100%; margin-top: 30px; font-size: 1rem; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="container">
    <div class="header no-print">
        <a href="detail_toko.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        <h2 style="margin:0; color: var(--primary-blue);">Catatan Belanja Stok</h2>
    </div>

    <div class="no-print">
        <p style="color:#666; font-size:0.9rem;">Isi daftar barang. Klik cetak untuk mendapatkan nota belanja.</p>
        
        <div id="input-container">
            <div class="form-row">
                <input type="text" class="in-nama" placeholder="Nama Barang">
                <input type="number" class="in-stok" placeholder="Jumlah">
                <input type="text" class="in-harga" placeholder="Est. Harga">
                <button class="btn-del" onclick="hapusBaris(this)"><i class="fas fa-trash"></i></button>
            </div>
        </div>

        <button class="btn-add" onclick="tambahBaris()"><i class="fas fa-plus"></i> Tambah Item</button>
        <button class="btn-print" onclick="prosesPrint()"><i class="fas fa-print"></i> Cetak Daftar Belanja</button>
    </div>

    <div id="print-area" style="display:none;">
        <div style="text-align:center; border-bottom: 3px double #000; padding-bottom:10px; margin-bottom:20px;">
            <h1 style="margin:0; text-transform:uppercase; font-family: sans-serif;"><?php echo $nama_toko; ?></h1>
            <p style="margin:5px 0; font-family: sans-serif; font-weight: bold;">RENCANA BELANJA STOK BARANG</p>
            <p style="font-size: 0.8rem; font-family: sans-serif;">Tanggal: <?php echo date('d F Y'); ?></p>
        </div>

        <table border="1" cellspacing="0" cellpadding="10" style="width:100%; border-collapse:collapse; font-family: sans-serif;">
            <thead>
                <tr style="background:#f2f2f2;">
                    <th width="5%">No</th>
                    <th width="45%">Nama Barang</th>
                    <th width="15%">Jumlah</th>
                    <th width="20%">Est. Harga</th>
                    <th width="15%">Ceklis</th>
                </tr>
            </thead>
            <tbody id="tabel-print-body"></tbody>
        </table>

        <div style="margin-top:40px; display:flex; justify-content:space-between; font-family: sans-serif;">
            <div style="text-align:center; width:200px;">
                <p>Pemilik Toko,</p><br><br><br>
                <p>( ........................ )</p>
            </div>
            <div style="width:300px; border:1px solid #ccc; padding:10px; font-size:0.8rem;">
                <b>Catatan Tambahan:</b><br>
                <span>Cek kembali kualitas barang sebelum membayar.</span>
            </div>
        </div>
    </div>
</div>

<script>
    function tambahBaris() {
        const container = document.getElementById('input-container');
        const row = document.createElement('div');
        row.className = 'form-row';
        row.innerHTML = `
            <input type="text" class="in-nama" placeholder="Nama Barang">
            <input type="number" class="in-stok" placeholder="Jumlah">
            <input type="text" class="in-harga" placeholder="Est. Harga">
            <button class="btn-del" onclick="hapusBaris(this)"><i class="fas fa-trash"></i></button>
        `;
        container.appendChild(row);
    }

    function hapusBaris(btn) {
        if (document.querySelectorAll('.form-row').length > 1) {
            btn.closest('.form-row').remove();
        } else {
            alert("Minimal harus ada satu item!");
        }
    }

    function prosesPrint() {
        const rows = document.querySelectorAll('.form-row');
        const tableBody = document.getElementById('tabel-print-body');
        tableBody.innerHTML = ''; 
        let adaData = false;

        rows.forEach((row, index) => {
            // Ambil data dari SETIAP baris secara spesifik
            const nama = row.querySelector('.in-nama').value;
            const stok = row.querySelector('.in-stok').value;
            const harga = row.querySelector('.in-harga').value;

            if (nama.trim() !== "") {
                adaData = true;
                const tr = `<tr>
                    <td align="center">${index + 1}</td>
                    <td>${nama}</td>
                    <td align="center"><b>${stok || '0'}</b></td>
                    <td>Rp ${harga || '-'}</td>
                    <td align="center">[ &nbsp; ]</td>
                </tr>`;
                tableBody.innerHTML += tr;
            }
        });

        if (!adaData) {
            alert("Harap isi nama barang!");
            return;
        }

        const printContent = document.getElementById('print-area').innerHTML;
        const win = window.open('', '', 'height=800,width=1000');
        win.document.write('<html><head><title>Cetak Daftar Belanja</title>');
        win.document.write('<style>table{width:100%; border-collapse:collapse;} th,td{border:1px solid black; padding:10px; font-family:sans-serif;} h1,p{font-family:sans-serif;}</style>');
        win.document.write('</head><body>');
        win.document.write(printContent);
        win.document.write('</body></html>');
        win.document.close();
        
        setTimeout(() => { win.print(); }, 500);
    }
</script>
</body>
</html>