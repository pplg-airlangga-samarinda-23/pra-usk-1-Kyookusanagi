<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

$query = "SELECT p.PenjualanID, p.TanggalPenjualan, p.TotalHarga, pel.NamaPelanggan
    FROM penjualan p
    LEFT JOIN pelanggan pel ON p.PelangganID = pel.PelangganID
    ORDER BY p.TanggalPenjualan DESC, p.PenjualanID DESC";
$transaksiList = mysqli_query($koneksi, $query);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaksi - Kasir</title>
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
                <li class="nav-item"><a class="nav-link" href="pelanggan.php">Pelanggan</a></li>
                <li class="nav-item"><a class="nav-link active" href="transaksi.php">Transaksi</a></li>
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
    <div class="card p-4 shadow-sm mb-4">
        <h4>Riwayat Penjualan</h4>
        <p class="text-muted">Lihat transaksi lengkap dengan tanggal, total, dan nama pelanggan.</p>
    </div>
    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <div class="card p-4 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($transaksi = mysqli_fetch_assoc($transaksiList)): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaksi['PenjualanID']) ?></td>
                            <td><?= htmlspecialchars($transaksi['TanggalPenjualan']) ?></td>
                            <td><?= htmlspecialchars($transaksi['NamaPelanggan'] ?: 'Umum') ?></td>
                            <td>Rp <?= number_format($transaksi['TotalHarga'], 0, ',', '.') ?></td>
                            <td>
                                <a href="transaksi_detail.php?penjualan_id=<?= $transaksi['PenjualanID'] ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
