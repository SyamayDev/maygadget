<?php
// Di bagian paling atas file, sebelum kode apapun
if (!isset($config_loaded)) {
    include 'config.php';
    $config_loaded = true;
}

// Inisialisasi keranjang belanja
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ambil semua kategori unik dari database
$stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Proses pencarian
$search_results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $stmt->execute([$search_term, $search_term]);
        $search_results = $stmt->fetchAll();
    } catch (PDOException $e) {
        $search_results = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAYGADGET</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Notification styling */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .notification.error {
            background-color: #f44336;
        }
        
        .notification.fade-out {
            opacity: 0;
            transform: translateY(20px);
        }

        /* About Us Section */
        .about-section {
            padding: 60px 0;
            background-color: rgba(0, 0, 0, 0.9);
        }
        .about-section .container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }
        .about-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #f1683a;
        }
        .about-section p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: white;
            margin-bottom: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .about-section .mission-vision {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 40px;
        }
        .about-section .mission-vision .box {
            flex: 1;
            min-width: 250px;
            padding: 20px;
            color: white;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease;
        }
        .about-section .mission-vision .box:hover {
            transform: translateY(-5px);
        }
        .about-section .mission-vision h3 {
            font-size: 1.5rem;
            color: #f1683a;
            margin-bottom: 10px;
        }
        .about-section .mission-vision p {
            font-size: 1rem;
            color: white;
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <nav class="navbar">
            <a href="#" class="logo">
                <img src="image/logo.png" alt="LKS SMK 2025 Logo">
            </a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="shop.php">Shop</a>
                <a href="categories.php">Categories</a>
                <a href="about.php" id="active">About</a>
                <a href="contact.php">Contact</a>
            </div>
            <div class="icons">
                <div class="search-form" id="searchForm">
                    <form action="shop.php" method="GET">
                        <input type="text" name="search" placeholder="Cari produk..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <i class="fas fa-search" id="searchToggle"></i>
                <a href="login.php"><i class="fas fa-user"></i></a>
                <a href="favorites.php"><i class="fas fa-heart"></i></a>
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-bag position-relative">
                        <span class="badge" id="cartCount">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    </i>
                </a>
                <div class="hamburger" id="hamburger">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </nav>
    </div>
</header>

<!-- Mobile Sidebar -->
<div class="mobile-sidebar" id="mobileSidebar">
    <span class="close-sidebar" id="closeSidebar">
        <i class="fas fa-times"></i>
    </span>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="categories.php">Categories</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
    </div>
</div>

<!-- About Us Section -->
<section class="about-section">
    <div class="container">
        <h2>Tentang Kami</h2>
        <p>Selamat datang di MAYGADGET, destinasi utama Anda untuk teknologi inovatif dan terjangkau! Kami adalah toko online yang berdedikasi untuk menghadirkan produk elektronik terbaik, mulai dari laptop, handphone, earphone, hingga smartwatch, dengan kualitas premium dan harga yang ramah di kantong. Didirikan dengan semangat untuk memenuhi kebutuhan teknologi modern, kami berkomitmen untuk memberikan pengalaman belanja yang mudah, aman, dan menyenangkan bagi setiap pelanggan.</p>
        <p>Di MAYGADGET, kami percaya bahwa teknologi harus dapat diakses oleh semua orang. Oleh karena itu, kami bekerja keras untuk menawarkan produk dengan spesifikasi tinggi, desain modern, dan layanan pelanggan yang luar biasa. Dengan dukungan tim profesional dan layanan purna jual 24/7, kami siap membantu Anda menemukan gadget impian yang sesuai dengan gaya hidup Anda.</p>
        <div class="mission-vision">
            <div class="box">
                <h3>Misi Kami</h3>
                <p>Menyediakan produk teknologi berkualitas tinggi dengan harga terjangkau, memberikan kemudahan akses bagi semua kalangan untuk menikmati inovasi terkini.</p>
            </div>
            <div class="box">
                <h3>Visi Kami</h3>
                <p>Menjadi toko online terpercaya di Indonesia yang menginspirasi dan memberdayakan pelanggan melalui teknologi canggih dan pelayanan terbaik.</p>
            </div>
            <div class="box">
                <h3>Nilai Kami</h3>
                <p>Integritas, inovasi, dan kepuasan pelanggan adalah inti dari setiap langkah kami. Kami berkomitmen untuk memberikan pengalaman belanja yang transparan dan memuaskan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="box">
            <i class="fas fa-truck"></i>
            <h5>Gratis Pengiriman</h5>
            <p>Minimal pembelian Rp500.000</p>
        </div>
        <div class="box">
            <i class="fas fa-shield-alt"></i>
            <h5>Pembayaran Aman</h5>
            <p>Cicilan hingga 12 bulan</p>
        </div>
        <div class="box">
            <i class="fas fa-sync-alt"></i>
            <h5>Garansi 14 Hari</h5>
            <p>Uang kembali jika tidak puas</p>
        </div>
        <div class="box">
            <i class="fas fa-headset"></i>
            <h5>Dukungan 24/7</h5>
            <p>Layanan pelanggan siap membantu</p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col">
                <h5>EKSTRA</h5>
                <a href="#">Merek</a>
                <a href="#">Voucher Hadiah</a>
                <a href="#">Afiliasi</a>
                <a href="#">Penawaran Spesial</a>
                <a href="#">Peta Situs</a>
            </div>
            <div class="col">
                <h5>INFORMASI</h5>
                <a href="#">Tentang Kami</a>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat & Ketentuan</a>
                <a href="#">Hubungi Kami</a>
                <a href="#">Peta Situs</a>
            </div>
            <div class="col">
                <h5>AKUN SAYA</h5>
                <a href="#">Akun Saya</a>
                <a href="#">Riwayat Pesanan</a>
                <a href="#">Daftar Keinginan</a>
                <a href="#">Newsletter</a>
                <a href="#">Pengembalian</a>
            </div>
            <div class="col">
                <h5>HUBUNGI KAMI</h5>
                <p><i class="fas fa-map-marker-alt"></i> MAYGADGET, Jl. CintaKasih 20</p>
                <p><i class="fas fa-envelope"></i> maygadget@example.com</p>
                <p><i class="fas fa-phone"></i> +6282267403010</p>
                <p><i class="fas fa-globe"></i> Jakarta, Indonesia</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 MAYGADGET. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</footer>

<script>
    // ===== GLOBAL FUNCTIONS =====
    // 1. Define toggleFavorite with async/await
    async function toggleFavorite(productId, event) {
        try {
            const response = await fetch('toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            });
            
            const data = await response.json();
            
            if(data.status === 'error') {
                showNotification(data.message, 'error');
                if(data.message === 'Not logged in') {
                    window.location.href = 'login.php';
                }
                return;
            }
            
            // Immediate visual feedback
            const heartIcon = event.target;
            if(data.status === 'added') {
                heartIcon.classList.add('active');
                showNotification('Produk ditambahkan ke favorit');
            } else {
                heartIcon.classList.remove('active');
                showNotification('Produk dihapus dari favorit');
                
                // Jika di halaman favorites, hapus elemen
                if(window.location.pathname.includes('favorites.php')) {
                    const productElement = heartIcon.closest('.pro');
                    if(productElement) {
                        productElement.style.opacity = '0';
                        setTimeout(() => {
                            productElement.remove();
                            if(document.querySelectorAll('.pro-container .pro').length === 0) {
                                window.location.reload();
                            }
                        }, 300);
                    }
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Gagal mengupdate favorit', 'error');
        }
    }

    // 2. Define showNotification in global scope
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Event delegation untuk handle klik favorit
    document.addEventListener('click', function(event) {
        const favoriteIcon = event.target.closest('.favorite-icon');
        if (favoriteIcon) {
            event.preventDefault();
            event.stopPropagation();
            
            const productId = favoriteIcon.dataset.productId || favoriteIcon.querySelector('i').dataset.productId;
            const heartIcon = favoriteIcon.querySelector('i');
            
            // Immediate toggle visual sebelum request selesai
            heartIcon.classList.toggle('active');
            
            // Kirim request
            toggleFavorite(productId, event);
        }
    });

    // ===== DOM CONTENT LOADED =====
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Sidebar Functionality
        document.getElementById('hamburger').addEventListener('click', function() {
            document.getElementById('mobileSidebar').classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        document.getElementById('closeSidebar').addEventListener('click', function() {
            document.getElementById('mobileSidebar').classList.remove('active');
            document.body.style.overflow = 'auto';
        });

        // Search Functionality
        document.getElementById('searchToggle').addEventListener('click', function() {
            const searchForm = document.getElementById('searchForm');
            searchForm.classList.toggle('active');
            if (searchForm.classList.contains('active')) {
                searchForm.querySelector('input').focus();
            }
        });
    });
</script>
</body>
</html>