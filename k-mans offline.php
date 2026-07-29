<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id_login = $_SESSION['id_user'];

$q_toko = mysqli_query($conn, "SELECT id FROM toko WHERE id_user='$user_id_login'");
$toko = mysqli_fetch_assoc($q_toko);
$id_toko = $toko['id'] ?? 0;

$sql = "SELECT 
        nama_produk AS nama,
        SUM(CASE WHEN MONTH(tanggal)=1 THEN jumlah ELSE 0 END) AS januari,
        SUM(CASE WHEN MONTH(tanggal)=2 THEN jumlah ELSE 0 END) AS februari,
        SUM(CASE WHEN MONTH(tanggal)=3 THEN jumlah ELSE 0 END) AS maret,
        SUM(CASE WHEN MONTH(tanggal)=4 THEN jumlah ELSE 0 END) AS april,
        SUM(CASE WHEN MONTH(tanggal)=5 THEN jumlah ELSE 0 END) AS mei,
        SUM(CASE WHEN MONTH(tanggal)=6 THEN jumlah ELSE 0 END) AS juni,
        SUM(CASE WHEN MONTH(tanggal)=7 THEN jumlah ELSE 0 END) AS juli,
        SUM(CASE WHEN MONTH(tanggal)=8 THEN jumlah ELSE 0 END) AS agustus,
        SUM(CASE WHEN MONTH(tanggal)=9 THEN jumlah ELSE 0 END) AS september,
        SUM(CASE WHEN MONTH(tanggal)=10 THEN jumlah ELSE 0 END) AS oktober,
        SUM(CASE WHEN MONTH(tanggal)=11 THEN jumlah ELSE 0 END) AS november,
        SUM(CASE WHEN MONTH(tanggal)=12 THEN jumlah ELSE 0 END) AS desember,
        SUM(jumlah) AS total_terjual
        FROM penjualan_offline
        WHERE id_toko='$id_toko'
        GROUP BY nama_produk
        ORDER BY nama_produk";

$result = mysqli_query($conn, $sql);

