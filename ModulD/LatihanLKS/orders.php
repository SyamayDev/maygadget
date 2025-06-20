<?php
$page_title = "Dashboard Admin";
include "header.php";

// Ambil statistik
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$customer_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$revenue = $pdo->query("SELECT SUM(total_price) FROM orders")->fetchColumn() ?? 0;

// Data untuk grafik 6 bulan terakhir
$months = [];
$monthly_orders = [];

for ($i = 5; $i >= 0; $i--) {
    $month = date('M Y', strtotime("-$i months"));
    $months[] = $month;
    
    $start_date = date('Y-m-01', strtotime("-$i months"));
    $end_date = date('Y-m-t', strtotime("-$i months"));
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE order_date BETWEEN ? AND ?");
    $stmt->execute([$start_date, $end_date]);
    $monthly_orders[] = $stmt->fetchColumn();
}
?>


<!-- Pesanan Terbaru -->
<div class="card">
    <div class="card-header">
        <h2><?php echo $order_count; ?> Pesanan Terbaru</h2>
        <a href="orders.php" class="btn">Lihat Semua <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
                $orders = $stmt->fetchAll();
                
                if (count($orders) > 0):
                    foreach ($orders as $order):
                        $details = json_decode($order['order_details'], true);
                ?>
                <tr>
                    <td><?php echo $order['order_group_id']; ?></td>
                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                    <td><?php echo htmlspecialchars($details['name'] ?? 'N/A'); ?></td>
                    <td>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                    <td>
                        <span class="status-badge <?php echo $order['status'] ?? 'pending'; ?>">
                            <?php 
                            switch ($order['status']) {
                                case 'paid': echo 'Lunas'; break;
                                case 'pending': echo 'Menunggu'; break;
                                default: echo ucfirst($order['status']); 
                            }
                            ?>
                        </span>
                    </td>
                    <td>
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-icon">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6">Belum ada pesanan</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Grafik transaksi
    const ctx = document.getElementById('transactionChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: <?php echo json_encode($monthly_orders); ?>,
                backgroundColor: 'rgba(241, 104, 58, 0.2)',
                borderColor: 'rgba(241, 104, 58, 1)',
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>

<?php include "footer.php"; ?>