<?php
session_start();
include 'config.php';

// ===== AMBIL DATA KONTAK (DITAMBAHKAN) =====
// Fungsi getKontak sudah ada di config.php
$kontak = getKontak();
// ===== AKHIR AMBIL KONTAK =====

// ===== NOTIFIKASI =====
// Ambil notifikasi jika ada
$notification = '';
$notification_type = '';

if(isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification']['message'];
    $notification_type = $_SESSION['notification']['type'];
    unset($_SESSION['notification']); // Hapus setelah ditampilkan
}
// ===== AKHIR NOTIFIKASI =====
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afia Store - Toko Kue Online</title>
    
    <!-- Font Awesome untuk icon sosial media -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            overflow-x: hidden;
        }
        
        /* NOTIFIKASI STYLE */
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
        
        /* Top Bar - Social Media */
        .top-bar {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            padding: 8px 0;
            font-size: 14px;
        }
        
        .top-bar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .social-links a {
            color: white;
            margin-left: 15px;
            font-size: 18px;
            transition: transform 0.3s;
            display: inline-block;
        }
        
        .social-links a:hover {
            transform: translateY(-3px);
        }
        
        .contact-info i {
            margin-right: 5px;
        }
        
        .contact-info span {
            margin-right: 20px;
        }
        
        /* Main Header */
        .main-header {
            background: white;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .nav-menu li {
            margin-left: 30px;
        }
        
        .nav-menu a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .nav-menu a i {
            font-size: 1rem;
        }
        
        .nav-menu a:hover {
            color: #ff6b6b;
        }
        
        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: #ff6b6b;
            transition: width 0.3s;
        }
        
        .nav-menu a:hover::after {
            width: 100%;
        }
        
        /* Cart Badge */
        .cart-badge {
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            position: relative;
            top: -10px;
            right: 5px;
        }
        
        /* Floating Social Media */
        .floating-social {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
        }
        
        .floating-social a {
            display: block;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            text-align: center;
            line-height: 60px;
            color: white;
            font-size: 30px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s;
            animation: pulse 2s infinite;
        }
        
        .floating-social a:hover {
            transform: scale(1.1) rotate(360deg);
        }
        
        .floating-social .wa {
            background: linear-gradient(135deg, #25D366, #128C7E);
        }
        
        .floating-social .ig {
            background: linear-gradient(135deg, #833AB4, #E1306C, #FCAF45);
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Responsive untuk notifikasi */
        @media (max-width: 768px) {
            .notification {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
                min-width: auto;
            }
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .top-bar .container {
                flex-direction: column;
                gap: 10px;
            }
            
            nav {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-menu li {
                margin: 5px 10px;
            }
        }

        /* ========== ANIMASI LENGKAP AFIA STORE ========== */

/* Animasi dasar untuk semua elemen */
* {
    transition: all 0.3s ease;
}

/* Animasi masuk halaman */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

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

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Animasi untuk body */
body {
    animation: fadeIn 0.8s ease;
}

/* Animasi untuk header dan navigasi */
.top-bar {
    animation: fadeInDown 0.8s ease;
}

.main-header {
    animation: fadeInDown 0.8s ease;
    animation-delay: 0.1s;
    animation-fill-mode: both;
}

/* Animasi untuk hero section */
.hero {
    animation: zoomIn 1s ease;
}

/* Animasi untuk semua judul */
h1, h2, h3, .page-title, .section-title {
    animation: fadeInUp 0.8s ease;
    position: relative;
}

/* Efek garis bawah pada judul */
.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 3px;
    background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
    transition: width 0.5s ease;
}

.section-title:hover::after {
    width: 80px;
}

/* Animasi untuk category cards */
.category-card {
    animation: fadeInUp 0.8s ease;
    animation-fill-mode: both;
    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
}

.category-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(255,107,107,0.15);
}

.category-card:nth-child(1) { animation-delay: 0.1s; }
.category-card:nth-child(2) { animation-delay: 0.2s; }
.category-card:nth-child(3) { animation-delay: 0.3s; }
.category-card:nth-child(4) { animation-delay: 0.4s; }
.category-card:nth-child(5) { animation-delay: 0.5s; }
.category-card:nth-child(6) { animation-delay: 0.6s; }

/* Animasi untuk product cards */
.product-card {
    animation: fadeInUp 0.8s ease;
    animation-fill-mode: both;
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(255,107,107,0.15);
}

/* Delay untuk setiap product card */
.product-card:nth-child(1) { animation-delay: 0.1s; }
.product-card:nth-child(2) { animation-delay: 0.15s; }
.product-card:nth-child(3) { animation-delay: 0.2s; }
.product-card:nth-child(4) { animation-delay: 0.25s; }
.product-card:nth-child(5) { animation-delay: 0.3s; }
.product-card:nth-child(6) { animation-delay: 0.35s; }
.product-card:nth-child(7) { animation-delay: 0.4s; }
.product-card:nth-child(8) { animation-delay: 0.45s; }
.product-card:nth-child(9) { animation-delay: 0.5s; }
.product-card:nth-child(10) { animation-delay: 0.55s; }
.product-card:nth-child(11) { animation-delay: 0.6s; }
.product-card:nth-child(12) { animation-delay: 0.65s; }

/* Animasi untuk gambar produk */
.product-image {
    overflow: hidden;
}

.product-image img {
    transition: transform 0.5s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.1);
}

/* Animasi untuk badge produk */
.product-badge {
    animation: zoomIn 0.5s ease;
}

/* Animasi untuk tombol */
.btn, button, .btn-add, .btn-checkout, .btn-update, .btn-shop {
    animation: fadeInUp 0.8s ease;
    animation-delay: 0.3s;
    animation-fill-mode: both;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn:hover, button:hover, .btn-add:hover, .btn-checkout:hover, 
.btn-update:hover, .btn-shop:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(255,107,107,0.3);
}

/* Efek ripple pada tombol */
.btn::after, button::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    transform: translate(-50%, -50%);
    transition: width 0.5s, height 0.5s;
}

