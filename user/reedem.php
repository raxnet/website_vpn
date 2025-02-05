<?php
session_start();
include('../config/config.php');

// Pastikan koneksi berhasil
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data kode kupon dari form dan amankan input
$kode_kupon = mysqli_real_escape_string($conn, $_POST['kode_kupon']);

// Query untuk cek kupon
$query = "SELECT * FROM kupon WHERE kode_kupon = '$kode_kupon'";
$result = mysqli_query($conn, $query);

// Cek apakah query berhasil dijalankan
if (!$result) {
    die("Error: " . mysqli_error($conn));
}

$kupon = mysqli_fetch_assoc($result);

if ($kupon) {
    // Cek apakah kupon sudah digunakan oleh user
    $user_id = $_SESSION['user_id'];
    $check_riwayat = "SELECT * FROM riwayat_kupon WHERE user_id = '$user_id' AND kode_kupon = '$kode_kupon'";
    $riwayat_result = mysqli_query($conn, $check_riwayat);

    if (mysqli_num_rows($riwayat_result) == 0) {  // Jika kupon belum digunakan
        // Tambahkan saldo
        $saldo_tambah = $kupon['saldo'];
        $update_saldo = "UPDATE users SET saldo = saldo + $saldo_tambah WHERE id = '$user_id'";
        $update_result = mysqli_query($conn, $update_saldo);

        if (!$update_result) {
            die("Error: " . mysqli_error($conn));
        }

        // Simpan penggunaan kupon ke riwayat
        $insert_riwayat = "INSERT INTO riwayat_kupon (user_id, kode_kupon) VALUES ('$user_id', '$kode_kupon')";
        mysqli_query($conn, $insert_riwayat);

        // Menampilkan pesan sukses dengan CSS
        echo "<div class='notification success show'>Kupon berhasil digunakan! Saldo Anda bertambah sebesar " . number_format($saldo_tambah, 0, ',', '.') . " IDR.</div>";
    } else {
        // Menampilkan pesan jika kupon sudah digunakan
        echo "<div class='notification error show'>Anda sudah menggunakan kupon ini sebelumnya.</div>";
    }
} else {
    // Menampilkan pesan jika kupon tidak valid
    echo "<div class='notification error show'>Kode kupon tidak valid.</div>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redeem Kupon</title>
    <style>
        /* Gaya untuk notifikasi */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745; /* Warna hijau untuk sukses */
            color: #fff;
            padding: 10px 20px;  /* Mengurangi padding untuk ukuran lebih kecil */
            border-radius: 5px;
            font-size: 14px;  /* Ukuran font lebih kecil */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            max-width: 300px; /* Lebar maksimum lebih kecil */
            width: auto; /* Mengatur agar ukuran notifikasi menyesuaikan konten */
            display: none;
        }

        .notification.show {
            opacity: 1;
            display: block;
        }

        /* Gaya untuk notifikasi error */
        .notification.error {
            background-color: #dc3545; /* Warna merah untuk error */
        }

        /* Gaya untuk notifikasi sukses */
        .notification.success {
            background-color: #28a745; /* Warna hijau untuk sukses */
        }
    </style>
</head>
<body>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            
            notifications.forEach(function(notification) {
                // Menghilangkan notifikasi setelah 5 detik
                setTimeout(function() {
                    notification.classList.remove('show');
                }, 5000);
            });

            // Redirect ke dashboard setelah 3 detik
            setTimeout(function() {
                window.location.href = '/user/dashboard'; // Ganti dengan URL dashboard kamu
            }, 3000); // 3 detik setelah notifikasi muncul
        });
    </script>

</body>
</html>