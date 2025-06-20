<?php
session_start();

// Proses penghapusan item dari keranjang
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit();
}

// Proses update kuantitas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $quantity) {
        if (isset($_SESSION['cart'][$id]) && $quantity > 0) {
            $_SESSION['cart'][$id]['quantity'] = $quantity;
        } elseif (isset($_SESSION['cart'][$id]) && $quantity <= 0) {
            unset($_SESSION['cart'][$id]);
        }
    }
    header("Location: cart.php");
    exit();
}

// Hitung total harga
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - LKS SMK 2025</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-container {
            padding: 80px 0;
            background-color: rgba(0, 0, 0, 0.7);
            min-height: 100vh;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .cart-table th, .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .cart-table th {
            background-color: rgba(255, 255, 255, 0.05);
            color: #f1683a;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .quantity-input {
            width: 60px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            color: white;
            text-align: center;
        }
        
        .remove-btn {
            color: #f1683a;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .remove-btn:hover {
            color: #e05a2e;
            transform: scale(1.1);
        }
        
        .cart-summary {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 10px;
            margin-top: 30px;
            backdrop-filter: blur(10px);
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-continue {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .btn-continue:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .btn-update {
            background: #333;
            color: white;
        }
        
        .btn-update:hover {
            background: #444;
        }
        
        .btn-checkout {
            background: #f1683a;
            color: white;
            width: 100%;
        }
        
        .btn-checkout:hover {
            background: #e05a2e;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart i {
            font-size: 5rem;
            color: #f1683a;
            margin-bottom: 20px;
        }
        
        .empty-cart h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .empty-cart p {
            color: #aaa;
            margin-bottom: 20px;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            backdrop-filter: blur(10px);
            color: white;
            position: relative;
        }
        
        .close-modal {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #f1683a;
        }
        
        .modal-content h3 {
            margin-bottom: 20px;
            color: #f1683a;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 5px;
            color: white;
        }
        
        .form-group select option {
            background: #333;
            color: white;
        }
        
        .modal-content .btn {
            width: 100%;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .cart-table {
                display: block;
                overflow-x: auto;
            }
            
            .cart-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
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

    <section class="cart-container">
        <div class="container">
            <h2>Keranjang Belanja</h2>
            
            <?php if (!empty($_SESSION['cart'])): ?>
            <form method="post" action="cart.php">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <tr>
                            <td>
                                <div class="cart-item">
                                    <img src="<?php echo !empty($item['image']) ? $item['image'] : 'laptop.png'; ?>" alt="<?php echo $item['name']; ?>" class="cart-item-image">
                                    <span><?php echo $item['name']; ?></span>
                                </div>
                            </td>
                            <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                            <td>
                                <input type="number" name="quantity[<?php echo $id; ?>]" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1">
                            </td>
                            <td>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                            <td>
                                <a href="cart.php?remove=<?php echo $id; ?>" class="remove-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-actions">
                    <a href="index.php" class="btn btn-continue">Lanjutkan Belanja</a>
                    <button type="submit" name="update_cart" class="btn btn-update">Perbarui Keranjang</button>
                </div>
                
                <div class="cart-summary">
                    <h3>Ringkasan Belanja</h3>
                    <div style="display: flex; justify-content: space-between; margin-top: 20px; font-size: 1.2rem;">
                        <span>Total:</span>
                        <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <button type="button" class="btn btn-checkout" onclick="showCheckoutModal()">Checkout</button>
                </div>
            </form>
            
            <!-- Checkout Modal -->
            <div id="checkoutModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeCheckoutModal()">&times;</span>
                    <h3>Detail Pembelian</h3>
                    <form id="checkoutForm" method="post" action="process_checkout.php">
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <input type="text" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Alamat Pengiriman</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Metode Pembayaran</label>
                            <select id="payment_method" name="payment_method" required>
                                <option value="">Pilih Metode</option>
                                <option value="DANA">DANA</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Bank BRI">Bank BRI</option>
                                <option value="Bank BCA">Bank BCA</option>
                                <option value="Bank Mandiri">Bank Mandiri</option>
                                <option value="Bank BNI">Bank BNI</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-checkout">Konfirmasi Pesanan</button>
                    </form>
                </div>
            </div>
            
            <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Keranjang Belanja Kosong</h3>
                <p>Silakan tambahkan produk ke keranjang Anda</p>
                <a href="index.php" class="btn btn-continue">Kembali Berbelanja</a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
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

    <script>
    // Sidebar Mobile
    document.getElementById('hamburger').addEventListener('click', function() {
        document.getElementById('mobileSidebar').classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    document.getElementById('closeSidebar').addEventListener('click', function() {
        document.getElementById('mobileSidebar').classList.remove('active');
        document.body.style.overflow = 'auto';
    });

    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('mobileSidebar');
        const hamburger = document.getElementById('hamburger');
        if (sidebar.classList.contains('active')) {
            if (!sidebar.contains(event.target)) {
                if (event.target !== hamburger && !hamburger.contains(event.target)) {
                    sidebar.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            }
        }
    });

    // Search Functionality
    document.getElementById('searchToggle').addEventListener('click', function() {
        const searchForm = document.getElementById('searchForm');
        searchForm.classList.toggle('active');
        
        if (searchForm.classList.contains('active')) {
            searchForm.querySelector('input').focus();
        }
    });

    document.addEventListener('click', function(event) {
        const searchForm = document.getElementById('searchForm');
        const searchToggle = document.getElementById('searchToggle');
        if (searchForm.classList.contains('active')) {
            if (!searchForm.contains(event.target)) {
                if (event.target !== searchToggle && !searchToggle.contains(event.target)) {
                    searchForm.classList.remove('active');
                }
            }
        }
    });

    // Modal Functions
    function showCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('checkoutModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeCheckoutModal();
        }
    });

    // Update cart count
    function updateCartCount() {
        fetch('get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cartCount').textContent = data.count;
        })
        .catch(error => console.error('Error:', error));
    }

    // Notification styles
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

    .notification.error {
        background-color: #e74c3c;
    }

    .notification.success {
        background-color: #2ecc71;
    }

    @keyframes slideIn {
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .fade-out {
        animation: fadeOut 0.3s forwards;
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateY(-50px);
        }
    }
    `;
    document.head.appendChild(style);
    </script>
</body>
</html>