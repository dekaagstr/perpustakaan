<?php
$host     = "localhost";
$user     = "root";         // ganti kalau username berbeda
$password = "";             // ganti kalau pakai password
$database = "perpustakaan";

// Membuat koneksi
$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
