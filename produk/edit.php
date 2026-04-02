<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Kasir</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php
    include '../config.php';
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE ProdukID=$id"));
    ?>

    <nav class="navbar">
        <div class="navbar-brand">Kasir</div>
        <div class="navbar-menu">
            <a href="../dashboard.php">Dashboard</a>
            <a href="index.php">Produk</a>
            <a href="../logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Edit Produk</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="nama">Nama Produk</label>
                    <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['NamaProduk']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" id="harga" name="harga" value="<?= htmlspecialchars($data['Harga']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($data['Stok']); ?>" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="update" class="btn-submit">Update</button>
                    <a href="index.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <?php
    if (isset($_POST['update'])) {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $harga = mysqli_real_escape_string($conn, $_POST['harga']);
        $stok = mysqli_real_escape_string($conn, $_POST['stok']);

        mysqli_query($conn, "UPDATE produk SET 
        NamaProduk='$nama',
        Harga='$harga',
        Stok='$stok'
        WHERE ProdukID=$id");

        header("Location: index.php");
    }
    ?>

    <footer>
        <p>&copy; 2026 Sistem Kasir</p>
    </footer>
</body>
</html>