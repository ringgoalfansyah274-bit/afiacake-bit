<?php
include 'includes/header.php';

// Ambil semua kategori dari database
$categories = query("SELECT * FROM categories ORDER BY nama_kategori");

// Ambil parameter kategori dari URL
$kategori_terpilih = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Ambil parameter pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query produk dengan filter
$query = "SELECT * FROM products WHERE 1=1";

if($kategori_terpilih) {
    $query .= " AND kategori = '$kategori_terpilih'";
}

if($search) {
    $query .= " AND (nama_produk LIKE '%$search%' OR deskripsi LIKE '%$search%')";
}

$query .= " ORDER BY 
            CASE 
                WHEN kategori = 'Best Seller' THEN 1
                ELSE 2
            END, 
            created_at DESC";

$products = query($query);
?>

<style>
    /* ===== VARIABEL WARNA ===== */
    :root {
        --primary: #ff6b6b;
        --primary-dark: #ff5252;
        --primary-light: #ff8e8e;
        --secondary: #4CAF50;
        --secondary-dark: #45a049;
        --text-dark: #333;
        --text-light: #666;
        --text-lighter: #999;
        --bg-light: #f8f9fa;
        --white: #ffffff;
        --shadow: 0 10px 30px rgba(0,0,0,0.05);
        --shadow-hover: 0 20px 40px rgba(255,107,107,0.15);
    }

    /* ===== RESET & DASAR ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: var(--bg-light);
        color: var(--text-dark);
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* ===== HEADER HALAMAN ===== */
    .page-header {
        text-align: center;
        padding: 60px 0 30px;
        background: linear-gradient(135deg, rgba(255,107,107,0.05), rgba(255,142,142,0.05));
        margin-bottom: 40px;
    }

    .page-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 15px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: fadeInUp 0.8s ease;
    }

    .page-subtitle {
        color: var(--text-light);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
        animation: fadeInUp 1s ease;
    }

    /* ===== KATEGORI GRID ===== */
    .categories-section {
        margin-bottom: 40px;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .category-card {
        background: var(--white);
        padding: 25px 20px;
        border-radius: 15px;
        text-align: center;
        text-decoration: none;
        color: var(--text-dark);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .category-card:hover::before {
        opacity: 0.05;
    }

    .category-card.active {
        border-color: var(--primary);
        background: linear-gradient(135deg, #fff, #fff0f0);
    }

    .category-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, rgba(255,107,107,0.1), rgba(255,142,142,0.1));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.8rem;
        color: var(--primary);
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }

    .category-card:hover .category-icon {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: var(--white);
        transform: scale(1.1) rotate(5deg);
    }

    .category-card h3 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
        position: relative;
        z-index: 2;
    }

    .category-card p {
        color: var(--text-light);
        font-size: 0.85rem;
        line-height: 1.4;
        position: relative;
        z-index: 2;
    }

    /* ===== SEARCH SECTION ===== */
    .search-section {
        background: var(--white);
        padding: 30px;
        border-radius: 20px;
        box-shadow: var(--shadow);
        margin-bottom: 30px;
    }

    .search-box {
        display: flex;
        gap: 15px;
        max-width: 700px;
        margin: 0 auto;
    }

    .search-box input {
        flex: 1;
        padding: 15px 20px;
        border: 2px solid #eee;
        border-radius: 12px;
        font-size: 1rem;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(255,107,107,0.1);
    }

    .search-box button {
        padding: 15px 35px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: var(--white);
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-box button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255,107,107,0.3);
    }

    /* ===== FILTER INFO ===== */
    .filter-info {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: var(--white);
        padding: 15px 25px;
        border-radius: 12px;
        margin-bottom: 30px;
        display: inline-flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-info i {
        font-size: 1.2rem;
    }

    .filter-info strong {
        font-weight: 600;
        margin: 0 5px;
    }

    .clear-filter {
        color: var(--white);
        background: rgba(255,255,255,0.2);
        padding: 5px 10px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .clear-filter:hover {
        background: rgba(255,255,255,0.3);
    }

    /* ===== PRODUCT GRID ===== */
    .products-section {
        margin-top: 40px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .section-header h2 {
        font-size: 1.8rem;
        color: var(--text-dark);
    }

    .product-count {
        background: var(--white);
        padding: 8px 20px;
        border-radius: 30px;
        color: var(--text-light);
        font-size: 0.95rem;
        box-shadow: var(--shadow);
    }

    .product-count span {
        color: var(--primary);
        font-weight: 600;
        margin: 0 5px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        margin: 30px 0 50px;
    }

    /* ===== PRODUCT CARD ===== */
    .product-card {
        background: var(--white);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        animation: fadeInUp 0.8s ease;
        animation-fill-mode: both;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-hover);
    }

    /* Delay untuk setiap card */
    <?php foreach(range(0, 11) as $i): ?>
    .product-card:nth-child(<?= $i + 1 ?>) {
        animation-delay: <?= 0.1 + ($i * 0.05) ?>s;
    }
    <?php endforeach; ?>

    /* Product Badge */
    .product-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: var(--white);
        padding: 5px 15px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 2;
        box-shadow: 0 5px 10px rgba(255,107,107,0.2);
    }

    .product-badge.stok-habis {
        background: var(--text-lighter);
    }

    /* Product Image */
    .product-image {
        height: 200px;
        overflow: hidden;
        position: relative;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.1);
    }

    /* Product Info */
    .product-info {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        color: var(--primary);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .product-name {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--text-dark);
        line-height: 1.4;
    }

    .product-description {
        color: var(--text-light);
        font-size: 0.9rem;
        margin-bottom: 15px;
        line-height: 1.5;
        flex: 1;
    }

    .product-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 15px;
    }

    /* Product Meta */
    .product-meta {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        padding: 10px 0;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }

    .product-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--text-light);
        font-size: 0.85rem;
    }

    .product-meta i {
        color: var(--primary);
        font-size: 0.9rem;
    }

    /* Add to Cart Button */
    .add-to-cart-form {
        margin-top: auto;
    }

    .btn-add {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: var(--white);
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-add:hover:not(.disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255,107,107,0.3);
    }

    .btn-add i {
        font-size: 1.1rem;
    }

    .btn-add.disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: var(--white);
        border-radius: 20px;
        box-shadow: var(--shadow);
        margin: 40px 0;
    }

    .empty-state i {
        font-size: 5rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 1.8rem;
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--text-light);
        margin-bottom: 25px;
    }

    .btn-back {
        display: inline-block;
        padding: 12px 30px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: var(--white);
        text-decoration: none;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255,107,107,0.3);
    }

    /* ===== VIEW ALL BUTTON ===== */
    .view-all {
        text-align: center;
        margin: 50px 0 20px;
    }

    .btn-view-all {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 15px 40px;
        background: transparent;
        color: var(--primary);
        text-decoration: none;
        border: 2px solid var(--primary);
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-view-all:hover {
        background: var(--primary);
        color: var(--white);
        gap: 15px;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        .product-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .page-title {
            font-size: 2rem;
        }

        .categories-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .search-box {
            flex-direction: column;
        }

        .product-image {
            height: 180px;
        }

        .product-name {
            font-size: 1rem;
        }

        .product-price {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 480px) {
        .product-grid {
            grid-template-columns: 1fr;
        }

        .categories-grid {
            grid-template-columns: 1fr;
        }

        .section-header {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Our Cakes</h1>
        <p class="page-subtitle">Temukan berbagai macam kue lezat untuk momen spesialmu</p>
    </div>
</div>

<div class="container">
    <!-- Categories Grid -->
    <div class="categories-section" data-aos="fade-up">
        <div class="categories-grid">
            <!-- Semua Kue -->
            <a href="cakes.php" class="category-card <?= !$kategori_terpilih ? 'active' : '' ?>">
                <div class="category-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3>Semua Kue</h3>
                <p>Lihat semua koleksi</p>
            </a>
            
            <!-- Kategori Lainnya -->
            <?php foreach($categories as $cat): ?>
            <a href="?kategori=<?= urlencode($cat['nama_kategori']) ?>" 
               class="category-card <?= $kategori_terpilih == $cat['nama_kategori'] ? 'active' : '' ?>">
                <div class="category-icon">
                    <?php
                    $icon = 'fa-cake-candles';
                    if(strpos(strtolower($cat['nama_kategori']), 'birthday') !== false) $icon = 'fa-birthday-cake';
                    if(strpos(strtolower($cat['nama_kategori']), 'wedding') !== false) $icon = 'fa-heart';
                    if(strpos(strtolower($cat['nama_kategori']), 'cupcake') !== false) $icon = 'fa-cookie-bite';
                    if(strpos(strtolower($cat['nama_kategori']), 'custom') !== false) $icon = 'fa-pencil-ruler';
                    ?>
                    <i class="fas <?= $icon ?>"></i>
                </div>
                <h3><?= htmlspecialchars($cat['nama_kategori']) ?></h3>
                <p><?= htmlspecialchars($cat['deskripsi'] ?? 'Kue lezat untuk acara spesial') ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section" data-aos="fade-up">
        <form method="GET" class="search-box">
            <?php if($kategori_terpilih): ?>
                <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori_terpilih) ?>">
            <?php endif; ?>
            <input type="text" 
                   name="search" 
                   placeholder="Cari kue favoritmu..." 
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>
    </div>

    <!-- Filter Info -->
    <?php if($kategori_terpilih || $search): ?>
    <div class="filter-info" data-aos="fade-up">
        <i class="fas fa-filter"></i>
        <span>
            Filter aktif: 
            <?php if($kategori_terpilih): ?>
                Kategori <strong><?= htmlspecialchars($kategori_terpilih) ?></strong>
            <?php endif; ?>
            <?php if($search): ?>
                Pencarian "<strong><?= htmlspecialchars($search) ?></strong>"
            <?php endif; ?>
        </span>
        <a href="cakes.php" class="clear-filter">
            <i class="fas fa-times"></i> Hapus filter
        </a>
    </div>
    <?php endif; ?>

    <!-- Products Section -->
    <div class="products-section">
        <?php if(empty($products)): ?>
            <!-- Empty State -->
            <div class="empty-state" data-aos="fade-up">
                <i class="fas fa-search"></i>
                <h3>Produk Tidak Ditemukan</h3>
                <p>Maaf, tidak ada produk yang sesuai dengan pencarianmu.</p>
                <a href="cakes.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Lihat Semua Produk
                </a>
            </div>
        <?php else: ?>
            <!-- Section Header -->
            <div class="section-header" data-aos="fade-up">
                <h2>
                    <?php if($kategori_terpilih): ?>
                        <?= htmlspecialchars($kategori_terpilih) ?>
                    <?php else: ?>
                        Semua Produk
                    <?php endif; ?>
                </h2>
                <div class="product-count">
                    <i class="fas fa-cake-candles"></i> 
                    <span><?= count($products) ?></span> produk ditemukan
                </div>
            </div>

            <!-- Product Grid -->
            <div class="product-grid">
                <?php foreach($products as $index => $p): ?>
                <div class="product-card">
                    <!-- Badge -->
                    <?php if($p['kategori'] == 'Best Seller'): ?>
                        <div class="product-badge">Best Seller</div>
                    <?php elseif($p['stok'] <= 0): ?>
                        <div class="product-badge stok-habis">Stok Habis</div>
                    <?php endif; ?>
                    
                    <!-- Image -->
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" 
                             alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                    </div>
                    
                    <!-- Info -->
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($p['kategori']) ?></div>
                        <h3 class="product-name"><?= htmlspecialchars($p['nama_produk']) ?></h3>
                        
                        <p class="product-description">
                            <?= htmlspecialchars(substr($p['deskripsi'], 0, 70)) ?>...
                        </p>
                        
                        <div class="product-price">Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>
                        
                        <div class="product-meta">
                            <span>
                                <i class="fas fa-box"></i> Stok: <?= $p['stok'] ?>
                            </span>
                            <span>
                                <i class="fas fa-weight"></i> <?= $p['berat'] ?>gr
                            </span>
                        </div>
                        
                        <?php if($p['stok'] > 0): ?>
                        <form method="POST" action="keranjang.php" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="btn-add">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Tambah ke Keranjang</span>
                            </button>
                        </form>
                        <?php else: ?>
                        <button class="btn-add disabled" disabled>
                            <i class="fas fa-times"></i> Stok Habis
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- View All Button (only if not showing all) -->
            <?php if($kategori_terpilih || $search): ?>
            <div class="view-all">
                <a href="cakes.php" class="btn-view-all">
                    Lihat Semua Produk <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>