<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk - Kasir</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php
    include '../config.php';

    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit;
    }

    $data = mysqli_query($conn, "SELECT * FROM produk");
    ?>

    <nav class="navbar">
        <div class="navbar-brand">Kasir</div>
        <div class="navbar-menu">
            <a href="../dashboard.php">Dashboard</a>
            <span><?= htmlspecialchars($_SESSION['user']['Role']); ?></span>
            <a href="../logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="table-container">
            <div class="table-header">
                <h2>Data Produk</h2>
                <a href="tambah.php" class="btn-primary">+ Tambah</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($d = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['ProdukID']); ?></td>
                        <td><?= htmlspecialchars($d['NamaProduk']); ?></td>
                        <td>Rp <?= number_format($d['Harga'], 0, ',', '.'); ?></td>
                        <td><?= htmlspecialchars($d['Stok']); ?></td>
                        <td>
                            <a href="edit.php?id=<?= $d['ProdukID']; ?>" class="btn-small btn-edit">Edit</a>
                            <a href="hapus.php?id=<?= $d['ProdukID']; ?>" class="btn-small btn-delete" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Sistem Kasir</p>
    </footer>
</body>
</html>