<?php
include 'koneksi.php';

$tahun = isset($_GET['tahun'])
    ? $_GET['tahun']
    : date('Y');

$sql = "
SELECT
    p.id,
    p.nama,
    COALESCE(SUM(t.jumlah),0) AS total_terjual,
    COALESCE(SUM(t.total_harga),0) AS total_pendapatan
FROM produk p
LEFT JOIN transaksi t
    ON p.id = t.produk_id
    AND t.status='Disetujui'
    AND YEAR(t.tanggal)='$tahun'
GROUP BY p.id,p.nama
";

$result = mysqli_query($conn,$sql);

$data = [];

while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);