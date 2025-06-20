<?php
// Di bagian paling atas file, sebelum kode apapun
if (!isset($config_loaded)) {
    include 'config.php';
    $config_loaded = true;
}

// Cek apakah produk sudah difavoritkan oleh user
function isProductFavorited($pdo, $user_id, $product_id) {
    if (!$user_id) return false;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    return $stmt->fetchColumn() > 0;
}

// Inisialisasi keranjang belanja
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ambil semua kategori unik dari database
$stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Ambil produk dari database
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}

// Proses penambahan ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Cari produk di database
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        // Tambahkan ke keranjang
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image']
            ];
        }
        
        // Redirect untuk menghindari resubmit form
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

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
</head>
<body>
<header>
    <div class="container">
        <nav class="navbar">
            <a href="#" class="logo">
                <img src="image/logo.png" alt="LKS SMK 2025 Logo">
            </a>
            <div class="nav-links">
                <a href="index.php" id="active">Home</a>
                <a href="shop.php">Shop</a>
                <a href="categories.php">Categories</a>
                <a href="about.php">About</a>
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

<!-- carousel -->
<div class="carousel">
    <!-- list item -->
    <div class="list">
        <div class="item">
            <img src="image/img1.jpg">
            <div class="content">
                <div class="author" style="margin-top: 30px;">MAYGADGET</div>
                <div class="title">PROMO TERBAIK</div>
                <div class="topic">LAPTOP</div>
                <div class="des">
                    Kami memiliki laptop dengan spesifikasi yang sangat tinggi dan yang pasti harganya sangat murah untuk kantong para pelanggan. Dengan menggunakan laptop ini, Anda dapat melakukan berbagai hal seperti bermain game, menonton film, dan masih banyak lagi.
                </div>
                <div class="buttons">
                    <button><a href="shop.php">JELAJAHI</a></button>
                    <button><a href="cart.php">BELANJA</a></button>
                </div>
            </div>
        </div>
        <div class="item">
            <img src="image/img2.jpg">
            <div class="content">
                <div class="author" style="margin-top: 30px;">MAYGADGET</div>
                <div class="title">PROMO TERBAIK</div>
                <div class="topic">HANDPHONE</div>
                <div class="des">
                    Kami memiliki handphone dengan fitur yang sangat canggih dan yang pasti harganya sangat murah untuk kantong para pelanggan. Dengan menggunakan handphone ini, Anda dapat melakukan berbagai hal seperti bermain game, menonton film, dan masih banyak lagi.
                </div>
                <div class="buttons">
                    <button><a href="shop.php">JELAJAHI</a></button>
                    <button><a href="cart.php">BELANJA</a></button>
                </div>
            </div>
        </div>
        <div class="item">
            <img src="image/img3.jpg">
            <div class="content">
                <div class="author" style="margin-top: 30px;">MAYGADGET</div>
                <div class="title">PROMO TERBAIK</div>
                <div class="topic">EARPHONE</div>
                <div class="des">
                    Kami memiliki earphone dengan kualitas yang sangat tinggi dan yang pasti harganya sangat murah untuk kantong para pelanggan. Dengan menggunakan earphone ini, Anda dapat mendengarkan musik dengan sangat jelas dan nyaman.
                </div>
                <div class="buttons">
                    <button><a href="shop.php">JELAJAHI</a></button>
                    <button><a href="cart.php">BELANJA</a></button>
                </div>
            </div>
        </div>
        <div class="item">
            <img src="image/img4.jpg">
            <div class="content">
                <div class="author" style="margin-top: 30px;">MAYGADGET</div>
                <div class="title">PROMO TERBAIK</div>
                <div class="topic">SMARTWATCH</div>
                <div class="des">
                    Kami memiliki smartwatch dengan fitur yang sangat canggih dan yang pasti harganya sangat murah untuk kantong para pelanggan. Dengan menggunakan smartwatch ini, Anda dapat melakukan berbagai hal seperti berolahraga, mengecek notifikasi, dan masih banyak lagi.
                </div>
                <div class="buttons">
                    <button><a href="shop.php">JELAJAHI</a></button>
                    <button><a href="cart.php">BELANJA</a></button>
                </div>
            </div>
        </div>
    </div>
    <!-- list thumnail -->
    <div class="thumbnail">
        <div class="item">
            <img src="image/img1.jpg">
            <div class="content">
                <div class="title">
                    Laptop
                </div>
                <div class="description">
                    Promo Laptop Gaming
                </div>
            </div>
        </div>
        <div class="item">
            <img src="image/img2.jpg">
            <div class="content">
                <div class="title">
                    HandPhone
                </div>
                <div class="description">
                    Promo HandPhone Termurah
                </div>
            </div>
        </div>
        <div class="item">
            <img src="image/img3.jpg">
            <div class="content">
                <div class="title">
                    Headset
                </div>
                <div class="description">
                    Promo Headset Terbaik
                </div>
            </div>
        </div>
        <div class="item">
            <img src="image/img4.jpg">
            <div class="content">
                <div class="title">
                    SmartWatch
                </div>
                <div class="description">
                    Promo SmartWatch Termurah
                </div>
            </div>
        </div>
    </div>
    <!-- next prev -->
    <div class="arrows">
        <button id="prev"><</button>
        <button id="next">></button>
    </div>
    <!-- time running -->
    <div class="time"></div>
