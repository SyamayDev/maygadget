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

<!-- Bagian Grafik dan Statistik -->
<div class="stats-container">
    <div class="stats">
        <div class="stat-card">
            <div class="icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="title">Total Produk</div>
            <div class="value"><?php echo $product_count; ?></div>
            <div class="change">+5.2% dari bulan lalu</div>
        </div>
        
        <div class="stat-card">
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="title">Total Pesanan</div>
            <div class="value"><?php echo $order_count; ?></div>
            <div class="change">+12.7% dari bulan lalu</div>
        </div>
        
        <div class="stat-card">
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="title">Total Pelanggan</div>
            <div class="value"><?php echo $customer_count; ?></div>
            <div class="change down">-3.4% dari bulan lalu</div>
        </div>
        
        <div class="stat-card">
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="title">Pendapatan</div>
            <div class="value">Rp <?php echo number_format($revenue, 0, ',', '.'); ?></div>
            <div class="change">+8.9% dari bulan lalu</div>
        </div>
    </div>

    <div class="chart-container">
        <h3>Transaksi 6 Bulan Terakhir</h3>
        <canvas id="transactionChart"></canvas>
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