<?php
session_start();
require_once 'config.php'; // Use your provided config.php

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log to a file for debugging
function log_debug($message) {
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
}

log_debug("Starting process_checkout.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    log_debug("POST request received");
    log_debug("Cart contents: " . json_encode($_SESSION['cart']));

    if (empty($_SESSION['cart'])) {
        log_debug("Cart is empty");
        display_message("Keranjang kosong!", 'error');
        redirect('cart.php');
    }

    // Sanitize input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

    log_debug("Form data: name=$name, email=$email, phone=$phone, address=$address, payment_method=$payment_method");

    // Validate input
    if (!$name || !$email || !$phone || !$address || !$payment_method) {
        log_debug("Validation failed: Missing required fields");
        display_message("Semua field harus diisi!", 'error');
        redirect('cart.php');
    }

    // Validate payment method
    $allowed_methods = ['DANA', 'QRIS', 'Bank BRI', 'Bank BCA', 'Bank Mandiri', 'Bank BNI'];
    if (!in_array($payment_method, $allowed_methods)) {
        log_debug("Invalid payment method: $payment_method");
        display_message("Metode pembayaran tidak valid!", 'error');
        redirect('cart.php');
    }

    // Generate unique order group ID
    $order_group_id = uniqid('ORD_');
    log_debug("Generated order_group_id: $order_group_id");

    // Calculate total
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Prepare order details for JSON
    $order_details = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address
    ];

    try {
        log_debug("Starting database transaction");
        $pdo->beginTransaction();

        // Insert each cart item as a separate order
        foreach ($_SESSION['cart'] as $id => $item) {
            log_debug("Inserting order for product_id: $id");
            $stmt = $pdo->prepare("
                INSERT INTO orders 
                (order_group_id, user_id, product_id, quantity, total_price, payment_method, order_details)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $stmt->execute([
                $order_group_id,
                $user_id,
                $id,
                $item['quantity'],
                $item['price'] * $item['quantity'],
                $payment_method,
                json_encode($order_details)
            ]);
            log_debug("Inserted order for product_id: $id");
        }

        $pdo->commit();
        log_debug("Transaction committed");

        // Prepare WhatsApp message
        $message = "Pesanan Baru (ID: $order_group_id)\n\n";
        $message .= "Nama: $name\n";
        $message .= "Email: $email\n";
        $message .= "Telepon: $phone\n";
        $message .= "Alamat: $address\n";
        $message .= "Metode Pembayaran: $payment_method\n\n";
        $message .= "Detail Pesanan:\n";

        foreach ($_SESSION['cart'] as $item) {
            $message .= "➤ {$item['name']}\n";
            $message .= "   Harga: Rp " . number_format($item['price'], 0, ',', '.') . "\n";
            $message .= "   Jumlah: {$item['quantity']}\n";
            $message .= "   Subtotal: Rp " . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "\n\n";
        }

        $message .= "TOTAL: Rp " . number_format($total, 0, ',', '.') . "\n";
        $message .= "Terima kasih!";

        // Clear cart
        $_SESSION['cart'] = [];
        log_debug("Cart cleared");

        // Set success message
        display_message("Pesanan berhasil diproses! Anda akan diarahkan ke WhatsApp.", 'success');

        // Redirect to WhatsApp
        $whatsapp_url = send_whatsapp_notification('6282267403010', $message);
        log_debug("Redirecting to WhatsApp: $whatsapp_url");
        redirect($whatsapp_url);

    } catch (PDOException $e) {
        $pdo->rollBack();
        log_debug("Database error: " . $e->getMessage());
        display_message("Gagal memproses pesanan: " . $e->getMessage(), 'error');
        redirect('cart.php');
    }
} else {
    log_debug("Invalid request method or no POST data");
    display_message("Akses tidak valid!", 'error');
    redirect('cart.php');
}
?>