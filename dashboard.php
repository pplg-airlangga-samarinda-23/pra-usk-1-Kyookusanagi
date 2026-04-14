<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kasir</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard {
            max-width: 1000px;
            margin: 2rem auto;
        }

        .dashboard h2 {
            color: #1e293b;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .dashboard-info {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.2);
        }

        .dashboard-info p {
            font-size: 1.1rem;
            margin: 0;
        }

        .dashboard-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .dashboard-links a {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 2rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            color: #1e293b;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            font-size: 1.1rem;
        }

        .dashboard-links a:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: #2563eb;
            color: #2563eb;
        }

        @media (max-width: 768px) {
            .dashboard-links {
                grid-template-columns: 1fr;
            }

            .dashboard h2 {
                font-size: 1.5rem;
            }
        }
    </style>
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
                <p>Selamat datang, <strong><?= htmlspecialchars($user['Username']); ?></strong> (<?= htmlspecialchars($user['Role']); ?>)</p>
            </div>

            <div class="dashboard-links">
                <a href="produk/index.php">Kelola Produk</a>
                <?php if ($user['Role'] == 'petugas' || $user['Role'] == 'admin'): ?>
                    <a href="transaksi/index.php">Transaksi</a>
                <?php endif; ?>
                <?php if ($user['Role'] == 'admin'): ?>
                    <a href="user/index.php">Kelola User</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Sistem Kasir</p>
    </footer>
</body>
</html>