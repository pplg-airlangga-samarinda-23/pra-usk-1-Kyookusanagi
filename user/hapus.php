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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$user_id = intval($_GET['id']);

// Prevent deleting own account
if ($user_id == $user['UserID']) {
    header("Location: index.php?error=Tidak bisa menghapus akun sendiri");
    exit;
}

// Check if user exists
$target_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE UserID = $user_id"));

if (!$target_user) {
    header("Location: index.php?error=User tidak ditemukan");
    exit;
}

// Delete user
$delete = "DELETE FROM user WHERE UserID = $user_id";

if (mysqli_query($conn, $delete)) {
    header("Location: index.php?success=User berhasil dihapus");
    exit;
} else {
    header("Location: index.php?error=" . urlencode("Gagal menghapus user: " . mysqli_error($conn)));
    exit;
}
?>
