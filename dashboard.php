<?php include 'koneksi.php'; ?>
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            background-image: url(cover/bg2.png);
            background-size: cover;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: sticky;
            top: 0;
            background-color: #555555;
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
    <h4 class="text-center">Admin Perpus</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="anggota.php">Anggota</a>
    <a href="buku.php">Buku</a>
    <a href="pinjam.php">Peminjaman</a>
    <a href="kembali.php">Pengembalian</a>
    <a href="laporan.php">Laporan</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Dashboard</h2>

    <?php
    // Jumlah anggota
    $anggota = $koneksi->query("SELECT COUNT(*) as total FROM anggota")->fetch_assoc()['total'];

    // Jumlah buku
    $buku = $koneksi->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];

    // Buku populer
    $populer = $koneksi->query("
        SELECT b.judul, SUM(dp.jumlah) as total_dipinjam
        FROM detail_peminjaman dp
        JOIN buku b ON b.id_buku = dp.id_buku
        GROUP BY b.id_buku
        ORDER BY total_dipinjam DESC
        LIMIT 1
    ")->fetch_assoc();

    // Buku terbaru
    $terbaru = $koneksi->query("
        SELECT judul, tahun_terbit 
        FROM buku 
        ORDER BY tahun_terbit DESC 
        LIMIT 1
    ")->fetch_assoc();
    ?>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Anggota</h5>
                    <p class="card-text fs-4"><?= $anggota ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Buku</h5>
                    <p class="card-text fs-4"><?= $buku ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Buku Populer</h5>
                    <p class="card-text"><?= $populer['judul'] ?? 'Belum ada' ?></p>
                    <small>Total dipinjam: <?= $populer['total_dipinjam'] ?? 0 ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Buku Terbaru</h5>
                    <p class="card-text"><?= $terbaru['judul'] ?? 'Belum ada' ?></p>
                    <small>Tahun: <?= $terbaru['tahun_terbit'] ?? '-' ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
