<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$editProduct = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['NamaProduk']));
    $harga = floatval($_POST['Harga']);
    $stok = intval($_POST['Stok']);

    if (!empty($_POST['ProdukID'])) {
        $id = intval($_POST['ProdukID']);
        mysqli_query($koneksi, "UPDATE produk SET NamaProduk = '$nama', Harga = $harga, Stok = $stok WHERE ProdukID = $id");
        $message = 'Data produk berhasil diperbarui.';
    } else {
        mysqli_query($koneksi, "INSERT INTO produk (NamaProduk, Harga, Stok) VALUES ('$nama', $harga, $stok)");
        $message = 'Produk baru berhasil ditambahkan.';
    }
    header('Location: produk.php?msg=' . urlencode($message));
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($koneksi, "DELETE FROM produk WHERE ProdukID = $id");
    header('Location: produk.php?msg=' . urlencode('Produk berhasil dihapus.'));
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = mysqli_query($koneksi, "SELECT * FROM produk WHERE ProdukID = $id LIMIT 1");
    $editProduct = mysqli_fetch_assoc($result);
}

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

$produkList = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY ProdukID DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produk - Kasir</title>
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
                <li class="nav-item"><a class="nav-link active" href="produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="pelanggan.php">Pelanggan</a></li>
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
                <h4><?= $editProduct ? 'Edit Produk' : 'Tambah Produk' ?></h4>
                <?php if ($message): ?>
                    <div class="alert alert-success py-2"><?= $message ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="ProdukID" value="<?= $editProduct ? htmlspecialchars($editProduct['ProdukID']) : '' ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="NamaProduk" class="form-control" required value="<?= $editProduct ? htmlspecialchars($editProduct['NamaProduk']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="Harga" step="0.01" class="form-control" required value="<?= $editProduct ? htmlspecialchars($editProduct['Harga']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="Stok" class="form-control" required value="<?= $editProduct ? htmlspecialchars($editProduct['Stok']) : '' ?>">
                    </div>
                    <button class="btn btn-primary w-100">Simpan</button>
                    <?php if ($editProduct): ?>
                        <a href="produk.php" class="btn btn-link mt-2">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Data Produk</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($produk = mysqli_fetch_assoc($produkList)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($produk['ProdukID']) ?></td>
                                    <td><?= htmlspecialchars($produk['NamaProduk']) ?></td>
                                    <td>Rp <?= number_format($produk['Harga'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($produk['Stok']) ?></td>
                                    <td>
                                        <a href="produk.php?edit=<?= $produk['ProdukID'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="produk.php?delete=<?= $produk['ProdukID'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
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
