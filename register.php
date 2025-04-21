<?php
require_once "lib/koneksi.php";
require_once "lib/user.php";

$user = new User();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $email = $_POST["email"];
    $namaLengkap = $_POST["namaLengkap"];
    $alamat = $_POST["alamat"];

    $message = $user->registerPeminjam($username, $password, $confirm_password, $email, $namaLengkap, $alamat);


    // Jika registrasi berhasil, arahkan ke halaman login
    if ($message === "Registrasi peminjam berhasil!") {
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Peminjam - NusaPustaka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
body {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgb(14, 42, 81);
}

.card {
    display: flex;
    flex-direction: row;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: rgb(14, 42, 81);
    max-width: 800px; /* Diperkecil jadi 800px */
    width: 100%;
}

.form-section {
    padding: 15px; /* Padding lebih kecil agar tidak boros ruang */
    flex: 1;
    font-size: 12px; /* Teks tetap kecil */
}

.form-control {
    font-size: 12px;
    padding: 4px 8px;
    height: 28px; /* Tinggi input sedikit dikurangi */
    margin-bottom: 6px; /* Jarak antar input lebih rapat */
}

.image-section {
    background: url('asset/img/p.jpeg') no-repeat center center/cover;
    width: 45%; /* Gambar sedikit lebih kecil */
    height: auto;
}

button.btn {
    padding: 5px;
    font-size: 12px;
    background-color: #ff8c00; /* Warna oranye */
    border: none; /* Menghilangkan border agar lebih clean */
    color: #fff; /* Warna teks putih agar kontras */
}

button.btn:hover {
    background-color: #ff6600;
}


@media (max-width: 768px) {
    .card {
        flex-direction: column;
        max-width: 100%;
    }
    .image-section {
        height: 150px;
        width: 100%;
    }
}

    </style>
</head>
<body>
    <div class="card">
        <div class="form-section">
            <h3 class="text-center">Registrasi</h3>
            <?php if ($message && $message !== "Registrasi peminjam berhasil!"): ?>
                <div class="alert alert-danger"> <?= htmlspecialchars($message); ?> </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="namaLengkap" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Daftar</button>
            </form>
            <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
        <div class="image-section"></div>
    </div>
</body>
</html>