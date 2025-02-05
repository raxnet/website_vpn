<?php
// Menunda output
ob_start();
include('../admin/app/autoload.php');

// Menghapus pengguna
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $stmt_delete = $conn->prepare($delete_sql);
    if ($stmt_delete) {
        $stmt_delete->bind_param("i", $user_id);
        $stmt_delete->execute();
        $stmt_delete->close();
        header("Location: pengguna");
        exit();
    }
}

// Mengambil data pengguna untuk edit
if (isset($_GET['edit'])) {
    $user_id = $_GET['edit'];
    $edit_sql = "SELECT * FROM users WHERE id = ?";
    $stmt_edit = $conn->prepare($edit_sql);
    if ($stmt_edit) {
        $stmt_edit->bind_param("i", $user_id);
        $stmt_edit->execute();
        $result = $stmt_edit->get_result();
        $user_data = $result->fetch_assoc();
        $stmt_edit->close();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_sql);
        if ($stmt_update) {
            $stmt_update->bind_param("ssi", $username, $email, $user_id);
            $stmt_update->execute();
            $stmt_update->close();
            header("Location: pengguna");
            exit();
        }
    }
}

ob_end_flush();
?><!DOCTYPE html><html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <style>
        body {
            background-color: #ffffff;
            color: #333;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

.container {
        max-width: 500px;
        margin: 20px auto;
        background: #ffffff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        margin-top: 90px;
    }
body.dark-mode .container {
            background-color: #34495e;
        }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;
    }

    .btn {
        font-size: 12px;
        padding: 6px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-primary { background-color: #007bff; color: #fff; }
    .btn-primary:hover { background-color: #0056b3; }

    .btn-danger { background-color: #dc3545; color: #fff; }
    .btn-danger:hover { background-color: #bd2130; }

    .user-card {
        background: #f8f8f8;
        padding: 10px;
        margin: 10px 0;
        border-radius: 6px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    body.dark-mode .user-card {
           background-color: #2c3e50;
        }

body.dark-mode .container {
           color: #2c3e50;
        }
        
    .user-info {
        font-size: 13px;
        color: #444;
    }
body.dark-mode .user-info {
           color: #ffffff;
        }
    .actions {
        display: flex;
        gap: 5px;
    }

    .form-container {
        margin-top: 15px;
        background: #f9f9f9;
        padding: 10px;
        border-radius: 6px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    body.dark-mode .from-container {
           background-color: #2c3e50;
        }

    .form-container input {
        width: 100%;
        padding: 6px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 12px;
    }

    .form-container button {
        margin-top: 10px;
        width: 100%;
    }

</style>

</head>
<body><div class="container">
    <div class="header">
        <form action="pengguna" method="GET" style="flex-grow: 1; margin-right: 10px;">
            <input type="text" name="search" placeholder="Cari pengguna..." style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
        </form></div>

<!-- Daftar Pengguna -->
<?php
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($user = $result->fetch_assoc()) {
        echo "<div class='user-card'>";
        echo "<div class='user-info'><strong>" . htmlspecialchars($user['username']) . "</strong><br>" . htmlspecialchars($user['email']) . "</div>";
        echo "<div class='actions'>
                <a href='pengguna?edit=" . $user['id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                <a href='pengguna?delete=" . $user['id'] . "' onclick='return confirm(\"Hapus pengguna ini?\")' class='btn btn-danger btn-sm'>Hapus</a>
              </div>";
        echo "</div>";
    }
} else {
    echo "<p style='text-align:center;'>Tidak ada pengguna.</p>";
}
?>

<!-- Form Edit -->
<?php if (isset($_GET['edit'])): ?>
    <div class="form-container">
        <form action="pengguna.php?edit=<?php echo $user_data['id']; ?>" method="POST">
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
<?php endif; ?>

</div></body>
</html>