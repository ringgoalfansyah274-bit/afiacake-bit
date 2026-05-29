<?php
session_start();
include 'includes/config.php';

// Inisialisasi keranjang jika belum ada
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Proses TAMBAH KE KERANJANG
if(isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $product_name = '';
    
    // Validasi quantity
    if($quantity < 1) $quantity = 1;
    
    // Cek apakah produk sudah ada di keranjang
    $found = false;
    foreach($_SESSION['cart'] as &$item) {
        if($item['id'] == $product_id) {
            $item['quantity'] += $quantity;
            $found = true;
            $product_name = $item['nama'];
            break;
        }
    }
    
    // Jika belum ada, ambil data produk dari database
    if(!$found) {
        $query = "SELECT * FROM products WHERE id = $product_id";
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) > 0) {
            $product = mysqli_fetch_assoc($result);
            $product_name = $product['nama_produk'];
            
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'nama' => $product['nama_produk'],
                'harga' => $product['harga'],
                'quantity' => $quantity
            ];
        }
    }
    
    // Set notifikasi sukses
    $_SESSION['notification'] = [
        'type' => 'success',
        'message' => "✅ " . $product_name . " berhasil ditambahkan ke keranjang!"
    ];
    
    // Redirect kembali ke halaman sebelumnya
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Proses HAPUS dari keranjang
if(isset($_GET['remove'])) {
    $index = $_GET['remove'];
    if(isset($_SESSION['cart'][$index])) {
        // Ambil nama produk untuk notifikasi
        $product_name = $_SESSION['cart'][$index]['nama'];
        
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
        
        // Set notifikasi sukses hapus
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => "🗑️ " . $product_name . " telah dihapus dari keranjang!"
        ];
    }
    header('Location: keranjang.php');
    exit;
}

// Proses UPDATE quantity
if(isset($_POST['update_cart'])) {
    $updated = false;
    foreach($_POST['quantity'] as $index => $qty) {
        if(isset($_SESSION['cart'][$index])) {
            $old_qty = $_SESSION['cart'][$index]['quantity'];
            $new_qty = (int)$qty;
            if($new_qty < 1) $new_qty = 1;
            
            if($old_qty != $new_qty) {
                $_SESSION['cart'][$index]['quantity'] = $new_qty;
                $updated = true;
            }
        }
    }
    
    if($updated) {
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => "🔄 Jumlah produk berhasil diperbarui!"
        ];
    }
    
    header('Location: keranjang.php');
    exit;
}

// Hitung total
$total = 0;
foreach($_SESSION['cart'] as $item) {
    $total += $item['harga'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Afia Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        
        .header {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            padding: 1rem 2rem;
        }
        
        .header nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        h1 {
            margin-bottom: 30px;
            color: #333;
        }
        
        /* NOTIFIKASI STYLE (tetap ada untuk berjaga-jaga) */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
            max-width: 400px;
            min-width: 300px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        .notification-content {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 4px solid;
        }

        .notification-success .notification-content {
            border-left-color: #4CAF50;
            background: #f0f9f0;
        }

        .notification-success i {
            color: #4CAF50;
            font-size: 1.5rem;
        }

        .notification-error .notification-content {
            border-left-color: #f44336;
            background: #fef0f0;
        }

        .notification-error i {
            color: #f44336;
            font-size: 1.5rem;
        }

        .notification-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
            margin-left: auto;
            padding: 0 5px;
        }

        .notification-close:hover {
            color: #333;
        }
        
        .cart-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .cart-table th {
            background: #f8f8f8;
            padding: 15px;
            text-align: left;
        }
        
        .cart-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .cart-table input[type="number"] {
            width: 70px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .btn-remove {
            color: #dc3545;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            transition: transform 0.2s;
        }
        
        .btn-remove:hover {
            transform: scale(1.2);
            color: #ff0000;
        }
        
        .btn-update {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-update:hover {
            background: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(255,107,107,0.3);
        }
        
        .btn-checkout {
            background: #4CAF50;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: all 0.3s;
            font-weight: bold;
        }
        
        .btn-checkout:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76,175,80,0.3);
        }
        
        .btn-shop {
            display: inline-block;
            padding: 12px 30px;
            background: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .btn-shop:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .total {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff6b6b;
            background: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .empty-cart i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-cart h2 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .empty-cart p {
            color: #666;
            margin-bottom: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .cart-table {
                font-size: 0.9rem;
            }
            
            .cart-table th, 
            .cart-table td {
                padding: 10px;
            }
            
            .cart-table input[type="number"] {
                width: 50px;
            }
            
            .cart-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .total {
                text-align: center;
            }

            .notification {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <nav>
            <div class="logo">
                <h1>🍰 Afia Store</h1>
            </div>
            <div>
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="cakes.php"><i class="fas fa-cake-candles"></i> Cakes</a>
                <a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a>
            </div>
        </nav>
    </div>

    <!-- NOTIFIKASI POPUP - TIDAK PERLU LAGI, SUDAH DI HEADER -->
    
    <div class="container">
        <h1><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h1>
        
        <?php if(empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Keranjang belanja masih kosong</h2>
                <p>Yuk, tambahkan kue favoritmu!</p>
                <a href="cakes.php" class="btn-shop"><i class="fas fa-arrow-right"></i> Mulai Belanja</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['cart'] as $index => $item): 
                            $subtotal = $item['harga'] * $item['quantity'];
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($item['nama']) ?></strong></td>
                            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td>
                                <input type="number" name="quantity[<?= $index ?>]" value="<?= $item['quantity'] ?>" min="1">
                            </td>
                            <td><strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong></td>
                            <td>
                                <a href="?remove=<?= $index ?>" class="btn-remove" onclick="return confirm('Yakin ingin menghapus item ini?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-actions">
                    <button type="submit" name="update_cart" class="btn-update">
                        <i class="fas fa-sync-alt"></i> Update Keranjang
                    </button>
                    <div class="total">
                        Total: Rp <?= number_format($total, 0, ',', '.') ?>
                    </div>
                </div>
            </form>
            
            <div style="text-align: right; margin-top: 30px;">
                <a href="checkout.php" class="btn-checkout">
                    <i class="fas fa-credit-card"></i> Lanjut ke Checkout
                </a>
            </div>

            <!-- Tombol Lanjut Belanja -->
            <div style="text-align: left; margin-top: 20px;">
                <a href="cakes.php" style="color: #ff6b6b; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Lanjut Belanja
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Konfirmasi sebelum hapus
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if(!confirm('Yakin ingin menghapus item ini?')) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>