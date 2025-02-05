<?php
ob_start();
include('../admin/app/autoload.php');

// Variabel untuk pesan sukses atau gagal
$message = '';

// Handle update data jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $site_key = $_POST['site_key'];
    $secret_key = $_POST['secret_key'];
    $status = $_POST['status'];

    // Prepare query untuk update
    $stmt = $conn->prepare("UPDATE cloudflare_captcha SET site_key=?, secret_key=?, status=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("sssi", $site_key, $secret_key, $status, $id);

    // Eksekusi query dan periksa keberhasilan update
    if ($stmt->execute()) {
        $message = "Data berhasil diperbarui!";
    } else {
        $message = "Gagal memperbarui data: " . $stmt->error;
    }
}

// Ambil data dari database
$query = $conn->query("SELECT * FROM cloudflare_captcha ORDER BY id DESC");
ob_end_flush();
?>
	
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Cloudflare Captcha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container {
            max-width: 900px; /* Membatasi lebar kontainer */
        }
        .table {
            table-layout: fixed; /* Membatasi lebar tabel */
            width: 100%; /* Menyesuaikan lebar tabel */
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 8px; /* Mengurangi padding agar tabel tidak terlalu besar */
            font-size: 13px; /* Menyesuaikan ukuran font untuk tampilan lebih kecil */
        }
        .table th {
            font-weight: bold;
            background-color: #f1f1f1; /* Warna latar belakang untuk header tabel */
        }
        .table td {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap; /* Membatasi teks agar tidak meluber */
        }
        .badge-success, .badge-danger {
            text-transform: capitalize;
        }
        .modal-content {
            border-radius: 8px;
        }
        .btn {
            padding: 8px 16px;
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h4 class="text-center mb-3">Manajemen Cloudflare Captcha</h4>
    
    <!-- Tampilkan pesan setelah update -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center">
            <?= $message ?>
        </div>
    <?php endif; ?>
    
    <div class="table-responsive">
        <table class="table table-bordered table-sm text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Site Key</th>
                    <th>Secret Key</th>
                    <th>Status</th>
                    <th>Terakhir Diperbarui</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $query->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['site_key']) ?></td>
                    <td><?= htmlspecialchars($row['secret_key']) ?></td>
                    <td>
                        <span class="badge <?= $row['status'] == 'aktif' ? 'bg-success' : 'bg-danger' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td><?= $row['updated_at'] ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-btn" 
                            data-id="<?= $row['id'] ?>" 
                            data-sitekey="<?= htmlspecialchars($row['site_key']) ?>"
                            data-secretkey="<?= htmlspecialchars($row['secret_key']) ?>"
                            data-status="<?= $row['status'] ?>"
                            data-bs-toggle="modal" data-bs-target="#editModal">
                            Edit
                        </button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Captcha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label for="edit-sitekey" class="form-label">Site Key</label>
                        <input type="text" class="form-control" id="edit-sitekey" name="site_key" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-secretkey" class="form-label">Secret Key</label>
                        <input type="text" class="form-control" id="edit-secretkey" name="secret_key" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status</label>
                        <select class="form-select" id="edit-status" name="status">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Load data ke modal edit
    $('.edit-btn').click(function () {
        $('#edit-id').val($(this).data('id'));
        $('#edit-sitekey').val($(this).data('sitekey'));
        $('#edit-secretkey').val($(this).data('secretkey'));
        $('#edit-status').val($(this).data('status'));
    });
});
</script>

</body>
</html>