.btn:active::after, button:active::after {
    width: 300px;
    height: 300px;
    opacity: 0;
}

/* Animasi untuk price */
.price, .product-price {
    transition: color 0.3s, transform 0.3s;
}

.product-card:hover .price,
.product-card:hover .product-price {
    color: #ff5252;
    transform: scale(1.05);
}

/* Animasi untuk footer */
footer {
    animation: fadeIn 1s ease;
    animation-delay: 0.5s;
    animation-fill-mode: both;
}

/* Animasi untuk floating social media */
.floating-social a {
    transition: all 0.3s ease;
    animation: pulse 2s infinite;
}

.floating-social a:hover {
    transform: scale(1.1) rotate(360deg);
}

/* Animasi untuk cart badge */
.cart-badge {
    animation: bounce 1s ease infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-3px);
    }
}

/* Animasi untuk notifikasi */
.notification {
    animation: slideInRight 0.5s ease;
}

/* Animasi untuk form input */
input, textarea, select {
    transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s;
}

input:focus, textarea:focus, select:focus {
    transform: translateY(-2px);
    border-color: #ff6b6b;
    box-shadow: 0 5px 15px rgba(255,107,107,0.2);
}

/* Animasi untuk tabel */
.cart-table tr {
    transition: background-color 0.3s;
}

.cart-table tr:hover {
    background-color: #fff5f5;
}

/* Animasi untuk loading */
@keyframes loading {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #ff6b6b;
    border-radius: 50%;
    animation: loading 1s linear infinite;
}

/* Smooth scroll untuk seluruh halaman */
html {
    scroll-behavior: smooth;
}

/* Animasi untuk back to top */
.back-to-top {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 998;
    box-shadow: 0 5px 15px rgba(255,107,107,0.3);
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(255,107,107,0.4);
}

/* Animasi untuk page transition */
.page-transition {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
    z-index: 99999;
    transform: translateX(-100%);
    transition: transform 0.5s ease;
}

.page-transition.active {
    transform: translateX(0);
}
    </style>
</head>
<body>
    <!-- NOTIFIKASI POPUP -->
    <?php if($notification): ?>
    <div id="notification" class="notification notification-<?= $notification_type ?>">
        <div class="notification-content">
            <i class="fas <?= $notification_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <span><?= $notification ?></span>
            <button onclick="tutupNotifikasi()" class="notification-close">&times;</button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Top Bar with Social Media -->
    <div class="top-bar">
        <div class="container">
            <div class="contact-info">
                <i class="fas fa-phone"></i> <span><?= isset($kontak['telepon']) ? $kontak['telepon'] : '(021) 1234-5678' ?></span>
                <i class="fas fa-envelope"></i> <span><?= isset($kontak['email']) ? $kontak['email'] : 'info@afiastore.com' ?></span>
                <i class="fas fa-clock"></i> <span><?= isset($kontak['jam_operasional']) ? $kontak['jam_operasional'] : 'Senin - Sabtu: 09.00 - 18.00' ?></span>
            </div>
            <div class="social-links">
                <a href="https://wa.me/<?= isset($kontak['whatsapp']) ? $kontak['whatsapp'] : '6281234567890' ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                <a href="https://instagram.com/<?= isset($kontak['instagram']) ? $kontak['instagram'] : 'afia_store' ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <div class="main-header">
        <nav>
            <div class="logo">
                <h1>🍰 Afia Store</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="cakes.php"><i class="fas fa-cake-candles"></i> Cakes</a></li>
                <li><a href="blog.php"><i class="fas fa-blog"></i> Blog</a></li>
                <li><a href="faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
                <li><a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                <li><a href="about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
                <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <li><a href="keranjang.php">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <?php 
                    if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                        echo '<span class="cart-badge">' . count($_SESSION['cart']) . '</span>';
                    }
                    ?>
                </a></li>
                <li><a href="admin/login.php"><i class="fas fa-user-shield"></i> Admin</a></li>
            </ul>
        </nav>
    </div>
    
    <!-- Floating Social Media -->
    <div class="floating-social">
        <a href="https://wa.me/<?= isset($kontak['whatsapp']) ? $kontak['whatsapp'] : '6281234567890' ?>?text=Halo%20Afia%20Store%2C%20saya%20mau%20pesan%20kue" 
           target="_blank" 
           class="wa"
           title="Chat WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://instagram.com/<?= isset($kontak['instagram']) ? $kontak['instagram'] : 'afia_store' ?>" 
           target="_blank" 
           class="ig"
           title="Follow Instagram">
            <i class="fab fa-instagram"></i>
        </a>
    </div>

    <!-- SCRIPT NOTIFIKASI -->
    <script>
    // Fungsi untuk menutup notifikasi
    function tutupNotifikasi() {
        var notif = document.getElementById('notification');
        if(notif) {
            notif.style.animation = 'slideOut 0.3s ease';
            setTimeout(function() {
                notif.style.display = 'none';
            }, 300);
        }
    }

    // Notifikasi otomatis hilang setelah 3 detik
    setTimeout(function() {
        var notif = document.getElementById('notification');
        if(notif) {
            notif.style.animation = 'slideOut 0.3s ease';
            setTimeout(function() {
                notif.style.display = 'none';
            }, 300);
        }
    }, 3000);

    // Animasi slide out
    var style = document.createElement('style');
    style.innerHTML = `
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
    </script>