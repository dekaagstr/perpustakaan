<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: sticky;
            top: 0;
            background-color: #343a40;
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
            background-color: #495057;
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
    <h4 class="text-center">User Perpus</h4>
    <a href="dashboard_user.php">Dashboard</a>
    <a href="anggota_user.php">Anggota</a>
    <a href="buku_user.php">Buku</a>
    <a href="pinjam_user.php">Peminjaman</a>
    <a href="kembali_user.php">Pengembalian</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Content -->
<div class="content">
    <h3>Data Anggota</h3>

    <!-- Tabel Anggota -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $result = $koneksi->query("SELECT * FROM anggota ORDER BY id_anggota DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['alamat']) ?></td>
                <td><?= $row['tanggal_daftar'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
