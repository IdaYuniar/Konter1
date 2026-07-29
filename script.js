let dataPenjualan = [];
let hasilClustering = [];
let chart = null;

class KMeans {
    constructor(data, k) {
        this.data = data.map(d => [d.transaksi, d.nominal / 100000, d.frekuensi]);
        this.k = k;
        this.centroids = [];
        this.clusters = [];
    }

    initializeCentroids() {
        // Randomly initialize centroids
        for (let i = 0; i < this.k; i++) {
            const randomIdx = Math.floor(Math.random() * this.data.length);
            this.centroids.push([...this.data[randomIdx]]);
        }
    }

    calculateDistance(point1, point2) {
        return Math.sqrt(
            Math.pow(point1[0] - point2[0], 2) +
            Math.pow(point1[1] - point2[1], 2) +
            Math.pow(point1[2] - point2[2], 2)
        );
    }

    assignClusters() {
        this.clusters = Array.from({ length: this.k }, () => []);
        
        for (let point of this.data) {
            let distances = this.centroids.map(centroid => 
                this.calculateDistance(point, centroid)
            );
            let clusterIndex = distances.indexOf(Math.min(...distances));
            this.clusters[clusterIndex].push(point);
        }
    }

    updateCentroids() {
        let newCentroids = [];
        
        for (let cluster of this.clusters) {
            if (cluster.length === 0) {
                newCentroids.push(this.centroids[newCentroids.length]);
                continue;
            }
            
            let sum = cluster[0].map((_, i) => 
                cluster.reduce((acc, point) => acc + point[i], 0)
            );
            let avg = sum.map(s => s / cluster.length);
            newCentroids.push(avg);
        }
        
        return newCentroids;
    }

    run(maxIterations = 100) {
        this.initializeCentroids();
        let prevCentroids = null;
        let iteration = 0;

        while (iteration < maxIterations) {
            this.assignClusters();
            let newCentroids = this.updateCentroids();
            
            // Check convergence
            if (JSON.stringify(prevCentroids) === JSON.stringify(newCentroids)) {
                break;
            }
            
            this.centroids = newCentroids;
            prevCentroids = [...newCentroids];
            iteration++;
        }
        
        return this.getResults();
    }

    getResults() {
        let clusterAssignments = [];
        
        for (let i = 0; i < this.data.length; i++) {
            let distances = this.centroids.map(centroid => 
                this.calculateDistance(this.data[i], centroid)
            );
            let clusterId = distances.indexOf(Math.min(...distances));
            clusterAssignments.push(clusterId);
        }
        
        return {
            clusters: this.clusters,
            assignments: clusterAssignments,
            centroids: this.centroids,
            iterations: 10 // Simplified
        };
    }
}

// Load data
async function loadData() {
    try {
        // Load from JSON file
        const response = await fetch('data.json');
        dataPenjualan = await response.json();
        displayData();
    } catch (error) {
        console.error('Error loading data:', error);
        // Fallback data
        dataPenjualan = [
            {id: "C001", transaksi: 45, nominal: 2500000, frekuensi: 12},
            {id: "C002", transaksi: 23, nominal: 1200000, frekuensi: 8}
        ];
        displayData();
    }
}

