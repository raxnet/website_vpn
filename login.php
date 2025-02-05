
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

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token!';
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $remember = isset($_POST['remember']) ? true : false;

        if (empty($username) || empty($password)) {
            $error = 'Username atau password tidak boleh kosong!';
        } else {
            // Logika untuk memeriksa status captcha
            if ($captcha_status == 'aktif' ) {  // Jika status captcha aktif
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
                        loginUser($conn, $username, $password, $remember);
                    }
                } else {
                    $error = 'Captcha harus diisi!';
                }
            } else { // Jika status captcha nonaktif
                loginUser($conn, $username, $password, $remember);
            }
        }
    }
}

// Fungsi untuk login pengguna
function loginUser($conn, $username, $password, $remember) {
    $sql = "SELECT * FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($remember) {
                    setcookie('user_id', $user['id'], time() + (86400 * 30), "/");
                    setcookie('username', $user['username'], time() + (86400 * 30), "/");
                }

                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard");
                } else {
                    header("Location: ../user/dashboard");
                }
                exit();
            } else {
                global $error;
                $error = 'Password salah!';
            }
        } else {
            global $error;
            $error = 'Username tidak ditemukan!';
        }
    } else {
        die('Query failed: ' . $conn->error);
    }
}
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Style seperti sebelumnya */
        
         
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 350px;
            width: 90%;
        }

        .login-container h2 {
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
            padding-left: 30px; /* Menambahkan padding kiri untuk memberi ruang ikon */
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
            z-index: 1; /* Menambahkan z-index agar ikon tetap di depan */
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

        .social-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .social-buttons button {
            background-color: transparent;
            border: none;
            padding: 8px;
            margin: 0 8px;
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
        }

        .social-buttons button:hover {
            transform: scale(1.1);
            opacity: 0.8;
        }

        .social-buttons img {
            width: 24px;
            height: 24px;
        }
    </style>
    
   
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <div class="social-buttons">
            <button><img src="/layout/google.svg" alt="Google"></button>
            <button><img src="/layout/github.svg" alt="GitHub"></button>
            <button><img src="/layout/telegram.svg" alt="Telegram"></button>
        </div>

        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <?php if (!empty($error)): ?>
                <div class="error-message"><?= $error; ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <div class="form-control-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan Username"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <div class="form-control-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Password"
                        required>
                </div>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label class="form-check-label" for="remember">Ingat Saya</label>
            </div>

            <?php if ($captcha_status == 'aktif'): ?>
                <div class="mb-3">
                    <div class="cf-turnstile" data-sitekey="<?= $cloudflare_site_key; ?>"></div>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <div class="footer-links">
            <p>Belum punya akun? <a href="daftar">Daftar Sekarang</a></p>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




