<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Tambah Anggota
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $tanggal = date('Y-m-d');
    $koneksi->query("INSERT INTO anggota (nama, alamat, tanggal_daftar) VALUES ('$nama', '$alamat', '$tanggal')");
    header("Location: anggota.php");
}

// Update Anggota
if (isset($_POST['edit'])) {
    $id = $_POST['id_anggota'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $koneksi->query("UPDATE anggota SET nama='$nama', alamat='$alamat' WHERE id_anggota=$id");
    header("Location: anggota.php");
}

// Hapus Anggota
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Cek apakah anggota pernah meminjam
    $cek_peminjaman = $koneksi->query("SELECT * FROM peminjaman WHERE id_anggota=$id");

    if ($cek_peminjaman->num_rows > 0) {
        // Tidak boleh hapus anggota yang sudah pernah meminjam
        echo "<script>alert('Tidak bisa menghapus! Anggota sudah memiliki riwayat peminjaman.'); window.location='anggota.php';</script>";
    } else {
        $koneksi->query("DELETE FROM anggota WHERE id_anggota=$id");
        header("Location: anggota.php");
    }
}

// Ambil data untuk edit jika ada
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_data = $koneksi->query("SELECT * FROM anggota WHERE id_anggota=$id")->fetch_assoc();
}
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
    <h4 class="text-center">Admin Perpus</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="anggota.php">Anggota</a>
    <a href="buku.php">Buku</a>
    <a href="pinjam.php">Peminjaman</a>
    <a href="kembali.php">Pengembalian</a>
    <a href="laporan.php">Laporan</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Content -->
<div class="content">
    <h3>Data Anggota</h3>

    <!-- Form Tambah/Edit -->
    <form method="POST" class="row g-3 mb-4">
        <input type="hidden" name="id_anggota" value="<?= $edit_data['id_anggota'] ?? '' ?>">
        <div class="col-md-4">
            <input type="text" name="nama" class="form-control" placeholder="Nama" required value="<?= $edit_data['nama'] ?? '' ?>">
        </div>
        <div class="col-md-4">
            <input type="text" name="alamat" class="form-control" placeholder="Alamat" required value="<?= $edit_data['alamat'] ?? '' ?>">
        </div>
        <div class="col-md-4">
            <?php if ($edit_data): ?>
                <button name="edit" class="btn btn-warning">Update</button>
                <a href="anggota.php" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button name="tambah" class="btn btn-primary">Tambah</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Tabel Anggota -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
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
                <td><?= $row['nama'] ?></td>
                <td><?= $row['alamat'] ?></td>
                <td><?= $row['tanggal_daftar'] ?></td>
                <td>
                    <a href="anggota.php?edit=<?= $row['id_anggota'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="anggota.php?hapus=<?= $row['id_anggota'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
