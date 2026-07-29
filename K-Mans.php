<?php
include 'koneksi.php';

// DATA URUT BERDASARKAN NAMA (Agar Pulsa ketemu Pulsa, Voucher ketemu Voucher)
$sql = "SELECT 
            p.id,
            p.nama,
            COALESCE(SUM(t.jumlah), 0) AS total_terjual,
            COALESCE(SUM(t.total_harga), 0) AS total_pendapatan
        FROM produk p
        LEFT JOIN transaksi t 
            ON p.id = t.produk_id AND t.status='Disetujui'
        GROUP BY p.id
        ORDER BY p.nama ASC"; // Ditambahkan ORDER BY agar rapi berurutan

$result = mysqli_query($conn, $sql);

$data = [];
while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>K-Means Produk SmartKonter</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    font-family: 'Poppins', sans-serif;
    background: #f4f7f6;
}

.card{
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.table thead{
    background: #1C7ED6;
    color: white;
}
</style>
</head>

<body>

<div class="container py-4">

<div class="mb-4">
    <h2>🎯 K-Means Clustering Produk</h2>
    <p class="text-muted">Analisis Produk Laris & Kurang Laris SmartKonter</p>
</div>

<div class="card p-3 mb-3">
    <div class="d-flex gap-3 align-items-center">
        <label class="fw-bold">Jumlah Cluster (K):</label>
        <input type="number" id="kValue" value="3" min="2" max="5" class="form-control w-25">

        <button class="btn btn-primary" onclick="loadData()">Load Data</button>
        <button class="btn btn-success" onclick="runKMeans()">Jalankan K-Means</button>
    </div>
</div>

<div class="card p-3">
    <h5>📊 Data Produk</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Total Terjual</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody id="tableBody"></tbody>
    </table>
</div>

<div class="card p-3">
    <h5>📈 Hasil Cluster</h5>
    <div id="result"></div>
</div>

<div class="card p-3">
    <h5>📊 Visualisasi Bar Chart</h5>
    <canvas id="chart"></canvas>
</div>

</div>

<script>
let data = <?php echo json_encode($data); ?>;
let chartInstance = null;

// Otomatis load data saat halaman selesai dimuat
window.onload = function() {
    loadData();
};

// LOAD TABLE & INITIAL CHART
function loadData(){
    let html = "";
    let points = data.map(d => ({
        nama: d.nama,
        x: parseFloat(d.total_terjual),
        cluster: null
    }));

    data.forEach(d=>{
        html += `
        <tr>
            <td>${d.nama}</td>
            <td>${d.total_terjual}</td>
            <td>Rp ${Number(d.total_pendapatan).toLocaleString('id-ID')}</td>
        </tr>`;
    });
    document.getElementById("tableBody").innerHTML = html;
    
    // Tampilkan grafik awal kosong/netral sebelum di-cluster
    drawChart(points, []);
}

// K-MEANS ALGORITHM
function runKMeans(){
    let K = parseInt(document.getElementById("kValue").value);

    let points = data.map(d => ({
        nama: d.nama,
        x: parseFloat(d.total_terjual),
        cluster: null
    }));

    if(points.length < K){
        alert("Jumlah data lebih sedikit dari jumlah cluster!");
        return;
    }

    // Centroid awal menyebar
    let centroids = [];
    let step = Math.max(1, Math.floor((points.length-1)/(K-1)));
    for(let i=0;i<K;i++){
      let idx=Math.min(i*step,points.length-1);
      centroids.push({x:points[idx].x});
    }

    let clusters = [];
    let changed = true;
    let maxIter = 100;
    let iter = 0;

    while(changed && iter < maxIter){
        changed = false;
        clusters = Array.from({length: K}, () => []);

        // Assign cluster
        points.forEach(p => {
            let minDist = Infinity;
            let index = 0;

            centroids.forEach((c,i) => {
                let dist = Math.abs(p.x - c.x);
                if(dist < minDist){
                    minDist = dist;
                    index = i;
                }
            });

            p.cluster = index;
            clusters[index].push(p);
        });

        // Update centroid
        centroids.forEach((c,i) => {
            if(clusters[i].length > 0){
                let newCentroid = clusters[i].reduce((sum,p)=>sum+p.x,0) / clusters[i].length;
                if(newCentroid !== c.x){
                    changed = true;
                }
                c.x = newCentroid;
            }
        });

        iter++;
    }

    showResult(clusters, centroids);
    drawChart(points, centroids);
}

// SHOW RESULT
function showResult(clusters, centroids){
    let html="";
    let avg=clusters.map(c=>c.length?c.reduce((s,p)=>s+p.x,0)/c.length:0);
    let rank=avg.map((v,i)=>({i,v})).sort((a,b)=>b.v-a.v);
    let labels=[];
    if(clusters.length>=3){
      labels[rank[0].i]="🔥 Produk Laris";
      labels[rank[1].i]="👍 Produk Kurang Laris";
      labels[rank[2].i]="❌ Produk Tidak Laris";
      for(let j=3;j<rank.length;j++) labels[rank[j].i]="Cluster "+(j+1);
    }else{
      labels[rank[0].i]="🔥 Produk Laris";
      labels[rank[1].i]="❌ Produk Tidak Laris";
    }
    clusters.forEach((cluster,index)=>{
      cluster.sort((a,b)=>a.nama.localeCompare(b.nama));
      html+=`<div class="card mb-3"><div class="card-body"><h5>${labels[index]}</h5><p><b>Centroid :</b> ${centroids[index].x.toFixed(2)}</p><table class="table table-bordered"><thead><tr><th>Produk</th><th>Total Terjual</th></tr></thead><tbody>`;
      cluster.forEach(p=>{html+=`<tr><td>${p.nama}</td><td>${p.x}</td></tr>`});
      html+=`</tbody></table></div></div>`;
    });
    document.getElementById("result").innerHTML=html;
}

// CHART
function drawChart(points, centroids){
    if(chartInstance){
        chartInstance.destroy();
    }

    let labels = points.map(p => p.nama);
    let backgroundColors = points.map(p => {
        if (p.cluster === null) return '#1C7ED6'; // Warna default sebelum di-cluster
        switch(p.cluster){
            case 0: return '#36A2EB';
            case 1: return '#FF6384';
            case 2: return '#4BC0C0';
            case 3: return '#FFCE56';
            default: return '#9966FF';
        }
    });

    let ctx = document.getElementById("chart").getContext("2d");

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Terjual',
                data: points.map(p => p.x),
                backgroundColor: backgroundColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Terjual'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Produk'
                    }
                }
            }
        }
    });
}
</script>

</body>
</html>