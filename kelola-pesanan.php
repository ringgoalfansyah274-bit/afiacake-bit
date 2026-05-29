<?php
session_start();
include '../includes/config.php';

// Cek login
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// ===== PROSES VERIFIKASI PEMBAYARAN (UNTUK YANG UPLOAD BUKTI) =====
if(isset($_POST['verifikasi'])) {
    $order_id = $_POST['order_id'];
    
    // Update status dari "Menunggu Verifikasi" menjadi "Diproses"
    $query = "UPDATE orders SET status = 'Diproses' WHERE id = $order_id";
    if(mysqli_query($conn, $query)) {
        // Ambil data order untuk notifikasi
        $order = query("SELECT * FROM orders WHERE id = $order_id")[0];
        $wa = $order['customer_phone'];
        $order_number = $order['order_number'];
        
        // Simpan notifikasi sukses
        $_SESSION['success'] = "✅ Pembayaran untuk order #$order_number telah diverifikasi. Pesanan sekarang DIPROSES.";
        
        // Option: Kirim notifikasi WhatsApp ke customer
        // $pesan = "✅ Pembayaran Anda untuk order #$order_number telah diverifikasi. Pesanan akan segera kami proses.";
        // $wa_link = "https://wa.me/$wa?text=" . urlencode($pesan);
        // Bisa redirect ke wa atau dikirim background
    } else {
        $_SESSION['error'] = "Gagal verifikasi: " . mysqli_error($conn);
    }
    header('Location: kelola-pesanan.php');
    exit;
}
// ===== AKHIR PROSES VERIFIKASI =====

// Update status pesanan (untuk status lain)
if(isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $query = "UPDATE orders SET status='$status' WHERE id=$order_id";
    if(mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Status pesanan berhasil diupdate!";
    } else {
        $_SESSION['error'] = "Gagal update status: " . mysqli_error($conn);
    }
    header('Location: kelola-pesanan.php');
    exit;
}

// Hapus pesanan
if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Hapus dulu order_items
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id=$id");
    // Hapus orders
    mysqli_query($conn, "DELETE FROM orders WHERE id=$id");
    
    $_SESSION['success'] = "Pesanan berhasil dihapus!";
    header('Location: kelola-pesanan.php');
    exit;
}

