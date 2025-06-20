<?php
include 'config.php';

// Jika user sudah login, redirect berdasarkan role
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user['role'] === 'admin') {
        redirect('dashboard.php');
    } else {
        redirect('index.php');
    }
}

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Simpan role ke session
            display_message('Login berhasil!');
            if ($user['role'] === 'admin') {
                redirect('dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $error = 'Username atau password salah!';
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LKS SMK 2025</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Gaya dasar dari halaman utama */
        :root {
            --primary-color: #f1683a;
            --dark-color: #000;
            --light-color: #eee;
            --gray-color: #333;
            --transition: all 0.3s ease;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --text-shadow: 0 5px 10px rgba(0, 0, 0, 0.4);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-color);
            color: var(--light-color);
            line-height: 1.6;
            overflow-x: hidden;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('image/img1.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 500px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            padding: 40px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(241,104,58,0.1) 0%, rgba(0,0,0,0) 70%);
            z-index: -1;
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--light-color);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo span {
            color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--light-color);
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--light-color);
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 0 3px rgba(241, 104, 58, 0.2);
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }
        
        .btn:hover {
            background: #e05a2e;
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(241, 104, 58, 0.3);
        }
        
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .links a {
            color: #aaa;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        .links a:hover {
            color: var(--primary-color);
        }
        
        .error {
            background: rgba(255, 77, 77, 0.15);
            border-left: 4px solid #ff4d4d;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #ffcccc;
        }
        
        .success {
            background: rgba(76, 175, 80, 0.15);
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #c8e6c9;
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .logo {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">LKS <span>SMK 2025</span></div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
            
            <div class="links">
                <a href="daftar.php">Buat Akun Baru</a>
                <a href="#">Lupa Password?</a>
            </div>
        </form>
    </div>
</body>
</html>