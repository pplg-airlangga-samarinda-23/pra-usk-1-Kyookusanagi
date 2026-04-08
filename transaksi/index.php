<?php
include '../config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

// Get all transactions with user info
$query = "SELECT t.TransaksiID, t.PetugasID, t.TanggalTransaksi, t.Total, u.Username 
          FROM transaksi t 
          JOIN user u ON t.PetugasID = u.UserID 
          ORDER BY t.TanggalTransaksi DESC";
$data = mysqli_query($conn, $query);

if (!$data) {
    $error = "Error: " . mysqli_error($conn);
}

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error_msg = isset($_GET['error']) ? $_GET['error'] : (isset($error) ? $error : '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi - Kasir</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .transaksi-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .transaksi-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .transaksi-header h2 {
            color: #1e293b;
            font-size: 1.8rem;
            margin: 0;
        }

        .transaksi-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn-buat-transaksi {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-buat-transaksi:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .transaksi-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .transaksi-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .transaksi-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1rem;
        }

        .card-id {
            font-weight: 700;
            color: #2563eb;
            font-size: 1.1rem;
        }

        .card-date {
            color: #64748b;
            font-size: 0.85rem;
        }

        .card-info {
            display: grid;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        .info-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .info-value {
            color: #1e293b;
            font-weight: 600;
        }

        .total-price {
            font-size: 1.2rem;
            color: #059669;
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .btn-delete-trans {
            flex: 1;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            font-size: 0.9rem;
        }

        .btn-delete-trans:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-delete-trans:active {
            transform: translateY(0);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #64748b;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        @media (max-width: 768px) {
            .transaksi-cards {
                grid-template-columns: 1fr;
            }

            .transaksi-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .message.success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }

        .message.error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Kasir</div>
        <div class="navbar-menu">
            <a href="../dashboard.php">Dashboard</a>
            <span><?= htmlspecialchars($user['Role']); ?></span>
            <a href="../logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="transaksi-page">
            <div class="transaksi-header">
                <h2>Data Transaksi</h2>
                <div class="transaksi-buttons">
                    <?php if ($user['Role'] == 'petugas' || $user['Role'] == 'admin'): ?>
                        <a href="buat.php" class="btn-buat-transaksi">Buat Transaksi</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="message success">
                    <span><?= htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="message error">
                    <span><?= htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($data) > 0): ?>
                <div class="transaksi-cards">
                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                        <div class="transaksi-card">
                            <div class="card-header">
                                <div>
                                    <div class="card-id">#<?= htmlspecialchars($row['TransaksiID']); ?></div>
                                    <div class="card-date"><?= date('d M Y H:i', strtotime($row['TanggalTransaksi'])); ?></div>
                                </div>
                            </div>

                            <div class="card-info">
                                <div class="info-row">
                                    <span class="info-label">Petugas</span>
                                    <span class="info-value"><?= htmlspecialchars($row['Username']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Total</span>
                                    <span class="info-value total-price">Rp <?= number_format($row['Total'], 0, ',', '.'); ?></span>
                                </div>
                            </div>

                            <div class="card-actions">
                                <a href="hapus.php?id=<?= $row['TransaksiID']; ?>" 
                                   class="btn-delete-trans" 
                                   onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect>
                        <line x1="7" y1="6" x2="17" y2="6"></line>
                        <line x1="7" y1="12" x2="17" y2="12"></line>
                        <line x1="7" y1="18" x2="17" y2="18"></line>
                    </svg>
                    <h3>Belum ada transaksi</h3>
                    <p>Mulai dengan membuat transaksi baru</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Sistem Kasir</p>
    </footer>
</body>
</html>
