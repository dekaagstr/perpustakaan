<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Tambah atau update buku
if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun = $_POST['tahun_terbit'];
    $stok = $_POST['stok'];

    // Cek apakah sudah ada buku yang sama
    $cek = $koneksi->query("SELECT * FROM buku WHERE judul='$judul' AND pengarang='$pengarang'")->fetch_assoc();

    if ($cek) {
        // update stok
        $id = $cek['id_buku'];
        $koneksi->query("UPDATE buku SET stok = stok + $stok WHERE id_buku = $id");
    } else {
        // Upload cover dengan validasi
        $cover = null;
        if ($_FILES['cover']['name']) {
            $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
            // Cek ekstensi file
            if (in_array(strtolower($ext), $allowed)) {
                $cover = 'cover/' . time() . '_' . $_FILES['cover']['name'];
                if (!move_uploaded_file($_FILES['cover']['tmp_name'], $cover)) {
                    // Jika gagal upload
                    echo "Error uploading file.";
                    exit;
                }
            } else {
                echo "Only image files are allowed.";
                exit;
            }
        }

        // insert buku baru
        $koneksi->query("INSERT INTO buku (judul, pengarang, tahun_terbit, stok, cover) 
                         VALUES ('$judul', '$pengarang', '$tahun', $stok, '$cover')");
    }

    header("Location: buku.php");
}

// Edit buku
if (isset($_POST['edit'])) {
    $id = $_POST['id_buku'];
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun = $_POST['tahun_terbit'];
    $stok = $_POST['stok'];

    // upload cover baru jika ada
    $updateCover = "";
    if ($_FILES['cover']['name']) {
        $cover = 'cover/' . time() . '_' . $_FILES['cover']['name'];
        move_uploaded_file($_FILES['cover']['tmp_name'], $cover);
        $updateCover = ", cover='$cover'";
    }

    $koneksi->query("UPDATE buku SET judul='$judul', pengarang='$pengarang', tahun_terbit='$tahun', stok=$stok $updateCover WHERE id_buku=$id");
    header("Location: buku.php");
}

// Hapus buku
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM buku WHERE id_buku=$id");
    header("Location: buku.php");
}

// Ambil data untuk form edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_data = $koneksi->query("SELECT * FROM buku WHERE id_buku=$id")->fetch_assoc();
}

// Pencarian
$keyword = $_GET['search'] ?? '';
$where = $keyword ? "WHERE judul LIKE '%$keyword%' OR pengarang LIKE '%$keyword%'" : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Buku</title>
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
        img.cover {
            height: 100px;
            object-fit: cover;
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
    <h3>Data Buku</h3>

    <!-- Form Pencarian -->
    <form class="mb-3" method="get">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari judul atau pengarang..." value="<?= $keyword ?>">
            <button class="btn btn-outline-secondary">Cari</button>
        </div>
    </form>

    <!-- Form Tambah/Edit -->
    <form method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
        <input type="hidden" name="id_buku" value="<?= $edit_data['id_buku'] ?? '' ?>">
        <div class="col-md-3">
            <input type="text" name="judul" class="form-control" placeholder="Judul" required value="<?= $edit_data['judul'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <input type="text" name="pengarang" class="form-control" placeholder="Pengarang" required value="<?= $edit_data['pengarang'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="tahun_terbit" class="form-control" placeholder="Tahun" required value="<?= $edit_data['tahun_terbit'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="stok" class="form-control" placeholder="Stok" required value="<?= $edit_data['stok'] ?? 1 ?>">
        </div>
        <div class="col-md-2">
            <input type="file" name="cover" class="form-control">
        </div>
        <div class="col-md-12">
            <?php if ($edit_data): ?>
                <button name="edit" class="btn btn-warning">Update</button>
                <a href="buku.php" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button name="tambah" class="btn btn-primary">Tambah</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Tabel Buku -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Cover</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Tahun</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $result = $koneksi->query("SELECT * FROM buku $where ORDER BY id_buku DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <?php if ($row['cover']): ?>
                        <img src="<?= $row['cover'] ?>" class="cover">
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td><?= $row['judul'] ?></td>
                <td><?= $row['pengarang'] ?></td>
                <td><?= $row['tahun_terbit'] ?></td>
                <td><?= $row['stok'] ?></td>
                <td>
                    <a href="buku.php?edit=<?= $row['id_buku'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="buku.php?hapus=<?= $row['id_buku'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus buku ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
