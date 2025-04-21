<?php
session_start();
require_once "../lib/koneksi.php"; // Koneksi ke database

// Cek apakah user sudah login dan sebagai petugas
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #f0f2f5;
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
    width: 240px;
    background: #1E293B;
    color: #fff;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    overflow-y: auto;   /* Tambahin ini biar bisa scroll */
    max-height: 100vh;  /* Batas tinggi maksimal */
}
.sidebar::-webkit-scrollbar {
    width: 8px;
}

.sidebar::-webkit-scrollbar-thumb {
    background-color: #FF5722;
    border-radius: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background-color: #334155;
}

        .sidebar .profile img {
            width: 100px;
            border-radius: 50%;
            border: 4px solid #fff;
            margin-bottom: 10px;
        }

        .sidebar a {
            text-decoration: none;
            color: #fff;
            padding: 12px 15px;
            border-radius: 6px;
            background: #334155;
            transition: background 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            background: #FF5722;
        }

        .content {
    flex-grow: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    overflow-y: auto;  /* Tambahkan ini untuk fitur scroll */
    max-height: 100vh;  /* Batas tinggi maksimal agar tidak melewati viewport */
}

        .header {
            background: #FF5722;
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-weight: bold;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    cursor: pointer;
}
.activity-log {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    margin-top: 26px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.activity-log:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    cursor: pointer;
}

    </style>
</head>
<body>
<div class="sidebar">
    <div class="profile text-center">
        <img src="../asset/img/user.png" alt="Petugas">
        <h3><?= isset($_SESSION['username']) ? $_SESSION['username'] : 'admin'; ?></h3>
    </div>

    <a href="?page=dashboard" class="active">Dashboard</a>
    <a href="?page=pengguna">Petugas</a>
    <a href="?page=minjam">Peminjam Buku</a>
    <a href="?page=buku">Kelola Buku</a>
    <a href="?page=kategori">Kategori Buku</a>
    <a href="?page=ulasan">Ulasan Buku</a>
    <a href="?page=denda">Denda Buku</a>
    <a href="../logout.php">Logout</a>
</div>

<div class='content'>
    <div class='header'>Dashboard Admin - Laporan Umum</div>

    <main>
        <?php
        if (isset($_GET['page'])) {
            $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
            switch ($page) {
                case "dashboard":
                    include "dashboard.php";
                    break;
               case "denda":
                    include "denda.php";
                    break;
                case "pengguna":
                    include "pengguna.php";
                    break;
                case "kategori":
                    include "kategoribuku.php";
                    break;
                case "buku":
                    include "buku.php";
                    break;
                 case "updatebuku":
                    include "update_buku.php";
                    break;
                case "ulasan":
                    include "ulasan_output.php";
                    break;
                case "relasi":
                    include "kategoribuku2.php";
                    break;
                case "update":
                    include "update_pengguna.php";
                    break;
                case "minjam":
                    include "minjam2.php";
                    break;
                default:
                    echo "<p>Halaman tidak ditemukan!</p>";
                    break;
            }
        }else
        {
        include "dashboard.php";
    }
        ?>
    </main>
</div>
</body>
</html>
