<?php
include '../config.php';
$id = mysqli_real_escape_string($conn, $_GET['id']);

mysqli_query($conn, "DELETE FROM produk WHERE ProdukID=$id");
header("Location: index.php");
exit;