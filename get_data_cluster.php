<?php

include 'koneksi.php';

$bulan=$_GET['bulan'];

$sql="SELECT
p.id,
p.nama,
COALESCE(SUM(t.jumlah),0) total_terjual,
COALESCE(SUM(t.total_harga),0) total_pendapatan

FROM produk p

LEFT JOIN transaksi t
ON p.id=t.produk_id
AND t.status='Disetujui'
AND MONTH(t.tanggal)='$bulan'

GROUP BY p.id
ORDER BY p.nama ASC";

$query=mysqli_query($conn,$sql);

$data=[];

while($row=mysqli_fetch_assoc($query)){
    $data[]=$row;
}

echo json_encode($data);