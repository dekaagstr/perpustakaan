<?php
session_start();
include 'koneksi.php';

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $tanggal = date('Y-m-d');

    // Cek apakah username sudah digunakan
    $cek = $koneksi->query("SELECT * FROM anggota WHERE username='$username'");
    if ($cek->num_rows > 0) {
        $error = "Username sudah digunakan. Silakan pilih yang lain.";
    } else {
        $koneksi->query("INSERT INTO anggota (nama, alamat, username, password, tanggal_daftar) 
                         VALUES ('$nama', '$alamat', '$username', '$password', '$tanggal')");

        // Login otomatis setelah registrasi
        $user = $koneksi->query("SELECT * FROM anggota WHERE username='$username'")->fetch_assoc();
        $_SESSION['user'] = $user;
        header("Location: dashboard_user.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #dee2e6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 450px;
            padding: 30px;
            border-radius: 15px;
        }
    </style>
</head>
<body>

<div class="card shadow">
    <h3 class="mb-4 text-center">Registrasi User</h3>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button name="register" class="btn btn-primary w-100">Daftar</button>
    </form>
    <p class="mt-3 text-center"> <a href="login_user.php">Kembali ke login</a></p>
</div>

</body>
</html>
