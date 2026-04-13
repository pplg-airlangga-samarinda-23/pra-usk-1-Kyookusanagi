<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$penjualanId = isset($_GET['penjualan_id']) ? intval($_GET['penjualan_id']) : 0;

$penjualan = null;
if ($penjualanId > 0) {
    $result = mysqli_query($koneksi, "SELECT p.PenjualanID, p.TanggalPenjualan, p.TotalHarga, pel.NamaPelanggan, pel.Alamat, pel.NomorTelepon
        FROM penjualan p
        LEFT JOIN pelanggan pel ON p.PelangganID = pel.PelangganID
        WHERE p.PenjualanID = $penjualanId LIMIT 1");
    $penjualan = mysqli_fetch_assoc($result);
}

$detailList = [];
if ($penjualan) {
    $detailResult = mysqli_query($koneksi, "SELECT dp.JumlahProduk, dp.Subtotal, prod.NamaProduk, prod.Harga
        FROM detailpenjualan dp
        LEFT JOIN produk prod ON dp.ProdukID = prod.ProdukID
        WHERE dp.PenjualanID = $penjualanId");
    while ($row = mysqli_fetch_assoc($detailResult)) {
        $detailList[] = $row;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Transaksi - Kasir</title>
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
    <?php if (!$penjualan): ?>
        <div class="alert alert-warning">Data transaksi tidak ditemukan. <a href="transaksi.php">Kembali ke daftar transaksi</a>.</div>
    <?php else: ?>
        <div class="card p-4 shadow-sm mb-4">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h4>Detail Penjualan #<?= htmlspecialchars($penjualan['PenjualanID']) ?></h4>
                    <p class="text-muted mb-1">Tanggal: <?= htmlspecialchars($penjualan['TanggalPenjualan']) ?></p>
                    <p class="text-muted mb-0">Pelanggan: <?= htmlspecialchars($penjualan['NamaPelanggan'] ?: 'Umum') ?></p>
                </div>
                <div class="text-end">
                    <p class="text-secondary mb-1">Total Penjualan</p>
                    <h4>Rp <?= number_format($penjualan['TotalHarga'], 0, ',', '.') ?></h4>
                </div>
            </div>
            <?php if ($penjualan['NamaPelanggan']): ?>
                <p class="mb-0"><strong>Alamat:</strong> <?= htmlspecialchars($penjualan['Alamat']) ?></p>
                <p><strong>Telepon:</strong> <?= htmlspecialchars($penjualan['NomorTelepon']) ?></p>
            <?php endif; ?>
        </div>
        <div class="card p-4 shadow-sm">
            <h5 class="mb-3">Detail Produk</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detailList as $detail): ?>
                            <tr>
                                <td><?= htmlspecialchars($detail['NamaProduk']) ?></td>
                                <td>Rp <?= number_format($detail['Harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($detail['JumlahProduk']) ?></td>
                                <td>Rp <?= number_format($detail['Subtotal'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <a href="transaksi.php" class="btn btn-secondary mt-3">Kembali</a>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
