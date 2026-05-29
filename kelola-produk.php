<?php
session_start();
include '../includes/config.php';

// Cek login
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Proses Tambah Produk
if(isset($_POST['tambah'])) {
    $nama = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    
    $query = "INSERT INTO products (nama_produk, deskripsi, harga, kategori) 
              VALUES ('$nama', '$deskripsi', '$harga', '$kategori')";
    mysqli_query($conn, $query);
    header('Location: kelola-produk.php');
    exit;
}

// Proses Hapus Produk
if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header('Location: kelola-produk.php');
    exit;
}

// Proses Edit Produk
if(isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    
    $query = "UPDATE products SET 
              nama_produk = '$nama',
              deskripsi = '$deskripsi',
              harga = '$harga',
              kategori = '$kategori'
              WHERE id = $id";
    mysqli_query($conn, $query);
    header('Location: kelola-produk.php');
    exit;
}

// Ambil semua produk
$products = query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Afia Store</title>
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
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        /* Tombol Tambah */
        .btn-tambah {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
        }
        
        /* Modal Form */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            width: 90%;
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.2);
        }
        
        .modal-content h3 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
        }
        
        .btn-simpan {
            background: #ff6b6b;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }
        
        .close {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }
        
        /* Tabel Produk */
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
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .btn-edit {
            background: #2196F3;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
        }
        
        .btn-hapus {
            background: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .badge {
            background: #ff6b6b;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8rem;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #ff6b6b;
            text-decoration: none;
        }
        
        .search-box {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .search-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .search-box button {
            padding: 10px 20px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2><i class="fas fa-cake-candles"></i> Kelola Produk - Afia Store</h2>
        <div>
            <span>Halo, <?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Kembali</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <!-- Tombol Tambah -->
        <button onclick="bukaModalTambah()" class="btn-tambah">
            <i class="fas fa-plus"></i> Tambah Produk Baru
        </button>
        
        <!-- Search Box -->
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari produk..." onkeyup="cariProduk()">
            <button onclick="cariProduk()"><i class="fas fa-search"></i> Cari</button>
        </div>
        
        <!-- Tabel Produk -->
        <div class="table-container">
            <table id="productTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td>#<?= $p['id'] ?></td>
                        <td><strong><?= $p['nama_produk'] ?></strong></td>
                        <td><?= substr($p['deskripsi'], 0, 50) ?>...</td>
                        <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                        <td><span class="badge"><?= $p['kategori'] ?></span></td>
                        <td>
                            <button onclick="bukaModalEdit(<?= $p['id'] ?>, '<?= $p['nama_produk'] ?>', '<?= $p['deskripsi'] ?>', <?= $p['harga'] ?>, '<?= $p['kategori'] ?>')" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="if(confirm('Yakin hapus produk ini?')) window.location.href='?hapus=<?= $p['id'] ?>'" class="btn-hapus">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- MODAL TAMBAH PRODUK -->
    <div id="modalTambah" class="modal">
        <div class="modal-content">
            <span class="close" onclick="tutupModal('modalTambah')">&times;</span>
            <h3><i class="fas fa-plus-circle"></i> Tambah Produk Baru</h3>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" required>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" required>
                </div>
                
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori">
                        <option value="Best Seller">Best Seller</option>
                        <option value="Regular">Regular</option>
                        <option value="New">New</option>
                    </select>
                </div>
                
                <button type="submit" name="tambah" class="btn-simpan">
                    <i class="fas fa-save"></i> Simpan Produk
                </button>
            </form>
        </div>
    </div>
    
    <!-- MODAL EDIT PRODUK -->
    <div id="modalEdit" class="modal">
        <div class="modal-content">
            <span class="close" onclick="tutupModal('modalEdit')">&times;</span>
            <h3><i class="fas fa-edit"></i> Edit Produk</h3>
            
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" id="edit_nama" required>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" id="edit_harga" required>
                </div>
                
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" id="edit_kategori">
                        <option value="Best Seller">Best Seller</option>
                        <option value="Regular">Regular</option>
                        <option value="New">New</option>
                    </select>
                </div>
                
                <button type="submit" name="edit" class="btn-simpan">
                    <i class="fas fa-save"></i> Update Produk
                </button>
            </form>
        </div>
    </div>
    
    <script>
        // Buka modal tambah
        function bukaModalTambah() {
            document.getElementById('modalTambah').style.display = 'block';
        }
        
        // Buka modal edit
        function bukaModalEdit(id, nama, deskripsi, harga, kategori) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_deskripsi').value = deskripsi;
            document.getElementById('edit_harga').value = harga;
            document.getElementById('edit_kategori').value = kategori;
            
            document.getElementById('modalEdit').style.display = 'block';
        }
        
        // Tutup modal
        function tutupModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        
        // Tutup modal jika klik di luar
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        // Fungsi pencarian
        function cariProduk() {
            var input = document.getElementById('searchInput').value.toLowerCase();
            var table = document.getElementById('productTable');
            var rows = table.getElementsByTagName('tr');
            
            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var found = false;
                
                for (var j = 0; j < cells.length - 1; j++) {
                    var cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(input) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html>