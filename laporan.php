<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Laporan per anggota (hanya yang sudah dikembalikan)
$laporan_anggota = $koneksi->query("
    SELECT a.nama, COUNT(p.id_peminjaman) AS jumlah_transaksi, SUM(dp.jumlah) AS total_buku
    FROM anggota a
    JOIN peminjaman p ON a.id_anggota = p.id_anggota
    JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
    WHERE p.dikembalikan = 1
    GROUP BY a.id_anggota
");

// Buku populer
$buku_populer = $koneksi->query("
    SELECT b.judul, SUM(dp.jumlah) AS total_dipinjam
    FROM detail_peminjaman dp
    JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
    JOIN buku b ON b.id_buku = dp.id_buku
    WHERE p.dikembalikan = 1
    GROUP BY dp.id_buku
    ORDER BY total_dipinjam DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex;
            background-image: url(cover/bg2.png);
            background-size: cover;
        }
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

<div class="sidebar">
    <h4 class="text-center">Admin Perpus</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="anggota.php">Anggota</a>
    <a href="buku.php">Buku</a>
    <a href="pinjam.php">Peminjaman</a>
    <a href="kembali.php">Pengembalian</a>
    <a href="laporan.php">Laporan</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<div class="content">
    <h3>Laporan Peminjaman</h3>

    <h5 class="mt-4">📋 Peminjaman per Anggota</h5>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Nama Anggota</th>
                <th>Jumlah Transaksi</th>
                <th>Total Buku Dipinjam</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($laporan_anggota->num_rows == 0): ?>
                <tr><td colspan="3" class="text-center text-muted">Belum ada data</td></tr>
            <?php else: while ($row = $laporan_anggota->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['jumlah_transaksi'] ?></td>
                    <td><?= $row['total_buku'] ?></td>
                </tr>
            <?php endwhile; endif; ?>
        </tbody>
    </table>

    <h5 class="mt-4">📚 Buku Paling Sering Dipinjam</h5>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Judul Buku</th>
                <th>Total Dipinjam</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($buku_populer->num_rows == 0): ?>
                <tr><td colspan="2" class="text-center text-muted">Belum ada data</td></tr>
            <?php else: while ($row = $buku_populer->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['judul'] ?></td>
                    <td><?= $row['total_dipinjam'] ?></td>
                </tr>
            <?php endwhile; endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