// ===== AMBIL SEMUA ORDERS DENGAN URUTAN PRIORITAS =====
$orders = query("
    SELECT 
        o.*,
        GROUP_CONCAT(CONCAT(p.nama_produk, ' (', oi.quantity, 'x)') SEPARATOR '<br>') as items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    GROUP BY o.id
    ORDER BY 
        CASE o.status
            WHEN 'Menunggu Pembayaran' THEN 1
            WHEN 'Menunggu Verifikasi' THEN 2
            WHEN 'Diproses' THEN 3
            WHEN 'Dikirim' THEN 4
            WHEN 'Selesai' THEN 5
            WHEN 'Dibatalkan' THEN 6
            ELSE 7
        END, 
        o.created_at DESC
");

// Ambil statistik untuk semua status
$total_pesanan = count($orders);
$menunggu_pembayaran = query("SELECT COUNT(*) as total FROM orders WHERE status='Menunggu Pembayaran'")[0]['total'];
$menunggu_verifikasi = query("SELECT COUNT(*) as total FROM orders WHERE status='Menunggu Verifikasi'")[0]['total'];
$diproses = query("SELECT COUNT(*) as total FROM orders WHERE status='Diproses'")[0]['total'];
$dikirim = query("SELECT COUNT(*) as total FROM orders WHERE status='Dikirim'")[0]['total'];
$selesai = query("SELECT COUNT(*) as total FROM orders WHERE status='Selesai'")[0]['total'];
$dibatalkan = query("SELECT COUNT(*) as total FROM orders WHERE status='Dibatalkan'")[0]['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Afia Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h2 {
            font-size: 1.5rem;
        }
        
        .header a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 5px 15px;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
            transition: all 0.3s;
        }
        
        .header a:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(255,107,107,0.2);
        }
        
        .stat-card i {
            font-size: 2rem;
            color: #ff6b6b;
            margin-bottom: 10px;
        }
        
        .stat-card .jumlah {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }
        
        .stat-card.menunggu-pembayaran { border-top: 4px solid #ffc107; }
        .stat-card.menunggu-verifikasi { border-top: 4px solid #17a2b8; }
        .stat-card.diproses { border-top: 4px solid #007bff; }
        .stat-card.dikirim { border-top: 4px solid #fd7e14; }
        .stat-card.selesai { border-top: 4px solid #28a745; }
        
        /* Filter */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-section select, .filter-section input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-width: 200px;
        }
        
        .filter-section button {
            padding: 10px 20px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f8f8f8;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-menunggu-pembayaran {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .status-menunggu-verifikasi {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .status-diproses {
            background: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }
        
        .status-dikirim {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .status-selesai {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-dibatalkan {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .btn-verifikasi {
            background: #28a745;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .btn-verifikasi:hover {
            background: #218838;
        }
        
        .btn-update {
            background: #ff6b6b;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .btn-hapus {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .items-list {
            font-size: 0.9rem;
            color: #666;
        }
        
        .payment-method {
            display: inline-block;
            padding: 3px 8px;
            background: #e9ecef;
            border-radius: 3px;
            font-size: 0.85rem;
        }
        
        .batas-waktu {
            font-size: 0.8rem;
            color: #ff6b6b;
            margin-top: 5px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2><i class="fas fa-shopping-cart"></i> Kelola Pesanan - Afia Store</h2>
        <div>
            <span>Halo, <?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <!-- Alert Messages -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Stats Cards dengan semua status -->
        <div class="stats-grid">
            <div class="stat-card menunggu-pembayaran">
                <i class="fas fa-clock"></i>
                <div class="jumlah"><?= $menunggu_pembayaran ?></div>
                <div>Menunggu Pembayaran</div>
            </div>
            <div class="stat-card menunggu-verifikasi">
                <i class="fas fa-hourglass-half"></i>
                <div class="jumlah"><?= $menunggu_verifikasi ?></div>
                <div>Menunggu Verifikasi</div>
            </div>
            <div class="stat-card diproses">
                <i class="fas fa-cogs"></i>
                <div class="jumlah"><?= $diproses ?></div>
                <div>Diproses</div>
            </div>
            <div class="stat-card dikirim">
                <i class="fas fa-truck"></i>
                <div class="jumlah"><?= $dikirim ?></div>
                <div>Dikirim</div>
            </div>
            <div class="stat-card selesai">
                <i class="fas fa-check-circle"></i>
                <div class="jumlah"><?= $selesai ?></div>
                <div>Selesai</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-ban"></i>
                <div class="jumlah"><?= $dibatalkan ?></div>
                <div>Dibatalkan</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-bag"></i>
                <div class="jumlah"><?= $total_pesanan ?></div>
                <div>Total Pesanan</div>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <select id="filterStatus" onchange="filterTable()">
                <option value="">Semua Status</option>
                <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                <option value="Diproses">Diproses</option>
                <option value="Dikirim">Dikirim</option>
                <option value="Selesai">Selesai</option>
                <option value="Dibatalkan">Dibatalkan</option>
            </select>
            
            <select id="filterPayment" onchange="filterTable()">
                <option value="">Semua Pembayaran</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="Bayar di Tempat (COD)">COD</option>
                <option value="DANA">DANA</option>
                <option value="OVO">OVO</option>
                <option value="GoPay">GoPay</option>
            </select>
            
            <input type="text" id="searchInput" placeholder="Cari nama customer..." onkeyup="filterTable()">
            <button onclick="filterTable()"><i class="fas fa-search"></i> Filter</button>
        </div>
        
        <!-- Tabel Pesanan -->
        <div class="table-container">
            <table id="ordersTable">
                <thead>
                    <tr>
                        <th>No. Order</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Batas Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($orders)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 50px;">
                                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ddd;"></i>
                                <p style="margin-top: 10px; color: #999;">Belum ada pesanan</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($orders as $order): ?>
                        <tr class="order-row" 
                            data-status="<?= $order['status'] ?>" 
                            data-payment="<?= $order['payment_method'] ?>"
                            data-customer="<?= strtolower($order['customer_name']) ?>">
                            
                            <td><strong>#<?= $order['order_number'] ?: $order['id'] ?></strong></td>
                            
                            <td>
                                <?= $order['customer_name'] ?><br>
                                <small style="color: #666;"><?= $order['customer_phone'] ?></small>
                            </td>
                            
                            <td class="items-list">
                                <?= $order['items'] ?: '-' ?>
                            </td>
                            
                            <td>
                                <strong>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong>
                                <?php if($order['diskon'] > 0): ?>
                                    <br><small style="color: #28a745;">Diskon: Rp <?= number_format($order['diskon'], 0, ',', '.') ?></small>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <span class="payment-method">
                                    <?= $order['payment_method'] ?>
                                </span>
                                <?php if(!empty($order['kode_voucher'])): ?>
                                    <br><small>Voucher: <?= $order['kode_voucher'] ?></small>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $order['status'])) ?>">
                                    <?= $order['status'] ?>
                                </span>
                            </td>
                            
                            <td>
                                <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                            </td>
                            
                            <td>
                                <?php if($order['status'] == 'Menunggu Pembayaran' && !empty($order['batas_pembayaran'])): ?>
                                    <span class="batas-waktu">
                                        <i class="fas fa-clock"></i> 
                                        <?= date('d/m/Y H:i', strtotime($order['batas_pembayaran'])) ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            
                            <td style="min-width: 150px;">
                                <!-- TOMBOL VERIFIKASI (khusus status Menunggu Verifikasi) -->
                                <?php if($order['status'] == 'Menunggu Verifikasi'): ?>
                                <form method="POST" style="margin-bottom: 5px;">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" name="verifikasi" class="btn-verifikasi">
                                        <i class="fas fa-check-circle"></i> Verifikasi Pembayaran
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <!-- Form Update Status (untuk status lain) -->
                                <form method="POST" style="margin-bottom: 5px;">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="status" style="width: 100%; margin-bottom: 5px;">
                                        <option value="Menunggu Pembayaran" <?= $order['status']=='Menunggu Pembayaran' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                                        <option value="Menunggu Verifikasi" <?= $order['status']=='Menunggu Verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                                        <option value="Diproses" <?= $order['status']=='Diproses' ? 'selected' : '' ?>>Diproses</option>
                                        <option value="Dikirim" <?= $order['status']=='Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                        <option value="Selesai" <?= $order['status']=='Selesai' ? 'selected' : '' ?>>Selesai</option>
                                        <option value="Dibatalkan" <?= $order['status']=='Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-update" style="width: 100%;">
                                        <i class="fas fa-sync-alt"></i> Update
                                    </button>
                                </form>
                                
                                <!-- Tombol Hapus -->
                                <a href="?hapus=<?= $order['id'] ?>" 
                                   class="btn-hapus" 
                                   style="width: 100%; text-align: center; margin-top: 5px; display: inline-block;"
                                   onclick="return confirm('Yakin ingin menghapus pesanan #<?= $order['order_number'] ?>?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Fungsi Filter Tabel
        function filterTable() {
            var statusFilter = document.getElementById('filterStatus').value.toLowerCase();
            var paymentFilter = document.getElementById('filterPayment').value.toLowerCase();
            var searchFilter = document.getElementById('searchInput').value.toLowerCase();
            
            var rows = document.getElementsByClassName('order-row');
            
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var status = row.getAttribute('data-status').toLowerCase();
                var payment = row.getAttribute('data-payment').toLowerCase();
                var customer = row.getAttribute('data-customer');
                
                var matchStatus = statusFilter === '' || status.includes(statusFilter);
                var matchPayment = paymentFilter === '' || payment.includes(paymentFilter);
                var matchSearch = searchFilter === '' || customer.includes(searchFilter);
                
                if (matchStatus && matchPayment && matchSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>