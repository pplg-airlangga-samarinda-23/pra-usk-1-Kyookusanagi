<?php
include '../config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$transaksi_id = intval($_GET['id']);

try {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    // Get transaction details to restore stock
    $get_details = "SELECT ProdukID, Jumlah FROM detail_transaksi WHERE TransaksiID = $transaksi_id";
    $details = mysqli_query($conn, $get_details);
    
    if (!$details) {
        throw new Exception("Error: " . mysqli_error($conn));
    }
    
    // Restore stock for each item
    while ($item = mysqli_fetch_assoc($details)) {
        $restore_stok = "UPDATE produk SET Stok = Stok + {$item['Jumlah']} WHERE ProdukID = {$item['ProdukID']}";
        if (!mysqli_query($conn, $restore_stok)) {
            throw new Exception("Gagal restore stok: " . mysqli_error($conn));
        }
    }
    
    // Delete detail transaksi
    $delete_detail = "DELETE FROM detail_transaksi WHERE TransaksiID = $transaksi_id";
    if (!mysqli_query($conn, $delete_detail)) {
        throw new Exception("Gagal hapus detail transaksi: " . mysqli_error($conn));
    }
    
    // Delete transaksi
    $delete_transaksi = "DELETE FROM transaksi WHERE TransaksiID = $transaksi_id";
    if (!mysqli_query($conn, $delete_transaksi)) {
        throw new Exception("Gagal hapus transaksi: " . mysqli_error($conn));
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    header("Location: index.php?success=Transaksi berhasil dihapus");
    exit;
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: index.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>
