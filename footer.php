    <!-- AOS Animation Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
    
    <style>
        footer {
            background: linear-gradient(135deg, #333, #222);
            color: white;
            padding: 60px 0 30px;
            margin-top: 80px;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }
        
        .footer-col h3 {
            font-size: 20px;
            margin-bottom: 25px;
            position: relative;
            color: #ff6b6b;
        }
        
        .footer-col h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 50px;
            height: 3px;
            background: #ff6b6b;
        }
        
        .footer-col p {
            color: #bbb;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        
        .footer-col ul {
            list-style: none;
        }
        
        .footer-col ul li {
            margin-bottom: 15px;
        }
        
        .footer-col ul li i {
            color: #ff6b6b;
            margin-right: 10px;
            width: 20px;
        }
        
        .footer-col ul li a {
            color: #bbb;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-col ul li a:hover {
            color: #ff6b6b;
            padding-left: 5px;
        }
        
        .footer-social {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .footer-social a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-social a:hover {
            background: #ff6b6b;
            transform: translateY(-5px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #888;
            font-size: 14px;
        }
        
        .footer-bottom a {
            color: #ff6b6b;
            text-decoration: none;
        }
    </style>
    
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h3>Afia Store</h3>
                <p>Menghadirkan kue lezat berkualitas untuk setiap momen spesial Anda. Dibuat dengan cinta dan bahan-bahan pilihan.</p>
                <div class="footer-social">
                    <a href="https://wa.me/<?= $kontak['whatsapp'] ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://instagram.com/<?= $kontak['instagram'] ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                    <li><a href="cakes.php"><i class="fas fa-chevron-right"></i> Cakes</a></li>
                    <li><a href="about.php"><i class="fas fa-chevron-right"></i> About Us</a></li>
                    <li><a href="contact.php"><i class="fas fa-chevron-right"></i> Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Contact Info</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> <?= $kontak['alamat'] ?></li>
                    <li><i class="fas fa-phone"></i> <?= $kontak['telepon'] ?></li>
                    <li><i class="fas fa-envelope"></i> <?= $kontak['email'] ?></li>
                    <li><i class="fas fa-clock"></i> <?= $kontak['jam_operasional'] ?></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Jam Operasional</h3>
                <ul>
                    <li><i class="fas fa-clock"></i> Senin - Jumat: 09.00 - 20.00</li>
                    <li><i class="fas fa-clock"></i> Sabtu: 09.00 - 18.00</li>
                    <li><i class="fas fa-clock"></i> Minggu: 10.00 - 16.00</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 Afia Store. Created with <i class="fas fa-heart" style="color: #ff6b6b;"></i> by <a href="#">Tim Peristiwa Penting</a></p>
        </div>
    </footer>
</body>
</html>