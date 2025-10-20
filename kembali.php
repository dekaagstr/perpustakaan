<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Proses pengembalian
if (isset($_GET['kembali'])) {
    $id_peminjaman = $_GET['kembali'];

    $koneksi->begin_transaction();
    try {
        // Ambil semua detail buku yang dipinjam
        $detail = $koneksi->query("SELECT id_buku, jumlah FROM detail_peminjaman WHERE id_peminjaman = $id_peminjaman");
        while ($row = $detail->fetch_assoc()) {
            $koneksi->query("UPDATE buku SET stok = stok + {$row['jumlah']} WHERE id_buku = {$row['id_buku']}");
        }

        // Tandai sebagai dikembalikan
        $koneksi->query("UPDATE peminjaman SET dikembalikan = 1 WHERE id_peminjaman = $id_peminjaman");

        $koneksi->commit();
        $success = "Pengembalian berhasil!";
    } catch (Exception $e) {
        $koneksi->rollback();
        $error = "Gagal mengembalikan: " . $e->getMessage();
    }
}

// Ambil daftar peminjaman yang belum dikembalikan
$data = $koneksi->query("
    SELECT p.id_peminjaman, a.nama, b.judul, dp.jumlah, p.tanggal_pinjam, p.tanggal_kembali
    FROM peminjaman p
    JOIN anggota a ON a.id_anggota = p.id_anggota
    JOIN detail_peminjaman dp ON dp.id_peminjaman = p.id_peminjaman
    JOIN buku b ON b.id_buku = dp.id_buku
    WHERE p.dikembalikan = 0
    ORDER BY p.tanggal_pinjam ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pengembalian Buku</title>
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
    <h3>Pengembalian Buku</h3>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Nama Anggota</th>
                <th>Judul Buku</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($data->num_rows == 0): ?>
                <tr><td colspan="6" class="text-center text-muted">Tidak ada buku yang perlu dikembalikan</td></tr>
            <?php else: while ($row = $data->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['judul'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td><?= $row['tanggal_pinjam'] ?></td>
                    <td><?= $row['tanggal_kembali'] ?></td>
                    <td>
                        <a href="kembali.php?kembali=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi pengembalian?')">Kembalikan</a>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
