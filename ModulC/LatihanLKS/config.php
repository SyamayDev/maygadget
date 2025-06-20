<?php
session_start();

// Koneksi ke database
$host = 'localhost';
$db   = 'lks_smk';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}


// Fungsi helper
function redirect($url) {
    header("Location: $url");
    exit();
}

if (!function_exists('isProductFavorited')) {
    function isProductFavorited($pdo, $user_id, $product_id) {
        if (!$user_id) return false;
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
}

function display_message($text, $type = 'success') {
    $_SESSION['message'] = ['text' => $text, 'type' => $type];
}

function get_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

// Fungsi untuk mengirim pesan WhatsApp
function send_whatsapp_notification($phone, $message) {
    $url = "https://api.whatsapp.com/send?phone=$phone&text=" . urlencode($message);
    return $url;
}
?>