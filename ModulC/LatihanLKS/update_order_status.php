<?php
session_start();
include "config.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Debugging: Tampilkan nilai yang diterima
    error_log("Received order_id: $order_id, status: $status");

    try {
        $allowed_statuses = ['pending', 'selesai', 'cancel'];
        if (!in_array($status, $allowed_statuses)) {
            throw new Exception("Status tidak valid: $status");
        }

        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);

        if ($stmt->rowCount() > 0) {
            header("Location: order_detail.php?id=" . $order_id . "&message=Status pesanan berhasil diperbarui&status=success");
        } else {
            header("Location: order_detail.php?id=" . $order_id . "&message=Pesanan tidak ditemukan atau tidak ada perubahan&status=error");
        }
    } catch (Exception $e) {
        error_log("Error updating status: " . $e->getMessage());
        header("Location: order_detail.php?id=" . $order_id . "&message=Error: " . urlencode($e->getMessage()) . "&status=error");
    }
} else {
    header("Location: orders.php");
    exit();
}
?>