<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

function get_count($koneksi, $table) {
    $result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM `$table`");
    return mysqli_fetch_assoc($result)['total'] ?? 0;
}

$produkCount = get_count($koneksi, 'produk');
$transaksiCount = get_count($koneksi, 'penjualan');
$pelangganCount = get_count($koneksi, 'pelanggan');

$totalPendapatan = 0;
$result = mysqli_query($koneksi, "SELECT SUM(TotalHarga) AS total FROM penjualan");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalPendapatan = $row['total'] ?? 0;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #eef2ff; }
        .navbar { background: #ffffff; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); }
        .card { border: 0; border-radius: 18px; }
        .stat-card { transition: transform .2s ease; }
        .stat-card:hover { transform: translateY(-4px); }
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
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="bg-white rounded-4 p-4 shadow-sm">
                <h2>Dashboard</h2>
                <p class="text-muted">Ringkasan data kasir berdasarkan database kamu.</p>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card p-4 stat-card">
                <h6 class="text-uppercase text-secondary">Produk</h6>
                <h2><?= $produkCount ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 stat-card">
                <h6 class="text-uppercase text-secondary">Transaksi</h6>
                <h2><?= $transaksiCount ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 stat-card">
                <h6 class="text-uppercase text-secondary">Pelanggan</h6>
                <h2><?= $pelangganCount ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 stat-card">
                <h6 class="text-uppercase text-secondary">Penjualan</h6>
                <h2>Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h2>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
