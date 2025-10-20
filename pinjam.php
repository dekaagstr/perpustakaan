<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Proses peminjaman
if (isset($_POST['pinjam'])) {
    $id_anggota = $_POST['id_anggota'];
    $id_buku = $_POST['id_buku'];
    $jumlah = (int)$_POST['jumlah'];
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+7 days'));

    $koneksi->begin_transaction();
    try {
        // Insert peminjaman
        $koneksi->query("INSERT INTO peminjaman (id_anggota, tanggal_pinjam, tanggal_kembali)
                         VALUES ($id_anggota, '$tanggal_pinjam', '$tanggal_kembali')");
        $id_peminjaman = $koneksi->insert_id;

        // Insert detail peminjaman
        $koneksi->query("INSERT INTO detail_peminjaman (id_peminjaman, id_buku, jumlah)
                         VALUES ($id_peminjaman, $id_buku, $jumlah)");

        // Update stok buku
        $koneksi->query("UPDATE buku SET stok = stok - $jumlah WHERE id_buku = $id_buku");

        $koneksi->commit();
        $success = "Peminjaman berhasil!";
    } catch (Exception $e) {
        $koneksi->rollback();
        $error = "Gagal meminjam buku: " . $e->getMessage();
    }
}

// Data untuk dropdown
$anggota = $koneksi->query("SELECT * FROM anggota ORDER BY nama");
$buku = $koneksi->query("SELECT * FROM buku WHERE stok > 0 ORDER BY judul");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Peminjaman Buku</title>
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
    <h3>Peminjaman Buku</h3>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Pilih Anggota</label>
            <select name="id_anggota" class="form-control" required>
                <option value="">-- Pilih --</option>
                <?php while ($a = $anggota->fetch_assoc()): ?>
                    <option value="<?= $a['id_anggota'] ?>"><?= $a['nama'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Pilih Buku</label>
            <select name="id_buku" class="form-control" required>
                <option value="">-- Pilih --</option>
                <?php while ($b = $buku->fetch_assoc()): ?>
                    <option value="<?= $b['id_buku'] ?>"><?= $b['judul'] ?> (Stok: <?= $b['stok'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>
        <div class="col-md-2">
            <label class="form-label d-block">&nbsp;</label>
            <button name="pinjam" class="btn btn-primary w-100">Pinjam</button>
        </div>
    </form>

    <!-- Riwayat Peminjaman (belum dikembalikan) -->
    <h5>Riwayat Peminjaman</h5>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $data = $koneksi->query("
                SELECT a.nama, b.judul, dp.jumlah, p.tanggal_pinjam, p.tanggal_kembali 
                FROM peminjaman p
                JOIN anggota a ON a.id_anggota = p.id_anggota
                JOIN detail_peminjaman dp ON dp.id_peminjaman = p.id_peminjaman
                JOIN buku b ON b.id_buku = dp.id_buku
                ORDER BY p.id_peminjaman DESC
            ");
            while ($row = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['judul'] ?></td>
                <td><?= $row['jumlah'] ?></td>
                <td><?= $row['tanggal_pinjam'] ?></td>
                <td><?= $row['tanggal_kembali'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
