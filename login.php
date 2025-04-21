<?php
session_start();

require_once "lib/koneksi.php";
require_once "lib/user.php";


$user = new user();
$message = "";
// Jika user sudah login, langsung arahkan ke dashboard sesuai role
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: modul/admin.php");
            break;
        case 'petugas':
            header("Location: modul/petugas.php");
            break;
        case 'peminjam':
            header("Location: modul/peminjam.php");
            break;
        default:
            header("Location: dashboard.php");
            break;
    }
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $message = $user->login($username, $password);
    if (strpos($message, "Login berhasil") !== false) {
        // Ambil role dari session setelah login berhasil
        $role = $_SESSION['role'] ?? '';

        switch ($role) {
            case 'admin':
                header("Location: modul/admin.php");
                break;
            case 'petugas':
                header("Location: modul/petugas.php");
                break;
            case 'peminjam':
                header("Location: modul/peminjam.php");
                break;
            default:
                header("Location: index.php"); // Redirect ke halaman default jika role tidak dikenali
                break;
        }
        exit();
    }
}
?>
     
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NusaPustaka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color:rgb(14, 42, 81); /* Warna hijau tua sebagai background */
        }
        .login-container {
            display: flex;
            background-color: #fff;
            border-radius: 20px;
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            box-shadow:  #ff8c00;
        }
        .login-form {
            flex: 1;
            padding: 50px;
        }
        .login-image {
            flex: 1;
            background: url('asset/img/p.jpeg') no-repeat center center/cover;
        }
        .login-form h3 {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .login-form .btn-primary {
            background-color: #ff8c00;
            border: none;
        }
        .login-form .btn-primary:hover {
            background-color: #ff6600;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h3 class="text-center">Login</h3>
            <?php if ($message): ?>
                <div class="alert alert-danger"> <?= $message; ?> </div>
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
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </div>
        <div class="login-image"></div>
    </div>
</body>
</html>
