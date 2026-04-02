<?php
$conn = mysqli_connect("localhost", "root", "", "kasir");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

session_start();
?>