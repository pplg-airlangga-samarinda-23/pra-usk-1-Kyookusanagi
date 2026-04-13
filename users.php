<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['Username']));
    $password = trim($_POST['Password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['Role']);

    if ($username && $password && in_array($role, ['admin', 'petugas'])) {
        $hash = md5($password);
        $check = mysqli_query($koneksi, "SELECT UserID FROM `user` WHERE Username = '$username' LIMIT 1");
        if (mysqli_num_rows($check) === 0) {
            mysqli_query($koneksi, "INSERT INTO `user` (Username, Password, Role) VALUES ('$username', '$hash', '$role')");
            $message = 'User baru berhasil dibuat.';
        } else {
            $message = 'Username sudah dipakai. Pilih username lain.';
        }
    } else {
        $message = 'Lengkapi semua field dengan benar.';
    }
}

$userList = mysqli_query($koneksi, "SELECT UserID, Username, Role FROM `user` ORDER BY UserID DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User - Kasir</title>
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
                <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
                <li class="nav-item"><a class="nav-link active" href="users.php">User</a></li>
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
                <h4>Buat User Baru</h4>
                <?php if ($message): ?>
                    <div class="alert alert-info py-2"><?= $message ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="Username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="Password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="Role" class="form-select" required>
                            <option value="petugas">Petugas</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Buat User</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card p-4 shadow-sm">
                <h4>Daftar User</h4>
                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = mysqli_fetch_assoc($userList)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['UserID']) ?></td>
                                    <td><?= htmlspecialchars($user['Username']) ?></td>
                                    <td><?= htmlspecialchars($user['Role']) ?></td>
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
