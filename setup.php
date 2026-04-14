<?php
$conn = mysqli_connect("localhost", "root", "", "kasir");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Create user table if not exists
$user_table = "CREATE TABLE IF NOT EXISTS user (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(100) UNIQUE NOT NULL,
    Password VARCHAR(100) NOT NULL,
    Role ENUM('admin', 'petugas') NOT NULL
)";

// Create produk table if not exists
$produk_table = "CREATE TABLE IF NOT EXISTS produk (
    ProdukID INT AUTO_INCREMENT PRIMARY KEY,
    NamaProduk VARCHAR(100) NOT NULL,
    Harga INT NOT NULL,
    Stok INT NOT NULL
)";

// Create transaksi table if not exists
$transaksi_table = "CREATE TABLE IF NOT EXISTS transaksi (
    TransaksiID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    TanggalTransaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    TotalHarga INT NOT NULL,
    FOREIGN KEY (UserID) REFERENCES user(UserID)
)";

// Create detail_transaksi table if not exists
$detail_table = "CREATE TABLE IF NOT EXISTS detail_transaksi (
    DetailID INT AUTO_INCREMENT PRIMARY KEY,
    TransaksiID INT NOT NULL,
    ProdukID INT NOT NULL,
    Jumlah INT NOT NULL,
    Subtotal INT NOT NULL,
    FOREIGN KEY (TransaksiID) REFERENCES transaksi(TransaksiID),
    FOREIGN KEY (ProdukID) REFERENCES produk(ProdukID)
)";

if (mysqli_query($conn, $user_table) && 
    mysqli_query($conn, $produk_table) && 
    mysqli_query($conn, $transaksi_table) && 
    mysqli_query($conn, $detail_table)) {
    
    // Check if default admin user exists
    $check = mysqli_query($conn, "SELECT * FROM user WHERE Username='admin'");
    if (mysqli_num_rows($check) == 0) {
        // Insert default admin user
        $admin_pass = md5("admin123");
        mysqli_query($conn, "INSERT INTO user (Username, Password, Role) VALUES ('admin', '$admin_pass', 'admin')");
    }
    
    echo "<div style='font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;'>";
    echo "<h2 style='color: #2563eb;'>✓ Setup Berhasil</h2>";
    echo "<p>Database dan tabel telah disiapkan.</p>";
    echo "<p><strong>Default Admin Account:</strong></p>";
    echo "<ul style='background: white; padding: 15px; border-radius: 3px; border-left: 3px solid #2563eb;'>";
    echo "<li>Username: <code style='background: #f0f0f0; padding: 2px 5px;'>admin</code></li>";
    echo "<li>Password: <code style='background: #f0f0f0; padding: 2px 5px;'>admin123</code></li>";
    echo "</ul>";
    echo "<p><a href='login.php' style='display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Pergi ke Login</a></p>";
    echo "</div>";
} else {
    echo "<div style='font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #dc2626; border-radius: 5px; background: #fee;'>";
    echo "<h2 style='color: #dc2626;'>✗ Setup Gagal</h2>";
    echo "<p>Error: " . mysqli_error($conn) . "</p>";
    echo "</div>";
}

mysqli_close($conn);
?>
