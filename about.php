<?php
include 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Afia Store</title>
    
    <!-- Font Awesome -->
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
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }
        
        /* Header */
        header {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
        }
        
        .nav-menu li {
            margin-left: 30px;
        }
        
        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }
        
        .nav-menu a:hover {
            opacity: 0.9;
        }
        
        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s;
        }
        
        .nav-menu a:hover::after {
            width: 100%;
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        /* Back Home */
        .back-home {
            display: inline-block;
            margin-bottom: 30px;
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .back-home:hover {
            transform: translateX(-5px);
        }
        
        .back-home i {
            margin-right: 5px;
        }
        
        /* Page Title */
        .page-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
            position: relative;
        }
        
        .page-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        /* About Content */
        .about-content {
            background: linear-gradient(135deg, #f9f9f9, #ffffff);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 60px;
        }
        
        .about-content p {
            color: #666;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .about-content h3 {
            color: #ff6b6b;
            margin: 30px 0 15px;
            font-size: 1.5rem;
        }
        
        .about-content ul {
            margin-left: 20px;
            margin-bottom: 20px;
        }
        
        .about-content li {
            color: #666;
            margin-bottom: 10px;
        }
        
        /* Team Section */
        .team-section {
            margin-top: 40px;
        }
        
        .team-section h3 {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .team-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .team-member {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-bottom: 4px solid transparent;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
            border-bottom-color: #ff6b6b;
            box-shadow: 0 15px 30px rgba(255,107,107,0.1);
        }
        
        .team-member strong {
            font-size: 1.2rem;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        
        .team-member span {
            color: #ff6b6b;
            font-size: 0.9rem;
        }
        
        /* Maps Section */
        .maps-section {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .section-title {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .section-title span {
            color: #ff6b6b;
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, #333, #222);
            color: white;
            text-align: center;
            padding: 30px 0;
            margin-top: 80px;
        }
        
        footer p {
            opacity: 0.9;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
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
            
            .about-content {
                padding: 30px;
            }
            
            .team-list {
                grid-template-columns: 1fr;
            }
            
            .page-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <div class="logo">
                <h1>🍰 Afia Store</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="cakes.php">Cakes</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="admin/login.php"><i class="fas fa-user-shield"></i> Admin</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Back to Home -->
        <a href="index.php" class="back-home">
            <i class="fas fa-arrow-left"></i> Kembali ke Home
        </a>
        
        <!-- Page Title -->
        <h1 class="page-title" data-aos="fade-up">Tentang Afia Store</h1>
        
        <!-- About Content -->
        <div class="about-content" data-aos="fade-up">
            <p>Afia Store adalah toko kue online yang menyediakan berbagai macam kue lezat untuk berbagai acara spesial Anda. Kami berdiri sejak tahun 2024 dengan komitmen untuk menghadirkan kue berkualitas dengan rasa yang istimewa.</p>
            
            <h3>Visi Kami</h3>
            <p>Menjadi toko kue online terpercaya yang selalu mengutamakan kepuasan pelanggan.</p>
            
            <h3>Misi Kami</h3>
            <ul>
                <li>Menyediakan kue berkualitas dengan bahan-bahan terbaik</li>
                <li>Memberikan pelayanan yang ramah dan profesional</li>
                <li>Mengutamakan kebersihan dan keamanan produk</li>
                <li>Terus berinovasi dalam menciptakan varian kue baru</li>
            </ul>
            
            <!-- Team Section -->
            <div class="team-section">
                <h3>Tim Peristiwa Penting</h3>
                <div class="team-list">
                    <div class="team-member" data-aos="zoom-in" data-aos-delay="100">
                        <strong>M. Fariz</strong>
                        <span>Project Manager</span>
                    </div>
                    <div class="team-member" data-aos="zoom-in" data-aos-delay="200">
                        <strong>Wida Mulya Ningsih</strong>
                        <span>Frontend Developer</span>
                    </div>
                    <div class="team-member" data-aos="zoom-in" data-aos-delay="300">
                        <strong>Ringgo Alfansyah Aditya</strong>
                        <span>Backend Developer</span>
                    </div>
                    <div class="team-member" data-aos="zoom-in" data-aos-delay="400">
                        <strong>Rendi Arif Firmansyah</strong>
                        <span>Database Administrator</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Location Section (MAPS) -->
        <div class="location-section" data-aos="fade-up">
            <h2 class="section-title">
                <span>Our</span> Location
            </h2>
            <div class="maps-section">
                <?php
                // Ambil koordinat dari database
                $lat = isset($kontak['maps_lat']) ? $kontak['maps_lat'] : '-6.2088';
                $lng = isset($kontak['maps_lng']) ? $kontak['maps_lng'] : '106.8456';
                $zoom = isset($kontak['maps_zoom']) ? $kontak['maps_zoom'] : 15;
                ?>
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126908.13891519684!2d106.77910703787136!3d-6.229411668815479!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945f34dbf%3A0xedbd2dcefa9be408!2sJakarta!5e0!3m2!1sid!2sid!4v1701234567890!5m2!1sid!2sid"
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy"
                    title="Lokasi Afia Store">
                </iframe>
            </div>
            
            <!-- Alamat dari Database -->
            <div style="text-align: center; margin-top: 20px; color: #666;">
                <i class="fas fa-map-marker-alt" style="color: #ff6b6b; margin-right: 5px;"></i>
                <?= isset($kontak['alamat']) ? $kontak['alamat'] : 'Jl. Contoh No. 123, Jakarta Selatan' ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Afia Store. All rights reserved.</p>
        <p style="margin-top: 10px; font-size: 0.9rem;">Created with <i class="fas fa-heart" style="color: #ff6b6b;"></i> by Tim Peristiwa Penting</p>
    </footer>
    
    <!-- AOS Animation Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>