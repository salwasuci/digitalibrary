<?php
session_start();
require_once "../lib/koneksi.php";

$db = new Database();
$conn = $db->conn;

$user_id = $_SESSION['user_id'];

// Proses hapus koleksi
if (isset($_GET['delete'])) {
    $koleksiID = $_GET['delete'];

    // Pastikan hanya menghapus koleksi milik user yang sedang login
    $hapus = $conn->prepare("DELETE FROM koleksipribadi WHERE KoleksiID = :id AND UserID = :user_id");
    $hapus->execute([
        'id' => $koleksiID,
        'user_id' => $user_id
    ]);

    // Redirect biar gak double delete pas refresh
    header("Location: koleksi_outputan.php");
    exit;
}

// Ambil data koleksi
$sql = "SELECT k.*, b.Judul, b.Penulis, b.gambar, c.NamaKategori, b.BukuID
        FROM koleksipribadi k
        JOIN buku b ON k.BukuID = b.BukuID
        LEFT JOIN kategoribuku_relasi r ON b.BukuID = r.BukuID
        LEFT JOIN kategoribuku c ON r.KategoriID = c.KategoriID
        WHERE k.UserID = :user_id";

$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$koleksi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NusaPustaka Library</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600&display=swap" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
     body { margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #fff; display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; height: 70px; }
        .navbar .logo img { height: 155px; object-fit: contain; margin-top: -54px; margin-bottom: -54px; margin-left: -30px; }
        .navbar .center-menu { display: flex; gap: 20px; }
        .navbar .center-menu a { text-decoration: none; color: #000; display: flex; align-items: center; font-weight: bold; transition: background-color 0.3s, color 0.3s; padding: 8px 15px; border-radius: 5px; }
        .navbar .center-menu a:hover { background-color:rgb(14, 42, 81); color: #fff; }
        .welcome-section { background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../asset/img/p.jpeg') no-repeat center center/cover; color: #fff; text-align: center; padding: 100px 20px; min-height: 523px; }
        .welcome-section h1 { margin-top: 75px; }
        .welcome-section p { margin-top: 20px; font-size: 1.2rem; font-weight: 700; line-height: 2; max-width: 800px; margin-left: auto; margin-right: auto; text-align: center; color: #fff; text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.7); border-top: 3px solid rgba(255, 255, 255, 0.8); border-bottom: 3px solid rgba(255, 255, 255, 0.8); padding: 15px 0; animation: fadeIn 1.5s ease-in-out; }

        .container-fluid.featurs { margin-top: -10px; }
        .featurs-item { text-align: center; background: #f9f9f9; border-radius: 10px; padding: 20px; }
        .featurs-item img { width: 100px; }
        .featurs-content h5 { margin-top: 15px; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
/*css kategori */
            .text-orange {
                color: #334155; /* Warna teks navy awal */
            }
            .btn.text-orange:hover {
                background-color: #334155; /* Background orange saat hover */
                color: #fff !important; /* Warna teks putih saat hover */
                border-color: #334155; /* Border menyesuaikan warna */
            }

            .btn.text-orange:hover i {
                color: #fff !important; /* Ikon berubah jadi putih saat hover */
            }
            .text-putih {
                color:rgb(255, 0, 0); 
            }
            .btn.text-putih:hover {
                background-color:rgb(229, 27, 40); /* Background orange saat hover */
                color: #fff !important; /* Warna teks putih saat hover */
                border-color:rgb(255, 0, 0); 
            }

            .btn.text-putih:hover i {
                color: #fff !important; /* Ikon berubah jadi putih saat hover */
            }

            .fruite .tab-class .nav-item a.active {
                background: var(--bs-secondary) !important;
            }

            .fruite .tab-class .nav-item a.active span {
                color: var(--bs-white) !important; 
            }

            .fruite .fruite-categorie .fruite-name {
                line-height: 40px;
            }

            .fruite .fruite-categorie .fruite-name a {
                transition: 0.5s;
            }

            .fruite .fruite-categorie .fruite-name a:hover {
                color: var(--bs-secondary);
            }

            .fruite .fruite-item {
                height: 100%;
                transition: 0.5s;
            }

            .fruite .fruite-item:hover {
                box-shadow: 0 0 55px rgba(0, 0, 0, 0.4);
            }

            .fruite .fruite-item .fruite-img {
                overflow: hidden;
                transition: 0.5s;
                border-radius: 10px 10px 0 0;
            }

            .fruite .fruite-item .fruite-img img {
                width: 100%; /* Supaya gambar memenuhi container */
                height: 350px; /* Atur tinggi gambar sesuai kebutuhan */
                object-fit: cover; /* Potong bagian berlebih biar gambar tetap proporsional */
                border-radius: 10px 10px 0 0;
                transition: 0.5s;
            }

            .fruite .fruite-item .fruite-img img:hover {
                transform: scale(1.3);
            }

            .kategori-judul {
                font-family: 'Poppins', sans-serif; /* Font modern dan bersih */
                font-size: 2rem; /* Ukuran lebih besar biar menonjol */
                font-weight: 700; /* Tebal biar lebih kuat kesannya */
                color: #334155; /* Warna abu tua untuk kesan profesional */
                letter-spacing: 1px; /* Jarak antar huruf untuk kesan premium */
                text-transform: uppercase; /* Biar makin tegas dan rapi */
                border-bottom: 3px solid #FF5722; /* Garis bawah warna orange biar eye-catching */
                display: inline-block; /* Biar garis bawah gak full lebar */
                padding-bottom: 5px; /* Jarak antara teks dan garis bawah */
            }

            /*** Fruits End ***/
        </style>
</head>
<body>
<div class="navbar">
        <div class="logo">
            <img src="../asset/img/logo.png">
        </div>

        <div class="center-menu">
            <a href="peminjam.php"><i class="bi bi-cursor-fill" style="margin-right: 8px; color: #ff6600; font-size: 1.4rem;"></i> Home</a>
            <a href="../logout.php"><i class="bi bi-person-vcard" style="margin-right: 8px; color: #ff6600; font-size: 1.4rem;"></i> Logout</a>
        </div>
    </div>

<div id="koleksi">
    <div class="container-fluid fruite py-2">
        <div class="container py-4">
            <div class="tab-class text-center">
                <div class="row g-2">
                    <div class="col-lg-4 text-start">
                        <h2 class="kategori-judul" style="font-size:25px; font-family: 'Fredoka', sans-serif;">Koleksi Buku Kamu</h2>
                    </div>
                    <div class="tab-content" style="margin-top:25px;">
                        <div id="tab-all" class="tab-pane fade show p-0 active">
                            <div class="row g-4">
                                <?php if (!empty($koleksi)) : ?>
                                    <?php foreach ($koleksi as $bk) : ?>
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <div class="rounded position-relative fruite-item">
                                                <div class="fruite-img">
                                                    <img src="/digitalibrary/asset/upload/<?= $bk['gambar'] ?>" 
                                                         class="img-fluid w-100 rounded-top" 
                                                         style="height: 350px; object-fit: cover;" 
                                                         alt="">
                                                </div>
                                                <div class="position-absolute d-flex justify-content-between align-items-center" 
                                                     style="top: 10px; left: 10px; right: 10px;">
                                                    <span class="text-white px-3 py-1 rounded kategori-label" style="background-color: #334155;">
                                                        <?= $bk['NamaKategori'] ?>
                                                    </span>
                                                </div>
                                                <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                    <h5 class="fw-bold"><?= $bk['Judul'] ?></h5>
                                                    <div class="book-info mb-3">
                                                        <span class="badge text-white me-1" style="background-color: #FF5722;">
                                                            <i class="fa fa-user me-1"></i>by: <?= $bk['Penulis'] ?>
                                                        </span>
                                                    </div>
                                                    <div class="row gx-2">
    <div class="col-6">
        <a href="minjam.php?id=<?= $bk['BukuID'] ?>" 
           class="btn border border-secondary rounded-pill w-100 text-orange fw-bold" 
           style="font-size: 0.8rem;">
            <i class="fa fa-book-open me-1 text-orange"></i> Pinjam
        </a>
    </div>
    <div class="col-6">
        <a href ="ulasan_inputan.php?id=<?= $bk['BukuID'] ?>" 
           class="btn border border-secondary rounded-pill w-100 text-orange fw-bold" 
           style="font-size: 0.8rem;">
            <i class="fa fa-comment-alt me-1 text-orange"></i> Ulasan
        </a>
    </div>
</div>

<div class="mt-2">
    <a href="koleksi_outputan.php?delete=<?= $bk['KoleksiID'] ?>" 
       onclick="return confirm('Yakin hapus dari koleksi?')" 
       class="btn border border-danger rounded-pill w-100 fw-bold text-danger text-putih" 
       style="font-size: 0.8rem;">
        <i class="fa fa-trash me-1 text-danger"></i> Hapus
    </a>
</div>
                                  </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="col-12">
                                        <div class="alert alert-info text-center">
                                            Belum ada buku di koleksimu.
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
</div>
</body
