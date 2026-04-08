<?php
include '../config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

// Check if user is admin
if ($user['Role'] != 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$data = mysqli_query($conn, "SELECT * FROM user ORDER BY Role DESC, Username ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Kasir</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .user-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .user-header h2 {
            color: #1e293b;
            font-size: 1.8rem;
            margin: 0;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 2px solid #e2e8f0;
        }

        th {
            padding: 1.2rem;
            text-align: left;
            font-weight: 700;
            color: #1e293b;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .user-id {
            font-weight: 600;
            color: #2563eb;
        }

        .username {
            font-weight: 600;
            color: #1e293b;
        }

        .role-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .role-admin {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
        }

        .role-petugas {
            background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%);
            color: #1e40af;
        }

        .btn-small {
            display: inline-block;
            padding: 0.5rem 0.875rem;
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
        }

        .btn-edit:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-primary {
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

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #94a3b8;
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

        @media (max-width: 768px) {
            .user-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            th, td {
                padding: 0.75rem;
                font-size: 0.85rem;
            }

            .btn-small {
                display: block;
                margin-bottom: 0.5rem;
            }
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
        <div class="user-page">
            <div class="user-header">
                <h2>Kelola User</h2>
                <a href="tambah.php" class="btn-primary">Tambah User</a>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="message success">
                    <span><?= htmlspecialchars($_GET['success']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="message error">
                    <span><?= htmlspecialchars($_GET['error']); ?></span>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <?php if (mysqli_num_rows($data) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td class="user-id">#<?= htmlspecialchars($row['UserID']); ?></td>
                                <td class="username"><?= htmlspecialchars($row['Username']); ?></td>
                                <td>
                                    <span class="role-badge role-<?= strtolower($row['Role']); ?>">
                                        <?= htmlspecialchars($row['Role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?= $row['UserID']; ?>" class="btn-small btn-edit">Edit</a>
                                    <?php if ($row['UserID'] != $user['UserID']): ?>
                                        <a href="hapus.php?id=<?= $row['UserID']; ?>" class="btn-small btn-delete" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Belum ada user</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Sistem Kasir</p>
    </footer>
</body>
</html>
