<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$editPelanggan = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['NamaPelanggan']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['Alamat']));
    $telepon = mysqli_real_escape_string($koneksi, trim($_POST['NomorTelepon']));

    if (!empty($_POST['PelangganID'])) {
        $id = intval($_POST['PelangganID']);
        mysqli_query($koneksi, "UPDATE pelanggan SET NamaPelanggan = '$nama', Alamat = '$alamat', NomorTelepon = '$telepon' WHERE PelangganID = $id");
        $message = 'Data pelanggan berhasil diperbarui.';
    } else {
        mysqli_query($koneksi, "INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES ('$nama', '$alamat', '$telepon')");
        $message = 'Pelanggan baru berhasil ditambahkan.';
    }
    header('Location: pelanggan.php?msg=' . urlencode($message));
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($koneksi, "DELETE FROM pelanggan WHERE PelangganID = $id");
    header('Location: pelanggan.php?msg=' . urlencode('Pelanggan berhasil dihapus.'));
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE PelangganID = $id LIMIT 1");
    $editPelanggan = mysqli_fetch_assoc($result);
}

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

$pelangganList = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY PelangganID DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pelanggan - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7fb; }
        .navbar { background: #ffffff; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); }
        .card { border: 0; border-radius: 18px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">Kasir</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link active" href="pelanggan.php">Pelanggan</a></li>
                <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="users.php">User</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center">
                <span class="me-3 text-secondary">Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a class="btn btn-outline-secondary btn-sm" href="logout.php">Keluar</a>
            </div>
        </div>
    </div>
</nav>
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card p-4 shadow-sm">
                <h4><?= $editPelanggan ? 'Edit Pelanggan' : 'Tambah Pelanggan' ?></h4>
                <?php if ($message): ?>
                    <div class="alert alert-success py-2"><?= $message ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="PelangganID" value="<?= $editPelanggan ? htmlspecialchars($editPelanggan['PelangganID']) : '' ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" name="NamaPelanggan" class="form-control" required value="<?= $editPelanggan ? htmlspecialchars($editPelanggan['NamaPelanggan']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="Alamat" class="form-control" rows="3"><?= $editPelanggan ? htmlspecialchars($editPelanggan['Alamat']) : '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="NomorTelepon" class="form-control" value="<?= $editPelanggan ? htmlspecialchars($editPelanggan['NomorTelepon']) : '' ?>">
                    </div>
                    <button class="btn btn-primary w-100">Simpan</button>
                    <?php if ($editPelanggan): ?>
                        <a href="pelanggan.php" class="btn btn-link mt-2">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Data Pelanggan</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($pelanggan = mysqli_fetch_assoc($pelangganList)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pelanggan['PelangganID']) ?></td>
                                    <td><?= htmlspecialchars($pelanggan['NamaPelanggan']) ?></td>
                                    <td><?= htmlspecialchars($pelanggan['NomorTelepon']) ?></td>
                                    <td><?= htmlspecialchars($pelanggan['Alamat']) ?></td>
                                    <td>
                                        <a href="pelanggan.php?edit=<?= $pelanggan['PelangganID'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="pelanggan.php?delete=<?= $pelanggan['PelangganID'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pelanggan ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