// Display data table
function displayData() {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';
    
    dataPenjualan.forEach(item => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${item.id}</td>
            <td>${item.transaksi}</td>
            <td>Rp ${item.nominal.toLocaleString()}</td>
            <td>${item.frekuensi}</td>
        `;
    });
}

// Run K-Means
async function runKMeans() {
    const k = parseInt(document.getElementById('kValue').value);
    
    if (dataPenjualan.length === 0) {
        alert('Load data terlebih dahulu!');
        return;
    }

    const kmeans = new KMeans(dataPenjualan, k);
    hasilClustering = kmeans.run();
    
    displayResults();
    createScatterPlot();
    generateInsights();
}

// Display clustering results
function displayResults() {
    const statsDiv = document.getElementById('clusterStats');
    const profilesDiv = document.getElementById('clusterProfiles');
    
    statsDiv.innerHTML = '';
    profilesDiv.innerHTML = '';

    // Cluster statistics
    for (let i = 0; i < hasilClustering.assignments.length; i++) {
        const clusterId = hasilClustering.assignments[i];
        const customer = dataPenjualan[i];
        
        if (i === 0 || hasilClustering.assignments[i-1] !== clusterId) {
            const clusterStats = dataPenjualan
                .filter((_, idx) => hasilClustering.assignments[idx] === clusterId)
                .reduce((acc, cust) => ({
                    count: acc.count + 1,
                    avgTransaksi: acc.avgTransaksi + cust.transaksi,
                    avgNominal: acc.avgNominal + cust.nominal,
                    avgFrekuensi: acc.avgFrekuensi + cust.frekuensi
                }), { count: 0, avgTransaksi: 0, avgNominal: 0, avgFrekuensi: 0 });
            
            const avgTransaksi = clusterStats.avgTransaksi / clusterStats.count;
            const avgNominal = clusterStats.avgNominal / clusterStats.count;
            const avgFrekuensi = clusterStats.avgFrekuensi / clusterStats.count;
            
            statsDiv.innerHTML += `
                <div class="cluster-card cluster-${clusterId}">
                    <h4>Cluster ${clusterId + 1}</h4>
                    <p><strong>${clusterStats.count}</strong> Pelanggan</p>
                    <p>Ø Transaksi: ${Math.round(avgTransaksi)}</p>
                    <p>Ø Nominal: Rp ${Math.round(avgNominal).toLocaleString()}</p>
                    <p>Ø Frekuensi: ${Math.round(avgFrekuensi)}</p>
                </div>
            `;
        }
    }

    // Cluster profiles
    for (let clusterId = 0; clusterId < hasilClustering.centroids.length; clusterId++) {
        const clusterData = dataPenjualan.filter((_, idx) => hasilClustering.assignments[idx] === clusterId);
        const profile = clusterData.length > 0 ? clusterData[0] : {};
        
        profilesDiv.innerHTML += `
            <div class="cluster-profile" style="border-left-color: hsl(${clusterId * 100}, 70%, 50%)">
                <h4>👥 Profil Cluster ${clusterId + 1}</h4>
                <p><strong>Jumlah Pelanggan:</strong> ${clusterData.length}</p>
                <p><strong>Rata-rata Transaksi:</strong> ${Math.round(hasilClustering.centroids[clusterId][0])}</p>
                <p><strong>Rata-rata Nominal:</strong> Rp ${Math.round(hasilClustering.centroids[clusterId][1] * 100000).toLocaleString()}</p>
                <p><strong>Rata-rata Frekuensi:</strong> ${Math.round(hasilClustering.centroids[clusterId][2])} kali/bulan</p>
                <p><em>Contoh Pelanggan: ${clusterData.map(c => c.id).join(', ') || 'Tidak ada'}</em></p>
            </div>
        `;
    }
}

// Create scatter plot
function createScatterPlot() {
    const ctx = document.getElementById('scatterChart').getContext('2d');
    
    if (chart) chart.destroy();
    
    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24', '#f0932b', '#eb4d4b'];
    
    const datasets = hasilClustering.centroids.map((centroid, clusterId) => ({
        label: `Cluster ${clusterId + 1}`,
        data: dataPenjualan
            .map((customer, idx) => ({
                x: customer.transaksi,
                y: customer.nominal / 100000,
                id: customer.id
            }))
            .filter((_, idx) => hasilClustering.assignments[idx] === clusterId),
        backgroundColor: colors[clusterId % colors.length] + '80',
        borderColor: colors[clusterId % colors.length],
        pointRadius: 8,
        pointHoverRadius: 12
    }));

    // Add centroids
    datasets.push({
        label: 'Centroids',
        data: hasilClustering.centroids.map(c => ({
            x: c[0],
            y: c[1]
        })),
        backgroundColor: 'black',
        pointRadius: 15,
        pointStyle: 'rectRot',
        showLine: false
    });

    chart = new Chart(ctx, {
        type: 'scatter',
        data: { datasets },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Scatter Plot: Transaksi vs Nominal Penjualan Pulsa'
                },
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Centroids') {
                                return `Centroid ${context.datasetIndex - hasilClustering.centroids.length}: (${Math.round(context.parsed.x)}, Rp ${Math.round(context.parsed.y * 100000).toLocaleString()})`;
                            }
                            return `${context.dataset.label}: ${context.parsed.x} transaksi, Rp ${(context.parsed.y * 100000).toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Jumlah Transaksi' },
                    grid: { color: '#eee' }
                },
                y: {
                    title: { display: true, text: 'Nominal Penjualan (x100rb)' },
                    grid: { color: '#eee' }
                }
            }
        }
    });
}

// Generate business insights
function generateInsights() {
    const insightsDiv = document.getElementById('insights');
    
    insightsDiv.innerHTML = `
        <div class="insight-card">
            <h4>🎯 Strategi Pemasaran</h4>
            <ul>
                <li><strong>Cluster Premium:</strong> Fokus loyalty program & produk premium</li>
                <li><strong>Cluster Regular:</strong> Promo bundling & diskon volume</li>
                <li><strong>Cluster Casual:</strong> Program aktivasi & voucher first buy</li>
            </ul>
        </div>
        <div class="insight-card">
            <h4>💰 Potensi Revenue</h4>
            <p><strong>Revenue Tertinggi:</strong> Cluster dengan rata-rata Rp ${(Math.max(...hasilClustering.centroids.map(c => c[1] * 100000))).toLocaleString()}/pelanggan</p>
            <p><strong>Total Pelanggan:</strong> ${dataPenjualan.length} orang</p>
        </div>
        <div class="insight-card">
            <h4>📈 Rekomendasi</h4>
            <p>1. Targetkan <strong>upselling</strong> pada cluster aktif</p>
            <p>2. Aktivasi <strong>customer casual</strong> dengan promo</p>
            <p>3. Analisis <strong>churn rate</strong> per cluster</p>
        </div>
    `;
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadData();
});