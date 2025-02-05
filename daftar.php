<?php
session_start();

// Redirect jika pengguna sudah login
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard");
    } else {
        header("Location: ../user/dashboard");
    }
    exit();
}

// Include konfigurasi database
include('config/config.php');

$error = '';
$success = '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Ambil site_key, secret_key, dan status dari database
$query = "SELECT site_key, secret_key, status FROM cloudflare_captcha WHERE id = 1 LIMIT 1";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->execute();
    $captcha_data = $stmt->get_result()->fetch_assoc();
    if (!$captcha_data) {
        die("Site key atau secret key Cloudflare tidak ditemukan di database.");
    }
    $cloudflare_site_key = $captcha_data['site_key'];
    $cloudflare_secret_key = $captcha_data['secret_key'];
    $captcha_status = $captcha_data['status']; // Status dari tabel
} else {
    die('Query failed: ' . $conn->error);
}

// Proses pendaftaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token!';
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        $email = trim($_POST['email']);
        $remember = isset($_POST['remember']) ? true : false;

        if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
            $error = 'Semua field harus diisi!';
        } elseif ($password !== $confirm_password) {
            $error = 'Password dan konfirmasi password tidak cocok!';
        } else {
            // Logika untuk memeriksa status captcha
            if ($captcha_status == 'aktif') {  // Jika status captcha aktif
                if (isset($_POST['cf-turnstile-response'])) {
                    $captcha_response = $_POST['cf-turnstile-response'];
                    $url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
                    $data = [
                        'secret' => $cloudflare_secret_key, // Menggunakan secret_key dari database
                        'response' => $captcha_response,
                    ];

                    $options = [
                        'http' => [
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => http_build_query($data),
                        ],
                    ];

                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    $response = json_decode($result);

                    if (!$response->success) {
                        $error = 'Captcha tidak valid!';
                    } else {
                        registerUser($conn, $username, $password, $email, $remember);
                    }
                } else {
                    $error = 'Captcha harus diisi!';
                }
            } else { // Jika status captcha nonaktif
                registerUser($conn, $username, $password, $email, $remember);
            }
        }
    }
}

// Fungsi untuk pendaftaran pengguna
function registerUser($conn, $username, $password, $email, $remember) {
    // Cek apakah username sudah ada
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            global $error;
            $error = 'Username atau email sudah terdaftar!';
        } else {
            // Hash password sebelum disimpan
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert data ke database
            $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sss", $username, $hashed_password, $email);
                if ($stmt->execute()) {
                    global $success;
                    $success = 'Pendaftaran berhasil! Silakan login.';
                    // Redirect setelah pendaftaran berhasil (opsional)
                    header("Location: login");
                    exit();
                } else {
                    global $error;
                    $error = 'Terjadi kesalahan, silakan coba lagi!';
                }
            } else {
                die('Query failed: ' . $conn->error);
            }
        }
    } else {
        die('Query failed: ' . $conn->error);
    }
}
?><!DOCTYPE html><html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran</title>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }.register-container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 350px;
        width: 90%;
    }

    .register-container h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #374151;
        font-weight: 600;
        font-size: 18px;
    }

    .form-group {
        margin-bottom: 15px;
        position: relative;
    }

    .form-label {
        font-size: 14px;
        color: #4b5563;
    }

    .form-control {
        border-radius: 6px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        transition: border-color 0.2s;
        padding-left: 30px;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        outline: none;
    }

    .form-control-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        color: #6b7280;
        z-index: 1;
    }

    .input-group {
        position: relative;
    }

    .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
        border-radius: 6px;
        padding: 12px 20px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .error-message {
        color: #ef4444;
        margin-bottom: 10px;
        text-align: center;
    }

    .success-message {
        color: #10b981;
        margin-bottom: 10px;
        text-align: center;
    }

    .footer-links {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #6b7280;
    }

    .footer-links a {
        color: #3b82f6;
        text-decoration: none;
        transition: color 0.2s;
    }

    .footer-links a:hover {
        color: #2563eb;
        text-decoration: underline;
    }
</style>

</head>
<body>
    <div class="register-container">
        <h2>Pendaftaran</h2><?php if (!empty($error)): ?>
        <div class="error-message"><?= $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="success-message"><?= $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <i class="fas fa-user form-control-icon"></i>
                <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan Username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <i class="fas fa-envelope form-control-icon"></i>
                <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan Email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <i class="fas fa-lock form-control-icon"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan Password" required>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <i class="fas fa-lock form-control-icon"></i>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Konfirmasi Password" required>
            </div>
        </div>

        <!-- Cloudflare Turnstile CAPTCHA -->
        <?php if ($captcha_status == 'aktif'): ?>
            <div class="form-group">
                <label for="captcha" class="form-label">Verifikasi Captcha</label>
                <div class="input-group">
                    <div class="cf-turnstile" data-sitekey="<?= $cloudflare_site_key; ?>"></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="remember" class="form-label">Ingat Saya</label>
            <input type="checkbox" id="remember" name="remember">
        </div>

        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </div>
    </form>

    <div class="footer-links">
        <p>Sudah punya akun? <a href="login">Masuk di sini</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>