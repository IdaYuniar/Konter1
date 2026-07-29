<?php
session_start();
include 'koneksi.php';

$id = $_GET['id'];

mysqli_query($conn,"
DELETE FROM penjualan_offline
WHERE id='$id'
");

echo "
<script>
alert('Data berhasil dihapus');
location='penjualan_offline.php';
</script>
";
?>