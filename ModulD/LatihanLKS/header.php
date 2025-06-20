<?php
require_once 'config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

// Pesan dari session
$message = get_message();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - LKS SMK 2025</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <!-- Chart.js untuk grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>    

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" width="150px" height="50px" alt="LKS SMK 2025 Logo"></a>
        </div>
        
        <div class="user-info">
            <div class="avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="name"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
            <div class="role">Administrator</div>
        </div>
        
        <ul class="nav-links">
            <li><a href="dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>>
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a></li>
            <li><a href="products.php" <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'class="active"' : ''; ?>>
                <i class="fas fa-box"></i> <span>Produk</span>
            </a></li>
            <li><a href="orders.php" <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'class="active"' : ''; ?>>
                <i class="fas fa-shopping-cart"></i> <span>Pesanan</span>
            </a></li>
        </ul>
        
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1><?php echo $page_title ?? 'Admin Panel'; ?></h1>
            <div class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </div>
            <div class="actions">
                <button class="btn" onclick="window.location.reload()"><i class="fas fa-sync-alt"></i> Refresh</button>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message['type']; ?>"><?php echo $message['text']; ?></div>
        <?php endif; ?>
        