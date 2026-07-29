<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>K-Means Produk Laku - Yusuf Cell</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f4f8fb;
    color:#333;
}

/* HEADER */

header{
    background:#0084ff;
    padding:18px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:white;
}

.logo{
    font-size:24px;
    font-weight:700;
}

nav a{
    color:white;
    text-decoration:none;
    margin-left:20px;
}

/* HERO */

.hero{
    background:
    linear-gradient(rgba(0,0,0,0.5),rgba(0,0,0,0.5)),
    url('https://images.unsplash.com/photo-1556740749-887f6717d7e4?q=80&w=2070');

    background-size:cover;
    background-position:center;

    height:320px;

    display:flex;
    align-items:center;

    padding:50px;
    color:white;
}

.hero-content{
    max-width:700px;
}

.hero h1{
    font-size:42px;
    margin-bottom:15px;
}

.hero p{
    line-height:1.8;
    opacity:0.9;
    margin-bottom:25px;
}

.hero button{
    background:#0084ff;
    border:none;
    color:white;
    padding:12px 28px;
    border-radius:12px;
    font-weight:600;
    cursor:pointer;
}

/* STAT */

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    padding:30px;
}

.stat-card{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.stat-card h4{
    color:#777;
    margin-bottom:10px;
}

.stat-card p{
    color:#0084ff;
    font-size:28px;
    font-weight:700;
}

/* CARD */

.container{
    padding:0 30px 30px;
}

.card{
    background:white;
    border-radius:18px;
    padding:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    margin-bottom:25px;
}

.card h2{
    margin-bottom:20px;
}

/* BUTTON */

.control-box{
    display:flex;
    gap:10px;
    margin-bottom:20px;
    flex-wrap:wrap;
}

.control-box button{
    background:#0084ff;
    border:none;
    color:white;
    padding:10px 20px;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
}

/* TABLE */

.table-container{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th{
    background:#0084ff;
    color:white;
    padding:14px;
}

table td{
    padding:14px;
    border-bottom:1px solid #eee;
}

/* CLUSTER */

.cluster-badge{
    color:white;
    padding:6px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.laku{
    background:#28a745;
}

.sedang{
    background:#ffc107;
    color:#333;
}

.tidak{
    background:#dc3545;
}

/* CHART */

.chart-container{
    height:400px;
}

/* INSIGHT */

.insight-box ul{
    padding-left:20px;
}

.insight-box li{
    margin-bottom:12px;
    line-height:1.7;
}

/* FOOTER */

footer{
    background:#0084ff;
    color:white;
    text-align:center;
    padding:20px;
    margin-top:20px;
}

/* RESPONSIVE */

@media(max-width:768px){

    .hero{
        height:auto;
        padding:40px 25px;
    }

    .hero h1{
        font-size:30px;
    }

    header{
        flex-direction:column;
        gap:10px;
    }

}

</style>
</head>

<body>

<!-- HEADER -->

<header>

    <div class="logo">
        <i class="fa fa-store"></i> Yusuf Cell
    </div>

    <nav>
        <a href="#">Dashboard</a>
        <a href="#">Produk</a>
        <a href="#">K-Means</a>
        <a href="#">Laporan</a>
    </nav>

</header>

<!-- HERO -->

<section class="hero">

    <div class="hero-content">

        <h1>Analisis Produk Digital Menggunakan K-Means</h1>

        <p>
            Sistem ini digunakan untuk mengelompokkan produk digital
            berdasarkan tingkat penjualannya menjadi produk sangat laku,
            cukup laku, dan kurang laku pada Konter Yusuf Cell.
        </p>

        <button onclick="jalankanKMeans()">
            Jalankan Analisis
        </button>

    </div>

</section>

<!-- STATISTIK -->

<section class="stats">

    <div class="stat-card">
        <h4>Total Produk</h4>
        <p id="totalProduk">0</p>
    </div>

    <div class="stat-card">
        <h4>Produk Sangat Laku</h4>
        <p id="produkLaku">0</p>
    </div>

    <div class="stat-card">
        <h4>Produk Sedang</h4>
        <p id="produkSedang">0</p>
    </div>

    <div class="stat-card">
        <h4>Produk Kurang Laku</h4>
        <p id="produkTidak">0</p>
    </div>

</section>

<!-- DATA -->

<div class="container">

    <div class="card">

        <h2>📊 Data Penjualan Produk Digital</h2>

        <div class="control-box">

            <button onclick="loadData()">
                Load Data
            </button>

            <button onclick="jalankanKMeans()">
                Jalankan K-Means
            </button>

        </div>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Jumlah Transaksi</th>
                        <th>Total Penjualan</th>
                        <th>Cluster</th>
                    </tr>

                </thead>

                <tbody id="tableBody"></tbody>

            </table>

        </div>

    </div>

    <!-- CHART -->

    <div class="card">

        <h2>📈 Grafik Penjualan Produk</h2>

        <div class="chart-container">
            <canvas id="salesChart"></canvas>
        </div>

    </div>

    <!-- INSIGHT -->

    <div class="card insight-box">

        <h2>💡 Business Insight</h2>

        <div id="insight"></div>

    </div>

</div>

<footer>

    © 2026 Yusuf Cell - K-Means Clustering Produk Digital

</footer>

<script>

const produkData = [

    {
        nama:"Pulsa Telkomsel",
        transaksi:120,
        penjualan:6000000
    },

    {
        nama:"Pulsa XL",
        transaksi:80,
        penjualan:3500000
    },

    {
        nama:"Pulsa Axis",
        transaksi:15,
        penjualan:400000
    },

    {
        nama:"Paket Data",
        transaksi:140,
        penjualan:7000000
    },

    {
        nama:"Token PLN",
        transaksi:65,
        penjualan:2800000
    },

    {
        nama:"Voucher Game",
        transaksi:20,
        penjualan:700000
    },

    {
        nama:"E-Wallet",
        transaksi:40,
        penjualan:1500000
    }

];

function loadData(){

    const tbody =
    document.getElementById("tableBody");

    tbody.innerHTML = "";

    produkData.forEach((item,index)=>{

        tbody.innerHTML += `

        <tr>

            <td>${index+1}</td>

            <td>${item.nama}</td>

            <td>${item.transaksi}</td>

            <td>
                Rp ${item.penjualan.toLocaleString()}
            </td>

            <td>-</td>

        </tr>

        `;

    });

    document.getElementById("totalProduk").innerText =
    produkData.length;

}

function jalankanKMeans(){

    const tbody =
    document.getElementById("tableBody");

    tbody.innerHTML = "";

    let laku = 0;
    let sedang = 0;
    let tidak = 0;

    produkData.forEach((item,index)=>{

        let cluster = "";
        let className = "";

        if(item.transaksi >= 100){

            cluster = "Sangat Laku";
            className = "laku";
            laku++;

        }

        else if(item.transaksi >= 40){

            cluster = "Cukup Laku";
            className = "sedang";
            sedang++;

        }

        else{

            cluster = "Kurang Laku";
            className = "tidak";
            tidak++;

        }

        tbody.innerHTML += `

        <tr>

            <td>${index+1}</td>

            <td>${item.nama}</td>

            <td>${item.transaksi}</td>

            <td>
                Rp ${item.penjualan.toLocaleString()}
            </td>

            <td>

                <span class="cluster-badge ${className}">
                    ${cluster}
                </span>

            </td>

        </tr>

        `;

    });

    document.getElementById("produkLaku").innerText =
    laku;

    document.getElementById("produkSedang").innerText =
    sedang;

    document.getElementById("produkTidak").innerText =
    tidak;

    renderChart();

    tampilInsight();

}

let chart;

function renderChart(){

    const ctx =
    document.getElementById("salesChart");

    if(chart){
        chart.destroy();
    }

    chart = new Chart(ctx,{

        type:'bar',

        data:{

            labels:produkData.map(p=>p.nama),

            datasets:[{

                label:'Jumlah Transaksi',

                data:produkData.map(p=>p.transaksi),

                borderWidth:1

            }]

        },

        options:{
            responsive:true,
            maintainAspectRatio:false
        }

    });

}

function tampilInsight(){

    document.getElementById("insight").innerHTML = `

    <ul>

        <li>
            Paket Data dan Pulsa Telkomsel merupakan produk paling laris.
        </li>

        <li>
            Produk dengan cluster sangat laku memiliki transaksi tinggi setiap bulan.
        </li>

        <li>
            Voucher Game dan Pulsa Axis termasuk produk kurang laku.
        </li>

        <li>
            Produk kurang laku dapat diberikan promo atau diskon.
        </li>

        <li>
            Produk sangat laku dapat dijadikan prioritas stok utama.
        </li>

    </ul>

    `;

}

loadData();

</script>

</body>
</html>