$data = [];
while($row = mysqli_fetch_assoc($result)){
    $data[] = [
        'nama' => $row['nama'],
        'januari' => (float)$row['januari'],
        'februari' => (float)$row['februari'],
        'maret' => (float)$row['maret'],
        'april' => (float)$row['april'],
        'mei' => (float)$row['mei'],
        'juni' => (float)$row['juni'],
        'juli' => (float)$row['juli'],
        'agustus' => (float)$row['agustus'],
        'september' => (float)$row['september'],
        'oktober' => (float)$row['oktober'],
        'november' => (float)$row['november'],
        'desember' => (float)$row['desember'],
        'total_terjual' => (int)$row['total_terjual']
    ];
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
    <h2>🎯 K-Means Clustering Produk Berdasarkan Bulan</h2>
    <p class="text-muted">Analisis Pengelompokan Produk Berdasarkan Filter Bulan Tertentu</p>
    <a href="penjualan_offline.php" class="btn btn-secondary btn-sm mb-3">← Kembali ke Catatan Penjualan</a>
</div>

<div class="card p-3 mb-3">
    <div class="row g-3 align-items-center">
        <div class="col-md-3">
            <label class="fw-bold form-label">Pilih Bulan Analisis:</label>
            <select id="bulanSelect" class="form-select">
                <option value="januari">Januari</option>
                <option value="februari" selected>Februari</option>
                <option value="maret">Maret</option>
                <option value="april">April</option>
                <option value="mei">Mei</option>
                <option value="juni">Juni</option>
                <option value="juli">Juli</option>
                <option value="agustus">Agustus</option>
                <option value="september">September</option>
                <option value="oktober">Oktober</option>
                <option value="november">November</option>
                <option value="desember">Desember</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="fw-bold form-label">Jumlah Cluster (K):</label>
            <input type="number" id="kValue" value="3" min="2" max="5" class="form-control">
        </div>
        <div class="col-md-7 d-flex align-items-end gap-2 pt-2">
            <button class="btn btn-primary" onclick="loadData()">Load Data</button>
            <button class="btn btn-success" onclick="runKMeans()">Jalankan K-Means</button>
        </div>
    </div>
</div>

<div class="card p-3">
    <h5 id="tableTitle">📊 Data Produk & Penjualan Bulanan</h5>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th id="thBulan">Jumlah Terjual (Bulan Terpilih)</th>
                    <th>Total Keseluruhan</th>
                </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
</div>

<div class="card p-3">
    <h5>📈 Hasil Akhir Cluster K-Means</h5>
    <div id="result"></div>
</div>

<div class="card p-3">
    <h5>📊 Visualisasi Bar Chart Penjualan</h5>
    <canvas id="chart"></canvas>
</div>

<!-- PENJABARAN RUMUS -->
<div class="card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">📝 Penjabaran Rumus Jarak D(x) Per Produk ke Centroid</h5>
        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIterasi" aria-expanded="false" aria-controls="collapseIterasi">
            📁 Tampilkan / Sembunyikan Penjabaran Rumus
        </button>
    </div>
    <div class="collapse" id="collapseIterasi">
        <div id="iterationLog">
            <p class="text-muted">Jalankan K-Means terlebih dahulu.</p>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let data = <?php echo json_encode($data); ?>;
let chartInstance = null;

window.onload = function() {
    loadData();
};

function loadData(){
    let bulan = document.getElementById("bulanSelect").value;
    let capitalBulan = bulan.charAt(0).toUpperCase() + bulan.slice(1);
    document.getElementById("thBulan").innerText = `Penjualan Bulan ${capitalBulan}`;
    document.getElementById("tableTitle").innerText = `📊 Data Produk & Penjualan Bulan ${capitalBulan}`;

    let html = "";
    let points = data.map(d => ({
        nama: d.nama,
        val: d[bulan],
        total: d.total_terjual,
        cluster: null
    }));

    if(data.length === 0) {
        html = `<tr><td colspan="3" class="text-center text-muted">Belum ada data penjualan offline.</td></tr>`;
    } else {
        data.forEach(d=>{
            html += `
            <tr>
                <td>${d.nama}</td>
                <td><b>${d[bulan]}</b></td>
                <td>${d.total_terjual}</td>
            </tr>`;
        });
    }
    document.getElementById("tableBody").innerHTML = html;
    drawChart(points, capitalBulan);
}

function runKMeans(){
    let K = parseInt(document.getElementById("kValue").value);
    let bulan = document.getElementById("bulanSelect").value;
    let capitalBulan = bulan.charAt(0).toUpperCase() + bulan.slice(1);

    let points = data.map(d => ({
        nama: d.nama,
        val: d[bulan],
        total: d.total_terjual,
        cluster: null
    }));

    if(points.length < K){
        alert("Jumlah data produk lebih sedikit dari jumlah cluster (K)!");
        return;
    }

    // Urutkan data berdasarkan nilai penjualan bulan terpilih untuk inisialisasi centroid awal
    points.sort((a, b) => b.val - a.val);

    let centroids = [];
    let step = Math.floor(points.length / K);
    for(let i=0; i<K; i++){
        let idx = Math.min(i * step, points.length - 1);
        centroids.push(points[idx].val); // Nilai tunggal untuk 1 dimensi (1 kolom bulan)
    }

    let clusters = [];
    let changed = true;
    let maxIter = 100;
    let iter = 0;
    let iterationLogs = [];

    while(changed && iter < maxIter){
        changed = false;
        clusters = Array.from({length: K}, () => []);
        
        let previousClusters = points.map(p => p.cluster);
        let currentIterationDetails = [];

        points.forEach(p => {
            let minDist = Infinity;
            let index = 0;
            let distancesToCentroids = [];

            centroids.forEach((c, i) => {
                // Rumus Jarak 1 Dimensi (Mutlak / Akar dari kuadrat selisih)
                let dist = Math.abs(p.val - c);

                distancesToCentroids.push({
                    clusterIndex: i,
                    distance: dist,
                    stepText: `|${p.val} - ${c.toFixed(1)}| = <b>${dist.toFixed(2)}</b>`
                });

                if(dist < minDist){
                    minDist = dist;
                    index = i;
                }
            });

            p.cluster = index;
            clusters[index].push(p);

            currentIterationDetails.push({
                nama: p.nama,
                distances: distancesToCentroids,
                selectedCluster: index
            });
        });

        let currentClusters = points.map(p => p.cluster);
        let isMemberChanged = currentClusters.some((val, idx) => val !== previousClusters[idx]);

        iterationLogs.push({
            iterasi: iter + 1,
            centroids: [...centroids],
            details: currentIterationDetails,
            isChanged: isMemberChanged
        });

        // Hitung Centroid Baru (Rata-rata nilai produk di cluster tersebut)
        let newCentroids = centroids.map((c, i) => {
            if(clusters[i].length > 0){
                let sumVal = clusters[i].reduce((sum, p) => sum + p.val, 0);
                return sumVal / clusters[i].length;
            }
            return c; 
        });

        // Cek perubahan posisi centroid dengan toleransi desimal
        let centroidChanged = newCentroids.some((nc, i) => {
            return nc.toFixed(4) !== centroids[i].toFixed(4);
        });

        centroids = newCentroids;

        if (!isMemberChanged && !centroidChanged) {
            changed = false;
        }

        iter++;
    }

    showResult(clusters, centroids, capitalBulan);
    showIterationLogs(iterationLogs);
    drawChart(points, capitalBulan);
}

function showResult(clusters, centroids, bulanName){
    let html = "";
    
    // Beri label deskriptif otomatis berdasarkan nilai centroid akhir
    let clusterSummary = centroids.map((c, idx) => ({
        index: idx,
        centroidVal: c,
        data: clusters[idx]
    }));

    // Urutkan dari nilai penjualan centroid tertinggi ke terendah
    clusterSummary.sort((a, b) => b.centroidVal - a.centroidVal);

    let labelsName = ["Produk Laris (Tinggi)", "Produk Sedang (Cukup)", "Produk Kurang Laris", "Produk Tidak Laris"];

    clusterSummary.forEach((item, rank) => {
        let clusterName = labelsName[rank] || `Cluster ${rank + 1}`;
        item.data.sort((a, b) => b.val - a.val);
        
        html += `
        <div class="card mb-3 border">
            <div class="card-body">
                <h5 class="text-primary fw-bold">${clusterName} (C${item.index + 1})</h5>
                <p class="text-muted mb-2">
                    <b>Centroid Akhir (${bulanName}):</b> ${item.centroidVal.toFixed(2)}
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Terjual (${bulanName})</th>
                                <th>Total Keseluruhan</th>
                            </tr>
                        </thead>
                        <tbody>`;
        if(item.data.length === 0) {
            html += `<tr><td colspan="3" class="text-center text-muted">Tidak ada produk dalam cluster ini</td></tr>`;
        } else {
            item.data.forEach(p => {
                html += `<tr>
                    <td>${p.nama}</td>
                    <td><b>${p.val}</b></td>
                    <td>${p.total}</td>
                </tr>`;
            });
        }
        html += `</tbody></table></div></div></div>`;
    });
    document.getElementById("result").innerHTML = html;
}

function showIterationLogs(logs) {
    let html = `<p class="text-success fw-bold">✔ Perhitungan selesai dalam ${logs.length} iterasi.</p>`;
    
    logs.forEach(log => {
        html += `
        <div class="border rounded p-3 mb-3 bg-white">
            <h6 class="fw-bold text-dark">Iterasi ke-${log.iterasi}</h6>
            <ul class="small mb-3">`;
        log.centroids.forEach((c, idx) => {
            html += `<li><b>Centroid C${idx+1}</b> = ${c.toFixed(2)}</li>`;
        });
        html += `</ul>`;

        log.details.forEach(det => {
            html += `
            <div class="card p-3 mb-2 bg-light border">
                <p class="fw-bold mb-1 text-primary">Produk: ${det.nama}</p>`;
            
            let minVal = Math.min(...det.distances.map(d => d.distance));

            det.distances.forEach(d => {
                let isChosen = (Math.abs(d.distance - minVal) < 0.0001);
                let badge = isChosen ? `<span class="badge bg-success ms-2">Terpilih (Jarak Terkecil)</span>` : "";
                let borderColor = isChosen ? "border-left: 4px solid #2b8a3e; background: #fff;" : "";
                
                html += `
                <div class="p-2 mb-1 rounded bg-white border" style="${borderColor}">
                    <span class="fw-bold text-dark">Ke Centroid C${d.clusterIndex + 1}:</span><br>
                    <small class="text-muted">Rumus: $D = ${d.stepText}$</small>
                    ${badge}
                </div>`;
            });

            html += `</div>`;
        });

        html += `</div>`;
    });

    document.getElementById("iterationLog").innerHTML = html;
}

function drawChart(points, bulanName){
    if(chartInstance){ chartInstance.destroy(); }
    let labels = points.map(p => p.nama);
    let backgroundColors = points.map(p => {
        if (p.cluster === null) return '#1C7ED6';
        switch(p.cluster){
            case 0: return '#1971c2'; 
            case 1: return '#2b8a3e'; 
            case 2: return '#e67700'; 
            case 3: return '#c92a2a';
            default: return '#845ef7';
        }
    });

    let ctx = document.getElementById("chart").getContext("2d");
    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: `Penjualan ${bulanName}`,
                data: points.map(p => p.val),
                backgroundColor: backgroundColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}
</script>

</body>
</html>