</div>

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

<!-- Product Section -->
<section id="product1" class="section-p1">
    <div class="container">
        <h2>Produk Terbaru</h2>
        <p>Pilihan Terbaik Untuk Anda</p>
        <div class="pro-container">
            <?php foreach ($products as $product): ?>
            <div class="pro">
<div class="favorite-icon" onclick="toggleFavorite(<?php echo $product['id']; ?>, event)">
    <i class="fas fa-heart <?php echo isProductFavorited($pdo, $_SESSION['user_id'] ?? 0, $product['id']) ? 'active' : ''; ?>"></i>
</div>

                <a href="sproduct.html?id=<?php echo $product['id']; ?>">
                    <img src="<?php echo !empty($product['image']) ? $product['image'] : 'laptop.png'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </a>
                <div class="des">
                    <span><?php echo htmlspecialchars($product['category']); ?></span>
                    <a href="sproduct.html?id=<?php echo $product['id']; ?>">
                        <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                    </a>
                    <div class="star">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>Rp. <?php echo number_format($product['price'], 0, ',', '.'); ?></h4>
                </div>
                <a href="#" class="cart" onclick="openOrderModal(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">
                    <i class="fas fa-shopping-cart"></i>
                </a>
            </div>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
                <p style="grid-column: 1 / -1; text-align: center;">Belum ada produk tersedia</p>
            <?php endif; ?>
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

<?php if (!empty($search_results)): ?>
    <section id="searchResults" class="section-p1">
        <div class="container">
            <h2>Hasil Pencarian: "<?php echo htmlspecialchars($_GET['search']); ?>"</h2>
            <div class="pro-container">
                <?php foreach ($search_results as $product): ?>
                    <div class="pro">
                        <a href="sproduct.html?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo !empty($product['image']) ? $product['image'] : 'laptop.png'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        <div class="des">
                            <span><?php echo htmlspecialchars($product['category']); ?></span>
                            <a href="sproduct.html?id=<?php echo $product['id']; ?>">
                                <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                            </a>
                            <div class="star">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h4>Rp. <?php echo number_format($product['price'], 0, ',', '.'); ?></h4>
                        </div>
                        <a href="#" class="cart" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['image']; ?>')">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Modal untuk pemesanan -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalProductName"></h2>
        <p id="modalProductPrice"></p>
        
        <form id="orderForm" method="post">
            <input type="hidden" name="product_id" id="modalProductId">
            <input type="hidden" name="add_to_cart" value="1">
            
            <div class="form-group">
                <label for="quantity">Jumlah</label>
                <input type="number" name="quantity" id="quantity" min="1" value="1" required>
            </div>
            
            <button type="submit" class="btn-order">Tambah ke Keranjang</button>
        </form>
    </div>
</div>

<script>
    // ===== GLOBAL FUNCTIONS =====
    // 1. Define toggleFavorite with async/await
    async function toggleFavorite(productId, heartIcon) {
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
            
            const productId = favoriteIcon.dataset.productId;
            const heartIcon = favoriteIcon.querySelector('i');
            
            // Immediate toggle visual sebelum request selesai
            heartIcon.classList.toggle('active');
            
            // Kirim request
            toggleFavorite(productId, heartIcon);
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

        // Carousel Functionality
        let nextDom = document.getElementById('next');
        let prevDom = document.getElementById('prev');
        let carouselDom = document.querySelector('.carousel');
        let SliderDom = carouselDom.querySelector('.carousel .list');
        let thumbnailBorderDom = document.querySelector('.carousel .thumbnail');
        let thumbnailItemsDom = thumbnailBorderDom.querySelectorAll('.item');
        let timeDom = document.querySelector('.carousel .time');
        
        thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
        
        let timeRunning = 3000;
        let timeAutoNext = 7000;
        let runTimeOut;
        let runNextAuto = setTimeout(() => nextDom.click(), timeAutoNext);
        
        nextDom.onclick = function() { showSlider('next'); }
        prevDom.onclick = function() { showSlider('prev'); }
        
        function showSlider(type) {
            let SliderItemsDom = SliderDom.querySelectorAll('.carousel .list .item');
            let thumbnailItemsDom = document.querySelectorAll('.carousel .thumbnail .item');
            
            if (type === 'next') {
                SliderDom.appendChild(SliderItemsDom[0]);
                thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
                carouselDom.classList.add('next');
            } else {
                SliderDom.prepend(SliderItemsDom[SliderItemsDom.length - 1]);
                thumbnailBorderDom.prepend(thumbnailItemsDom[thumbnailItemsDom.length - 1]);
                carouselDom.classList.add('prev');
            }
            
            clearTimeout(runTimeOut);
            runTimeOut = setTimeout(() => {
                carouselDom.classList.remove('next');
                carouselDom.classList.remove('prev');
            }, timeRunning);
            
            clearTimeout(runNextAuto);
            runNextAuto = setTimeout(() => nextDom.click(), timeAutoNext);
        }
    });

    // ===== OTHER GLOBAL FUNCTIONS =====
    function openOrderModal(id, name, price, image) {
        document.getElementById('modalProductId').value = id;
        document.getElementById('modalProductName').textContent = name;
        document.getElementById('modalProductPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('orderModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('orderModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

</script>
</body>
</html>