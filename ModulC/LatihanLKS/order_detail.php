<?php
if (!isset($_GET['id'])) {
    redirect('orders.php');
}

$order_id = $_GET['id'];
$page_title = "Detail Pesanan";
include "header.php";

// Ambil data pesanan
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    display_message('Pesanan tidak ditemukan', 'error');
    redirect('orders.php');
}

$details = json_decode($order['order_details'], true);
$product = $pdo->query("SELECT * FROM products WHERE id = " . $order['product_id'])->fetch();
?>

<div class="card">
    <div class="card-header">
        <h2>Detail Pesanan #<?php echo $order['order_group_id']; ?></h2>
    </div>
    
    <div class="order-details">
        <div class="detail-section">
            <h3>Informasi Pesanan</h3>
            <div class="detail-row">
                <span class="detail-label">ID Pesanan:</span>
                <span class="detail-value"><?php echo $order['order_group_id']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal:</span>
                <span class="detail-value"><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="status-badge <?php echo $order['status']; ?>">
                        <?php 
                        switch ($order['status']) {
                            case 'pending': 
                                echo '<span class="pending">Menunggu</span>'; 
                                break;
                            case 'selesai': 
                                echo '<span class="selesai">Selesai</span>'; 
                                break;
                            case 'cancel': 
                                echo '<span class="cancel">Dibatalkan</span>'; 
                                break;
                            default: 
                                echo '<span class="default">' . ucfirst($order['status']) . '</span>'; 
                        }
                        ?>
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Metode Pembayaran:</span>
                <span class="detail-value"><?php echo strtoupper($order['payment_method']); ?></span>
            </div>
        </div>
        
        <div class="detail-section">
            <h3>Informasi Pelanggan</h3>
            <div class="detail-row">
                <span class="detail-label">Nama:</span>
                <span class="detail-value"><?php echo htmlspecialchars($details['name'] ?? 'N/A'); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?php echo htmlspecialchars($details['email'] ?? 'N/A'); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Telepon:</span>
                <span class="detail-value"><?php echo htmlspecialchars($details['phone'] ?? 'N/A'); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Alamat:</span>
                <span class="detail-value"><?php echo htmlspecialchars($details['address'] ?? 'N/A'); ?></span>
            </div>
        </div>
        
        <div class="detail-section">
            <h3>Produk yang Dipesan</h3>
            <div class="ordered-product">
                <?php if ($product): ?>
                    <div class="product-image">
                        <img src="<?php 
                        if (!empty($product['image'])) {
                            if (strpos($product['image'], 'http') === 0 || strpos($product['image'], '/') === 0) {
                                echo htmlspecialchars($product['image']);
                            } else {
                                echo '' . htmlspecialchars($product['image']);
                            }
                        } else {
                            echo '../assets/images/no-image.jpg';
                        }
                    ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="product-info">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p><?php echo htmlspecialchars($product['category']); ?></p>
                        <div class="product-meta">
                            <span>Jumlah: <?php echo $order['quantity']; ?></span>
                            <span>Harga: Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Produk sudah dihapus dari sistem</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="detail-section total-section">
            <div class="detail-row">
                <span class="detail-label">Subtotal:</span>
                <span class="detail-value">Rp <?php echo number_format($product ? $product['price'] * $order['quantity'] : $order['total_price'], 0, ',', '.'); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total:</span>
                <span class="detail-value total">Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></span>
            </div>
        </div>
        
        <div class="detail-actions">
            <form action="update_order_status.php" method="post" class="status-form">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <div class="form-group">
                    <label for="status">Ubah Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Menunggu</option>
                        <option value="selesai" <?php echo $order['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="cancel" <?php echo $order['status'] == 'cancel' ? 'selected' : ''; ?>>Dibatalkan</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
            
            <button onclick="confirmDelete('delete_order.php?id=<?php echo $order['id']; ?>')" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus Pesanan
            </button>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>