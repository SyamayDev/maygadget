<?php
if (!isset($config_loaded)) {
    include 'config.php';
    $config_loaded = true;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Inisialisasi keranjang belanja
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Cek apakah produk sudah difavoritkan oleh user
function isProductFavorited($pdo, $user_id, $product_id) {
    if (!$user_id) return false;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    return $stmt->fetchColumn() > 0;
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

// Get favorite products
$stmt = $pdo->prepare("
    SELECT p.* FROM products p
    JOIN favorites f ON p.id = f.product_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();
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
        .favorite-icon i {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .favorite-icon i.active {
            color: #f00;
        }
        .pro {
            transition: opacity 0.3s ease;
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
                <a href="about.php">About</a>
                <a href="contact.php" id="active">Contact</a>
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

<section class="favorites-container">
    <div class="container">
        <h2 style="margin-top: 80px;">Produk Favorit Saya</h2>
        
        <?php if (!empty($favorites)): ?>
            <div class="pro-container">
                <?php foreach ($favorites as $product): ?>
                <div class="pro">
                    <div class="favorite-icon" data-product-id="<?php echo $product['id']; ?>">
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
                    <a href="#" class="cart" onclick="openOrderModal(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['image']; ?>')">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-favorites">
                <i class="fas fa-heart"></i>
                <h3>Belum ada produk favorit</h3>
                <p>Tambahkan produk ke favorit dengan mengklik ikon hati pada produk</p>
                <a href="index.php" class="btn btn-continue">Jelajahi Produk</a>
            </div>
        <?php endif; ?>
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
                <p><i class="fas fa-map-marker-alt"></i> MAYGADGET, Jl. Bhayangkara No.484</p>
                <p><i class="fas fa-envelope"></i> maygadget@example.com</p>
                <p><i class="fas fa-phone"></i> +6282267403010</p>
                <p><i class="fas fa-globe"></i> Medan, Indonesia</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 MAYGADGET. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</footer>

<!-- Modal untuk pemesanan -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <h2 id="modalProductName"></h2>
        <p id="modalProductPrice"></p>
        
        <form id="orderForm" method="post" action="favorites.php">
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
            // Menghapus notifikasi gagal mengupdate favorit
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
            setTimeout(() => {
                notification.remove();
            }, 300);
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

    // Order Modal Functions
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

    // Add style for notifications
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            background-color: #f1683a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            transform: translateY(100px);
            opacity: 0;
            animation: slideIn 0.3s forwards;
        }
        .notification.error { background-color: #e74c3c; }
        .notification.success { background-color: #2ecc71; }
        @keyframes slideIn { to { transform: translateY(0); opacity: 1; } }
        .fade-out { animation: fadeOut 0.3s forwards; }
        @keyframes fadeOut { to { opacity: 0; transform: translateY(-50px); } }
    `;
    document.head.appendChild(style);
</script>
</body>
</html>