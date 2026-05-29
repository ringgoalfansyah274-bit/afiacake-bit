<?php
session_start();
include 'includes/config.php';

// Inisialisasi session wishlist
if(!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Tambah ke wishlist
if(isset($_GET['action']) && $_GET['action'] == 'add') {
    $id = $_GET['id'];
    if(!in_array($id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $id;
    }
    
    if(isset($_GET['ajax'])) {
        echo json_encode(['success' => true]);
        exit;
    }
    header('Location: wishlist.php');
    exit;
}

// Hapus dari wishlist
if(isset($_GET['remove'])) {
    $key = array_search($_GET['remove'], $_SESSION['wishlist']);
    if($key !== false) {
        unset($_SESSION['wishlist'][$key]);
    }
    header('Location: wishlist.php');
    exit;
}

// Ambil produk wishlist
$wishlist_items = [];
if(!empty($_SESSION['wishlist'])) {
    $ids = implode(',', $_SESSION['wishlist']);
    $wishlist_items = query("SELECT * FROM products WHERE id IN ($ids)");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - Afia Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }
        
        .header {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            padding: 1rem 2rem;
        }
        
        nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 20px;
        }
        
        .nav-menu a {
            color: white;
            text-decoration: none;
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
        
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .wishlist-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .wishlist-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .wishlist-info {
            flex: 1;
        }
        
        .wishlist-info h3 {
            margin-bottom: 5px;
        }
        
        .wishlist-info .price {
            color: #ff6b6b;
            font-weight: bold;
        }
        
        .wishlist-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-add {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .btn-remove {
            color: #dc3545;
            background: none;
            border: none;
            cursor: pointer;
        }
        
        .empty-wishlist {
            text-align: center;
            padding: 60px;
            color: #999;
        }
        
        .empty-wishlist i {
            font-size: 5rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <nav>
            <div class="logo"><h1>🍰 Afia Store</h1></div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="cakes.php">Cakes</a></li>
                <li><a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                <li><a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="container">
        <h1><i class="fas fa-heart" style="color: #ff6b6b;"></i> Wishlist Saya</h1>
        
        <?php if(empty($wishlist_items)): ?>
            <div class="empty-wishlist">
                <i class="far fa-heart"></i>
                <h2>Wishlist masih kosong</h2>
                <p>Tambahkan produk favoritmu ke wishlist</p>
                <a href="cakes.php" style="display: inline-block; margin-top: 20px; padding: 10px 30px; background: #ff6b6b; color: white; text-decoration: none; border-radius: 5px;">
                    Lihat Produk
                </a>
            </div>
        <?php else: ?>
            <div class="wishlist-grid">
                <?php foreach($wishlist_items as $item): ?>
                <div class="wishlist-card">
                    <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="">
                    <div class="wishlist-info">
                        <h3><?= $item['nama_produk'] ?></h3>
                        <p class="price"><?= rupiah($item['harga']) ?></p>
                    </div>
                    <div class="wishlist-actions">
                        <form method="POST" action="keranjang.php">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="btn-add">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </form>
                        <a href="?remove=<?= $item['id'] ?>" class="btn-remove">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>