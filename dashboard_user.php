<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login_user.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: sticky;
            top: 0;
            background-color: #198754;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #157347;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center">Halo, <?= htmlspecialchars($user['nama']) ?></h4>
    <a href="dashboard_user.php">Dashboard</a>
    <a href="buku_user.php">Lihat Buku</a>
    <a href="pinjam_user.php">Pinjam Buku</a>
    <a href="kembali_user.php">Pengembalian</a>
    <a href="riwayat_user.php">Riwayat</a>
    <a href="logout_user.php" class="text-danger">Logout</a>
</div>

<!-- Content -->
<div class="content">
    <h3>Selamat Datang di Dashboard User</h3>
    <p>Anda login sebagai <strong><?= htmlspecialchars($user['nama']) ?></strong>. Gunakan menu di samping untuk mengakses fitur perpustakaan.</p>
</div>

</body>
</html>
