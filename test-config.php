<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>TEST KONEKSI AFIA STORE</h2>";

// Test 1: Cek file config
echo "<h3>1. Mencari file config.php...</h3>";
if (file_exists('includes/config.php')) {
    echo "✅ File config.php DITEMUKAN<br>";
    include 'includes/config.php';
} else {
    echo "❌ File config.php TIDAK DITEMUKAN<br>";
    echo "Path: " . realpath('includes') . "<br>";
    exit;
}

// Test 2: Cek koneksi database
echo "<h3>2. Menguji koneksi database...</h3>";
if (isset($conn) && $conn) {
    echo "✅ Koneksi database BERHASIL<br>";
} else {
    echo "❌ Koneksi database GAGAL<br>";
    echo "Error: " . mysqli_connect_error() . "<br>";
    exit;
}

// Test 3: Cek function query
echo "<h3>3. Menguji function query...</h3>";
if (function_exists('query')) {
    echo "✅ Function query() ADA<br>";
    
    $test = query("SELECT 1 as test");
    if ($test) {
        echo "✅ Function query() BERJALAN<br>";
    }
} else {
    echo "❌ Function query() TIDAK ADA<br>";
    exit;
}

// Test 4: Coba ambil produk
echo "<h3>4. Mencoba mengambil data produk...</h3>";
$products = query("SELECT * FROM products");
echo "✅ Ditemukan " . count($products) . " produk<br>";

foreach($products as $p) {
    echo "- " . $p['nama_produk'] . "<br>";
}

echo "<hr>";
echo "<h3 style='color:green'>✅ SEMUA BERES! Tinggal refresh index.php</h3>";
?>