<?php

include 'koneksi.php';

$nama=$_POST['nama'];
$pesan=$_POST['pesan'];

mysqli_query($conn,"
INSERT INTO testimoni
(nama,pesan)
VALUES
('$nama','$pesan')
");

header("Location:index.php");

?>