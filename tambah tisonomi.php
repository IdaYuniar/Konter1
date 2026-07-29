<section class="testimonial-section">

<div class="container">

<h2 class="section-title">
Apa Kata Pelanggan?
</h2>

<div class="testimonial-slider">

<?php
include 'koneksi.php';

$query=mysqli_query($conn,
"SELECT * FROM testimoni ORDER BY id DESC");

$i=0;

while($data=mysqli_fetch_assoc($query)){
?>

<div class="testimonial-item <?php echo ($i==0)?'active':''; ?>">

<p class="testimonial-text">
"<?php echo $data['pesan']; ?>"
</p>

<div class="stars">
⭐⭐⭐⭐⭐
</div>

<h5>
<?php echo $data['nama']; ?>
</h5>

</div>

<?php
$i++;
}
?>

</div>

<div id="testimonial-dots"></div>

</div>
</section>