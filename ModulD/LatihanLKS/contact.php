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
    <title>LKS SMK 2025 - Contact Us</title>
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

        /* Contact Details Section */
        #contact-details {
            padding: 80px 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            justify-content: space-between;
        }

        #contact-details .details {
            flex: 1;
            min-width: 300px;
            color: var(--light-color);
        }

        #contact-details .details span {
            color: var(--primary-color);
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            display: block;
            margin-bottom: 10px;
        }

        #contact-details .details h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--light-color);
        }

        #contact-details .details h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        #contact-details .details div li {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            color: #aaa;
        }

        #contact-details .details div li i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        #contact-details .map {
            flex: 1;
            min-width: 300px;
            margin-right: 20px;
        }

        #contact-details .map iframe {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        /* Form Details Section */
        #form-details {
            padding: 80px 0;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            justify-content: space-between;
        }

        #form-details form {
            flex: 1;
            min-width: 300px;
            color: var(--light-color);
        }

        #form-details form span {
            color: var(--primary-color);
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            display: block;
            margin-bottom: 10px;
        }

        #form-details form h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--light-color);
        }

        #form-details form input,
        #form-details form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #333;
            border-radius: 8px;
            color: white;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
        }

        #form-details form textarea {
            resize: vertical;
            min-height: 150px;
        }

        #form-details form button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: var(--transition);
        }

        #form-details form button:hover {
            background: #e05a2e;
        }

        #form-details .people {
            flex: 1;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        #form-details .people div {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            transition: var(--transition);
        }

        #form-details .people div:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
        }

        #form-details .people div img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        #form-details .people div p {
            color: #aaa;
            font-size: 0.9rem;
        }

        #form-details .people div p span {
            color: var(--light-color);
            font-weight: 600;
            font-size: 1rem;
            display: block;
            margin-bottom: 5px;
        }

        #contact-details {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

#contact-details .details {
  width: 40%;
  animation: slideInLeft 0.8s ease-out;
}

#contact-details .details span,
#form-details form span {
  font-size: 12px;
}

#contact-details .details h2,
#form-details form h2 {
  font-size: 26px;
  line-height: 35px;
  padding: 20px 0;
}

#contact-details .details h3 {
  font-size: 16px;
  padding-bottom: 15px;
}

#contact-details .details li {
  list-style: none;
  display: flex;
  padding: 10px 0;
  transition: all 0.3s ease;
}

#contact-details .details li:hover {
  transform: translateX(5px);
}

#contact-details .details li i {
  font-size: 14px;
  padding-right: 22px;
}

#contact-details .details li p {
  margin: 0;
  font-size: 14px;
}

        /* Responsive */
        @media screen and (max-width: 768px) {
            #contact-details,
            #form-details {
                flex-direction: column;
                gap: 20px;
            }

            #contact-details .map iframe {
                height: 300px;
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

<!-- Contact Details Section -->
<section id="contact-details" class="section-p1" style="margin-left: 30px;">
    <div class="details">
        <span>Hubungi Kami</span>
        <h2>Ayo belanja di MAYGADGET sekarang untuk teknologi terbaikmu!</h2>
        <h3>Pusat Toko MAYGADGET</h3>
        <div>
            <li>
                <i class="fas fa-map-marker-alt"></i>
                <p>Jl. Bhayangkara No.484, Indra Kasih, Kec. Medan Tembung</p>
            </li>
            <li>
                <i class="fas fa-envelope"></i>
                <p>maygadget@gmail.com</p>
            </li>
            <li>
                <i class="fas fa-phone-alt"></i>
                <p>082267403010</p>
            </li>
            <li>
                <i class="fas fa-clock"></i>
                <p>09:00 - 22:00, Senin - Minggu</p>
            </li>
        </div>
    </div>
    <div class="map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3981.849369324988!2d98.70179747529592!3d3.621879196352226!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303133d535005dcb%3A0xe681667921ffa12f!2sSekolah%20Menengah%20Kejuruan%20Tritech%20Informatika!5e0!3m2!1sid!2sid!4v1749449187772!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

<!-- Form Details Section -->
<section id="form-details" style="margin-left: 30px;">
    <form action="">
        <span>TINGGALKAN PESAN</span>
        <h2>Kami Siap Membantumu dengan Sepenuh Hati</h2>
        <input type="text" placeholder="Nama Lengkap">
        <input type="email" placeholder="Email">
        <input type="text" placeholder="Subjek">
        <textarea name="" id="" cols="30" rows="10" placeholder="Pesan Kamu"></textarea>
        <button type="submit" class="normal">Kirim</button>
    </form>
    <div class="people" style="margin-top: 45px;">
        <div>
            <img src="image/1.png" alt="Team Member 1">
            <p><span>Syahril May Mubdi</span> Senior Marketing Manager <br>Phone: 082267403010 <br>Email: syahril.may@maygadget.com</p>
        </div>
        <div>
            <img src="image/2.png" alt="Team Member 2">
            <p><span>Yaisy Faturrahman</span> Customer Support <br>Phone: 082267403012 <br>Email: yaisy.faturrahman@maygadget.com</p>
        </div>
        <div>
            <img src="image/3.png" alt="Team Member 3">
            <p><span>Kindi Maulana Nugraha</span> Customer Support <br>Phone: 082267403013 <br>Email: kindi.maulana@maygadget.com</p>
        </div>
                <div>
            <img src="image/4.png" alt="Team Member 3">
            <p><span>Farel Rakha Hanania</span> Customer Support <br>Phone: 082267403013 <br>Email: farel.hanania@maygadget.com</p>
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