<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $koneksi->query("SELECT * FROM anggota WHERE username='$username' AND password='$password'");

    if ($query->num_rows > 0) {
        $user = $query->fetch_assoc();
        $_SESSION['user'] = $user;
        header("Location: dashboard_user.php");
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 400px;
            padding: 30px;
            border-radius: 15px;
        }
    </style>
</head>
<body>

<div class="card shadow">
    <h3 class="mb-4 text-center">Login User</h3>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button name="login" class="btn btn-success w-100">Login</button>
    </form>
    <p class="mt-3 text-center">
    Buat Akun <a href="register_user.php">Klik di sini</a>
</p>

</div>

</body>
</html>
