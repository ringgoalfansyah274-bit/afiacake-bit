<?php
// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'afia_store';
$port = 3306; // Coba ganti 3307 jika perlu

// Fungsi koneksi dengan retry
function connectWithRetry($host, $username, $password, $database, $port, $maxRetries = 3) {
    for($i = 1; $i <= $maxRetries; $i++) {
        $conn = @mysqli_connect($host, $username, $password, $database, $port);
        
        if($conn) {
            return $conn;
        }
        
        // Tunggu 1 detik sebelum mencoba lagi
        if($i < $maxRetries) {
            sleep(1);
        }
    }
    return false;
}

// Coba koneksi
$conn = connectWithRetry($host, $username, $password, $database, $port);

// Jika gagal, coba tanpa port
if(!$conn) {
    $conn = connectWithRetry($host, $username, $password, $database, null);
}

// Jika masih gagal, tampilkan pesan user-friendly
if(!$conn) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Database Error - Afia Store</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
            }
            .error-box {
                background: white;
                padding: 40px;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 500px;
                text-align: center;
            }
            .error-box h1 {
                color: #dc3545;
                margin-bottom: 20px;
            }
            .error-box p {
                color: #666;
                margin-bottom: 30px;
                line-height: 1.6;
            }
            .steps {
                text-align: left;
                background: #f8f9fa;
                padding: 20px;
                border-radius: 10px;
                margin-bottom: 20px;
            }
            .steps ol {
                margin-left: 20px;
                color: #555;
            }
            .steps li {
                margin-bottom: 10px;
            }
            .btn {
                background: #ff6b6b;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 1rem;
                text-decoration: none;
                display: inline-block;
            }
            .btn:hover {
                background: #ff5252;
            }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>🔌 Database Connection Error</h1>
            <p>Maaf, kami tidak dapat terhubung ke database. Silakan coba langkah berikut:</p>
            
            <div class="steps">
                <ol>
                    <li><strong>Buka XAMPP Control Panel</strong> sebagai Administrator</li>
                    <li><strong>Start MySQL</strong> (klik tombol Start)</li>
                    <li>Jika MySQL sudah Start tapi error, <strong>Stop lalu Start lagi</strong></li>
                    <li>Pastikan tidak ada aplikasi lain yang menggunakan port 3306 (Skype, Docker)</li>
                    <li><strong>Refresh halaman ini</strong> setelah MySQL berjalan</li>
                </ol>
            </div>
            
            <button onclick="location.reload()" class="btn">
                🔄 Refresh Halaman
            </button>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Function query
function query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    
    if(!$result) {
        return [];
    }
    
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Function untuk ambil data kontak
function getKontak() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM kontak WHERE id = 1");
    if($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return [
        'telepon' => '(021) 1234-5678',
        'email' => 'info@afiastore.com',
        'jam_operasional' => 'Senin - Sabtu: 09.00 - 18.00',
        'whatsapp' => '6281234567890',
        'instagram' => 'afia_store',
        'alamat' => 'Jl. Contoh No. 123, Jakarta Selatan'
    ];
}

// Ambil data kontak
$kontak = getKontak();

// Function format rupiah
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Function cek stok
function cekStok($id) {
    global $conn;
    $result = mysqli_query($conn, "SELECT stok FROM products WHERE id = $id");
    if($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        return $data['stok'] ?? 0;
    }
    return 0;
}
?>