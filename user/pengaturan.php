<?php
session_start();
include('../script/autoload.php');
$user_id = $_SESSION['user_id']; 

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $hashed_new_password, $user_id);
                $update_stmt->execute();
                echo "Password berhasil diubah.";
            } else {
                echo "Konfirmasi password tidak cocok.";
            }
        } else {
            echo "Password lama salah.";
        }
    }

    if (isset($_POST['delete_account'])) {
        $delete_password = $_POST['delete_password'];
        if (password_verify($delete_password, $user['password'])) {
            $delete_sql = "DELETE FROM users WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $user_id);
            $delete_stmt->execute();
            session_destroy();
            header("Location: login");
            exit;
        } else {
            echo "Password salah. Akun tidak dapat dihapus.";
        }
    }

    if (isset($_POST['update_telegram'])) {
        $telegram_id = $_POST['telegram_id'];
        $telegram_username = $_POST['telegram_username'];
        $update_telegram_sql = "UPDATE users SET telegram_id = ?, telegram_username = ? WHERE id = ?";
        $update_telegram_stmt = $conn->prepare($update_telegram_sql);
        $update_telegram_stmt->bind_param("ssi", $telegram_id, $telegram_username, $user_id);
        $update_telegram_stmt->execute();
        echo "Akun Telegram berhasil diperbarui.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        html, body { margin: 0; padding: 0; height: 100%; }
        body { background-color: #f9f9f9; color: #333; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { 
            max-width: 800px; 
            margin: 20px auto; 
            padding: 20px; 
            margin-top: 50px; /* Menambahkan jarak dari bagian atas layar */
            background-color: #fff; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        body.dark-mode .container {
            background-color: #34495e;
        }

        .profile-card, .form-section, .delete-account { 
            background-color: #fff; 
            border-radius: 8px; 
            padding: 15px; 
            margin-bottom: 15px; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
        }
        
        body.dark-mode .profile-card  {
            background-color: #2c3e50;
        }
        
        body.dark-mode .form-section {
            background-color: #2c3e50;
        }
        
        body.dark-mode .delete-account {
            background-color: #2c3e50;
        }
        
        body.dark-mode  h3 h2 .p .label {
           color: #ffffff;
        }
        
        

        .profile-card .icon { display: block; font-size: 40px; color: #1da1f2; margin: 0 auto 10px; text-align: center; }
        .profile-card h2 { font-size: 18px; text-align: center; margin: 5px 0; }
        .profile-card p { font-size: 12px; color: #666; text-align: center; }
        .form-section h3, .delete-account h3 { font-size: 14px; margin-bottom: 12px; color: #1da1f2; }
        label { display: block; color: #333; margin-bottom: 5px; font-size: 12px; }
        input[type="password"], input[type="text"], input[type="checkbox"] { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12px; }
        input[type="password"]:focus, input[type="text"]:focus { outline: none; border: 2px solid #1da1f2; background-color: #f4f4f4; }
        .custom-button { background-color: #1da1f2; color: #ffffff; font-weight: bold; cursor: pointer; padding: 8px; border-radius: 5px; text-align: center; border: none; transition: background-color 0.3s ease; display: inline-block; width: 100%; }
        .custom-button:hover { background-color: #007bb5; }
        .delete-account button { background-color: #ff4d4d; color: #ffffff; padding: 8px; border-radius: 5px; border: none; font-weight: bold; cursor: pointer; transition: background-color 0.3s ease; }
        .delete-account button:hover { background-color: #e04343; }
        .custom-icon { font-size: 16px; vertical-align: middle; margin-right: 5px; }
        @media (max-width: 768px) { .container { padding: 10px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <span class="icon"><i class="fas fa-user-circle"></i></span>
            <h2><?= htmlspecialchars($user['username']) ?></h2>
            <p><i class="fas fa-calendar-alt custom-icon"></i> Bergabung pada <?= date('d M Y', strtotime($user['created_at'])) ?></p>
            <p><i class="fas fa-envelope custom-icon"></i>Email: <?= htmlspecialchars($user['email']) ?></p>
        </div>
        <div class="form-section">
            <h3>Update Telegram</h3>
            <form method="POST">
                <label for="telegram_id">Telegram ID</label>
                <input type="text" name="telegram_id" id="telegram_id" value="<?= htmlspecialchars($user['telegram_id']) ?>">
                <label for="telegram_username">Telegram Username</label>
                <input type="text" name="telegram_username" id="telegram_username" value="<?= htmlspecialchars($user['telegram_username']) ?>">
                <button type="submit" name="update_telegram" class="custom-button"><i class="fas fa-sync-alt"></i> Update Telegram</button>
            </form>
        </div>
        <div class="form-section">
            <h3>Ubah Password</h3>
            <form method="POST">
                <label for="current_password">Password Lama</label>
                <input type="password" name="current_password" id="current_password" required>
                <label for="new_password">Password Baru</label>
                <input type="password" name="new_password" id="new_password" required>
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <button type="submit" name="change_password" class="custom-button"><i class="fas fa-lock"></i> Ubah Password</button>
            </form>
        </div>
        <div class="delete-account">
            <h3>Hapus Akun Anda</h3>
            <p>Dengan menghapus akun Anda, semua data terkait akan hilang. Pastikan Anda ingin melanjutkan.</p>
            <form method="POST">
                <label for="delete_password">Masukkan Password Anda untuk Mengonfirmasi</label>
                <input type="password" name="delete_password" id="delete_password" required>
                <button type="submit" name="delete_account" class="custom-button"><i class="fas fa-trash-alt"></i> Hapus Akun</button>
            </form>
        </div>
    </div>
</body>
</html>