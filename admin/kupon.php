<?php
ob_start();
include('../admin/app/autoload.php');

// Tambah kupon baru
if (isset($_POST['tambah_kupon'])) {
    $kode_kupon = strtoupper($_POST['kode_kupon']);
    $saldo = intval($_POST['saldo']);
    $status = $_POST['status'];
    $expired_at = $_POST['expired_at'];

    $query = "INSERT INTO kupon (kode_kupon, saldo, status, expired_at) VALUES ('$kode_kupon', '$saldo', '$status', '$expired_at')";
    mysqli_query($conn, $query);
    header("Location: kupon.php?success=Kupon berhasil ditambahkan");
    exit;
}

// Edit kupon
if (isset($_POST['edit_kupon'])) {
    $id = $_POST['id'];
    $kode_kupon = strtoupper($_POST['kode_kupon']);
    $saldo = intval($_POST['saldo']);
    $status = $_POST['status'];
    $expired_at = $_POST['expired_at'];

    $query = "UPDATE kupon SET kode_kupon='$kode_kupon', saldo='$saldo', status='$status', expired_at='$expired_at' WHERE id='$id'";
    mysqli_query($conn, $query);
    header("Location: kupon?success=Kupon berhasil diperbarui");
    exit;
}

// Hapus kupon
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kupon WHERE id='$id'");
    header("Location: kupon?success=Kupon berhasil dihapus");
    exit;
}

// Ambil semua data kupon
$kupon_query = mysqli_query($conn, "SELECT * FROM kupon ORDER BY created_at DESC");
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kupon</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; color: #333; margin: 10px; }
        .container { max-width: 700px; margin-top: 50px; background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; font-size: 18px; margin-bottom: 10px; }
        input, select, button { width: 100%; padding: 6px; margin: 3px 0; font-size: 14px; }
        input, select { border: 1px solid #ccc; border-radius: 3px; }
        button { background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        table { width: 100%; margin-top: 10px; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 6px; border: 1px solid #ddd; text-align: center; }
        th { background: #f1f1f1; }
        .edit-btn { background: #; color: #333; padding: 4px 6px; border-radius :4px;}
        .hapus-btn { background: #; color: #fff; padding: 5px 7px; border-radius :4px;}
        .notif { padding: 8px; margin: 5px 0; text-align: center; font-size: 13px; }
        .success { background: #28a745; color: white; }
        .error { background: #dc3545; color: white; }
        .popup { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:15px; border-radius:5px; box-shadow: 0 0 10px rgba(0,0,0,0.2); max-width:350px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Kelola Kupon</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="notif success"><?= htmlspecialchars($_GET['success']) ?></div>
        <script> setTimeout(() => document.querySelector('.notif').remove(), 3000); </script>
    <?php endif; ?>

    <!-- Form Tambah Kupon -->
    <form method="post">
        <input type="text" name="kode_kupon" placeholder="Kode Kupon" required>
        <input type="number" name="saldo" placeholder="Saldo (IDR)" required>
        <select name="status">
            <option value="aktif">Aktif</option>
            <option value="tidak aktif">Tidak Aktif</option>
        </select>
        <input type="datetime-local" name="expired_at">
        <button type="submit" name="tambah_kupon">Tambah Kupon</button>
    </form>

    <!-- Tabel Kupon -->
    <table>
        <tr>
            <th>Kode</th>
            <th>Saldo</th>
            <th>Status</th>
            <th>Expired</th>
            <th>Aksi</th>
        </tr>
        <?php while ($kupon = mysqli_fetch_assoc($kupon_query)): ?>
        <tr>
            <td><?= htmlspecialchars($kupon['kode_kupon']) ?></td>
            <td><?= number_format($kupon['saldo'], 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($kupon['status']) ?></td>
            <td><?= $kupon['expired_at'] ? date('d-m-Y H:i', strtotime($kupon['expired_at'])) : '-' ?></td>
            <td>
                <button class="edit-btn" onclick="editKupon(<?= $kupon['id'] ?>, '<?= $kupon['kode_kupon'] ?>', <?= $kupon['saldo'] ?>, '<?= $kupon['status'] ?>', '<?= $kupon['expired_at'] ?>')">✏</button>
                <a href="?hapus=<?= $kupon['id'] ?>" onclick="return confirm('Hapus kupon ini?')" class="hapus-btn">❌</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Form Edit Kupon (Popup) -->
<div id="editPopup" class="popup">
    <h3>Edit Kupon</h3>
    <form method="post">
        <input type="hidden" name="id" id="edit_id">
        <input type="text" name="kode_kupon" id="edit_kode" required>
        <input type="number" name="saldo" id="edit_saldo" required>
        <select name="status" id="edit_status">
            <option value="aktif">Aktif</option>
            <option value="tidak aktif">Tidak Aktif</option>
        </select>
        <input type="datetime-local" name="expired_at" id="edit_expired">
        <button type="submit" name="edit_kupon">Simpan</button>
        <button type="button" onclick="document.getElementById('editPopup').style.display='none'">Batal</button>
    </form>
</div>

<script>
    function editKupon(id, kode, saldo, status, expired) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_kode').value = kode;
        document.getElementById('edit_saldo').value = saldo;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_expired').value = expired ? expired.replace(' ', 'T') : '';
        document.getElementById('editPopup').style.display = 'block';
    }
</script>

</body>
</html>