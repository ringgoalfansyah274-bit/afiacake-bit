<?php
session_start();
include '../includes/config.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Statistik
$total_produk = query("SELECT COUNT(*) as total FROM products")[0]['total'];
$total_orders = query("SELECT COUNT(*) as total FROM orders")[0]['total'];
$total_pendapatan = query("SELECT SUM(total_amount) as total FROM orders")[0]['total'] ?? 0;

// Orders per bulan
$orders_per_bulan = query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as bulan,
        COUNT(*) as jumlah,
        SUM(total_amount) as total
    FROM orders 
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY bulan DESC
    LIMIT 6
");

// Produk terlaris
$produk_terlaris = query("
    SELECT 
        p.nama_produk,
        COUNT(oi.id) as jumlah_terjual,
        SUM(oi.quantity) as total_qty
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY p.id
    ORDER BY total_qty DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Afia Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .header {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 5px 15px;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .stat-card i {
            font-size: 2rem;
            color: #ff6b6b;
            margin-bottom: 10px;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        th {
            background: #f8f8f8;
            padding: 12px;
            text-align: left;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .btn-cetak {
            background: #ff6b6b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        
        .back-link {
            color: #ff6b6b;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2><i class="fas fa-chart-bar"></i> Laporan Penjualan - Afia Store</h2>
        <div>
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Kembali</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <button onclick="window.print()" class="btn-cetak">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
        
        <!-- Statistik -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-cake-candles"></i>
                <h3><?= $total_produk ?></h3>
                <p>Total Produk</p>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <h3><?= $total_orders ?></h3>
                <p>Total Pesanan</p>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                <p>Total Pendapatan</p>
            </div>
        </div>
        
        <!-- Grafik Penjualan -->
        <div class="chart-container">
            <h3 style="margin-bottom: 20px;">Grafik Penjualan 6 Bulan Terakhir</h3>
            <canvas id="salesChart"></canvas>
        </div>
        
        <!-- Produk Terlaris -->
        <div class="chart-container">
            <h3 style="margin-bottom: 20px;">Produk Terlaris</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($produk_terlaris as $p): ?>
                    <tr>
                        <td><?= $p['nama_produk'] ?></td>
                        <td><?= $p['jumlah_terjual'] ?>x pesanan</td>
                        <td><?= $p['total_qty'] ?> pcs</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Data untuk grafik
        const bulan = <?= json_encode(array_column($orders_per_bulan, 'bulan')) ?>;
        const total = <?= json_encode(array_column($orders_per_bulan, 'total')) ?>;
        
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: bulan,
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: total,
                    borderColor: '#ff6b6b',
                    backgroundColor: 'rgba(255,107,107,0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>