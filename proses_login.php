<?php
include 'config.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = md5($_POST['password']);

$query = mysqli_query($conn, "SELECT * FROM user WHERE Username='$username' AND Password='$password'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    $_SESSION['user'] = $data;
    header("Location: dashboard.php");
} else {
    session_start();
    $_SESSION['error'] = "Username atau password salah!";
    header("Location: login.php");
}