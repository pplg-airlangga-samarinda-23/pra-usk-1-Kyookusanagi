<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'kasir2';

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($koneksi, 'utf8mb4');

$userTableSql = "CREATE TABLE IF NOT EXISTS `user` (
    `UserID` int NOT NULL AUTO_INCREMENT,
    `Username` varchar(50) NOT NULL,
    `Password` varchar(255) NOT NULL,
    `Role` enum('admin','petugas') NOT NULL DEFAULT 'petugas',
    PRIMARY KEY (`UserID`),
    UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

mysqli_query($koneksi, $userTableSql);

$result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM `user`");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    if (isset($row['total']) && $row['total'] == 0) {
        mysqli_query($koneksi, "INSERT INTO `user` (Username, Password, Role) VALUES
            ('admin', MD5('admin'), 'admin'),
            ('petugas', MD5('petugas'), 'petugas')");
    }
}
