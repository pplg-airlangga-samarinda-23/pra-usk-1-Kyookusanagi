<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kasir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    include 'config.php';

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    $user = $_SESSION['user'];
    ?>

    <nav class="navbar">
        <div class="navbar-brand">Kasir</div>
        <div class="navbar-menu">
            <span><?= htmlspecialchars($user['Role']); ?></span>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard">
            <h2>Dashboard</h2>
            <div class="dashboard-info">
                <p>Selamat datang, <strong><?= htmlspecialchars($user['Role']); ?></strong></p>
            </div>

            <div class="dashboard-links">
                <a href="produk/index.php">Kelola Produk</a>
                <?php if ($user['Role'] == 'admin'): ?>
                    <a href="#" class="admin">Kelola User</a>
                    <a href="#" class="admin">Laporan</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Sistem Kasir</p>
    </footer>
</body>
</html>