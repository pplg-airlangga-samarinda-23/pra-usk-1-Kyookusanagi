<?php
include '../config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

// Check if user is petugas or admin
if ($user['Role'] != 'petugas' && $user['Role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart = json_decode($_POST['cart'] ?? '[]', true);
    
    if (!is_array($cart) || empty($cart)) {
        $message = 'Keranjang masih kosong!';
        $message_type = 'error';
    } else {
        try {
            // Start transaction
            mysqli_begin_transaction($conn);
            
            // Calculate total
            $total = 0;
            foreach ($cart as $item) {
                $total += intval($item['subtotal']);
            }
            
            // Insert transaksi
            $insert_transaksi = "INSERT INTO transaksi (PetugasID, Total, TanggalTransaksi) VALUES ({$user['UserID']}, $total, CURRENT_TIMESTAMP)";
            if (!mysqli_query($conn, $insert_transaksi)) {
                throw new Exception("Gagal membuat transaksi: " . mysqli_error($conn));
            }
            
            $transaksi_id = mysqli_insert_id($conn);
            
            // Insert detail transaksi
            foreach ($cart as $item) {
                $insert_detail = "INSERT INTO detail_transaksi (TransaksiID, ProdukID, Jumlah, Harga) 
                                VALUES ($transaksi_id, {$item['product_id']}, {$item['quantity']}, {$item['subtotal']})";
                if (!mysqli_query($conn, $insert_detail)) {
                    throw new Exception("Gagal menambah item transaksi: " . mysqli_error($conn));
                }
                
                // Update stok produk
                $update_stok = "UPDATE produk SET Stok = Stok - {$item['quantity']} WHERE ProdukID = {$item['product_id']}";
                if (!mysqli_query($conn, $update_stok)) {
                    throw new Exception("Gagal update stok: " . mysqli_error($conn));
                }
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            $message = 'Transaksi berhasil dibuat! ID: #' . $transaksi_id;
            $message_type = 'success';
            $cart = []; // Reset cart
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Get all products
$produk_query = mysqli_query($conn, "SELECT * FROM produk WHERE Stok > 0 ORDER BY NamaProduk ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Transaksi - Kasir</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .transaksi-wrapper {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .transaksi-form-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .form-section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .produk-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .produk-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .produk-item:hover {
            background: white;
            border-color: #2563eb;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
        }

        .produk-info {
            flex: 1;
        }

        .produk-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .produk-price {
            color: #10b981;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .produk-stok {
            color: #64748b;
            font-size: 0.85rem;
        }

        .btn-tambah-item {
            background: #2563eb;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-tambah-item:hover {
            background: #1d4ed8;
        }

        .keranjang-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .keranjang-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.85rem;
        }

        .keranjang-item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .keranjang-item-name {
            font-weight: 600;
            color: #1e293b;
            flex: 1;
        }

        .keranjang-item-qty {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            background: white;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .qty-btn {
            background: none;
            border: none;
            padding: 0.25rem 0.5rem;
            cursor: pointer;
            color: #2563eb;
            font-weight: 700;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: #e2e8f0;
        }

        .keranjang-item-price {
            color: #10b981;
            font-weight: 600;
        }

        .keranjang-empty {
            text-align: center;
            color: #94a3b8;
            padding: 1rem;
        }

        .keranjang-total {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin: 1rem 0;
        }

        .keranjang-total-label {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .keranjang-total-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .keranjang-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-simpan, .btn-batal {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-simpan {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-simpan:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-simpan:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-batal {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-batal:hover {
            background: #cbd5e1;
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

        @media (max-width: 1024px) {
            .transaksi-wrapper {
                grid-template-columns: 1fr;
            }

            .keranjang-section {
                position: static;
            }

            .produk-list {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Kasir</div>
        <div class="navbar-menu">
            <a href="index.php">Transaksi</a>
            <span><?= htmlspecialchars($user['Role']); ?></span>
            <a href="../logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div class="message <?= $message_type; ?>">
                <span>
                    <?php if ($message_type == 'success'): ?>
                        <?= htmlspecialchars($message); ?> 
                        <br><a href="index.php" style="color: inherit; text-decoration: underline;">Lihat transaksi</a>
                    <?php else: ?>
                        <?= htmlspecialchars($message); ?>
                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="transaksi-wrapper">
            <div class="transaksi-form-section">
                <h3 class="form-section-title">Pilih Produk</h3>
                <div class="produk-list">
                    <?php while ($produk = mysqli_fetch_assoc($produk_query)): ?>
                        <div class="produk-item" onclick="tambahKeKeranjang(<?= htmlspecialchars(json_encode($produk)); ?>)">
                            <div class="produk-info">
                                <div class="produk-name"><?= htmlspecialchars($produk['NamaProduk']); ?></div>
                                <div class="produk-price">Rp <?= number_format($produk['Harga'], 0, ',', '.'); ?></div>
                                <div class="produk-stok">Stok: <?= $produk['Stok']; ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <form method="POST" id="form-transaksi">
                <div class="keranjang-section">
                    <h3 class="form-section-title">Keranjang</h3>
                    <div id="keranjang-items"></div>

                    <div class="keranjang-total">
                        <div class="keranjang-total-label">Total Harga</div>
                        <div class="keranjang-total-value">Rp <span id="total-harga">0</span></div>
                    </div>

                    <input type="hidden" name="cart" id="cart-data" value="[]">

                    <div class="keranjang-actions">
                        <button type="submit" class="btn-simpan" id="btn-submit">Simpan Transaksi</button>
                        <a href="index.php" class="btn-batal">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Sistem Kasir</p>
    </footer>

    <script>
        let cart = [];

        function formatNumber(num) {
            // Handle NaN or invalid numbers
            if (isNaN(num) || !isFinite(num)) {
                return '0';
            }
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function tambahKeKeranjang(produk) {
            // Ensure numeric values
            produk.Harga = parseInt(produk.Harga) || 0;
            produk.Stok = parseInt(produk.Stok) || 0;
            
            let existing = cart.find(item => item.product_id == produk.ProdukID);
            
            if (existing) {
                if (existing.quantity < produk.Stok) {
                    existing.quantity++;
                    existing.subtotal = existing.quantity * existing.price;
                }
            } else {
                cart.push({
                    product_id: produk.ProdukID,
                    product_name: produk.NamaProduk,
                    price: parseInt(produk.Harga),
                    quantity: 1,
                    subtotal: parseInt(produk.Harga),
                    stok: parseInt(produk.Stok)
                });
            }
            
            updateKeranjang();
        }

        function updateKeranjang() {
            let html = '';
            let total = 0;

            if (cart.length === 0) {
                html = '<div class="keranjang-empty">Belum ada item</div>';
            } else {
                cart.forEach((item, index) => {
                    // Ensure all numeric values are valid
                    item.quantity = parseInt(item.quantity) || 1;
                    item.price = parseInt(item.price) || 0;
                    item.subtotal = item.quantity * item.price;
                    
                    total += item.subtotal;
                    html += `
                        <div class="keranjang-item">
                            <div class="keranjang-item-header">
                                <div class="keranjang-item-name">${item.product_name || 'Unknown Product'}</div>
                                <button type="button" onclick="hapusDariKeranjang(${index})" style="background: none; border: none; color: #ef4444; cursor: pointer; font-weight: 700;">✕</button>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="keranjang-item-qty">
                                    <button type="button" class="qty-btn" onclick="kurangiQty(${index})">−</button>
                                    <span style="padding: 0.25rem 0.5rem; border-right: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0;">${item.quantity}</span>
                                    <button type="button" class="qty-btn" onclick="tambahQty(${index})">+</button>
                                </div>
                                <div class="keranjang-item-price">Rp ${formatNumber(item.subtotal)}</div>
                            </div>
                        </div>
                    `;
                });
            }

            // Ensure total is a valid number
            total = parseInt(total) || 0;
            
            document.getElementById('keranjang-items').innerHTML = html;
            document.getElementById('total-harga').textContent = formatNumber(total);
            document.getElementById('cart-data').value = JSON.stringify(cart);
            document.getElementById('btn-submit').disabled = cart.length === 0;
        }

        function hapusDariKeranjang(index) {
            cart.splice(index, 1);
            updateKeranjang();
        }

        function tambahQty(index) {
            if (cart[index] && cart[index].quantity < cart[index].stok) {
                cart[index].quantity = parseInt(cart[index].quantity) + 1;
                cart[index].subtotal = cart[index].quantity * parseInt(cart[index].price);
                updateKeranjang();
            }
        }

        function kurangiQty(index) {
            if (cart[index] && cart[index].quantity > 1) {
                cart[index].quantity = parseInt(cart[index].quantity) - 1;
                cart[index].subtotal = cart[index].quantity * parseInt(cart[index].price);
                updateKeranjang();
            }
        }

        document.getElementById('form-transaksi').addEventListener('submit', function () {
            document.getElementById('cart-data').value = JSON.stringify(cart);
        });

        // Initialize
        updateKeranjang();
    </script>
</body>
</html>
