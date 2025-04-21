<?php
// Menghubungkan ke database
require_once "lib/koneksi.php";

$db = new Database(); // Buat objek koneksi
$conn = $db->conn;    // Ambil koneksi dari properti class
// Cek apakah pengguna sudah login
session_start();
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: modul/admin.php");
            exit();
        case 'petugas':
            header("Location: modul/petugas.php");
            exit();
        case 'peminjam':
            header("Location: modul/peminjam.php");
            exit();
    }
}
// Ambil data kategori dari database
$stmtKategori = $conn->query("SELECT * FROM kategoribuku");
$kategori = $stmtKategori->fetchAll(PDO::FETCH_ASSOC);

// Ambil data buku beserta kategorinya
$stmtBuku = $conn->query("SELECT buku.*, kategoribuku.NamaKategori 
                         FROM buku 
                         JOIN kategoribuku_relasi ON buku.BukuID = kategoribuku_relasi.BukuID 
                         JOIN kategoribuku ON kategoribuku_relasi.KategoriID = kategoribuku.KategoriID");
$buku = $stmtBuku->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NusaPustaka Library</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .navbar {
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            height: 70px;
        }

        .navbar .logo img {
            height: 155px;
            object-fit: contain;
            margin-top: -54px;
            margin-bottom: -54px;
            margin-left: -30px;
        }

        .navbar .center-menu {
            display: flex;
            gap: 20px;
        }

      .navbar .center-menu a {
    text-decoration: none;
    color: #000;
    display: flex;
    align-items: center;
    font-weight: bold;
    transition: background-color 0.3s, color 0.3s;
    padding: 8px 15px;
    border-radius: 5px;
}

.navbar .center-menu a:hover {
    background-color:rgb(14, 42, 81);
    color: #fff;
}
        .navbar .menu {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .dropdown-content a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: #000;
        }

        .menu:hover .dropdown-content {
            display: block;
        }

        .welcome-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('asset/img/p.jpeg') no-repeat center center/cover;
            color: #fff;
            text-align: center;
            padding: 100px 20px;
            min-height: 523px;
        }

        .welcome-section h1 {
            margin-top: 75px;
        }
        .welcome-section p {
    margin-top: 20px; 
    font-size: 2rem; 
    font-weight: 700; 
    line-height: 2; 
    max-width: 800px; 
    margin-left: auto; 
    margin-right: auto; 
    text-align: center;
    color: #fff; /* Warna putih */
    text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.7); /* Bayangan lebih tebal untuk kontras */
    border-top: 3px solid rgba(255, 255, 255, 0.8);
    border-bottom: 3px solid rgba(255, 255, 255, 0.8);
    padding: 15px 0;
    animation: fadeIn 1.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
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
            /*css ulasan buku */
            .carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(0, 0, 0, 0.5); /* kasih background biar kelihatan */
    border-radius: 50%;
    padding: 20px;
    background-size: 100%, 100%;
}
            .testimonial-card {
        background: linear-gradient(145deg, #fff3e0, #ffffff);
        border-radius: 18px;
        padding: 30px;
        border: 1px solid #FF5722;
        box-shadow: 0 8px 20px rgba(255, 111, 0, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        position: relative;
    }

    .testimonial-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(255, 87, 34, 0.25);
    }

    .testimonial-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .avatar {
        width: 45px;
        height: 45px;
        background-color: #FFE0B2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #FF5722;
        font-size: 1.2rem;
    }

    .user-info {
        font-weight: 600;
        color: #FF5722;
        font-size: 1rem;
    }

    .judul-buku {
        color: #333;
        font-size: 0.95rem;
    }

    .ulasan-text {
        color: #555;
        font-style: italic;
        margin-bottom: 10px;
    }

    .rating {
        color: #FFD700;
        font-size: 1.1rem;
        margin-bottom: 5px;
    }

    /*css galerry buku */
    .masonry-gallery {
    column-count: 3;
    column-gap: 1rem;
    margin-top:15px;
  }

  .masonry-gallery img {
    width: 100%;
    margin-bottom: 1rem;
    border-radius: 12px;
    break-inside: avoid;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
  }

  .masonry-gallery img:hover {
    transform: scale(1.03);
  }

  @media (max-width: 768px) {
    .masonry-gallery {
      column-count: 2;
    }
  }

  @media (max-width: 576px) {
    .masonry-gallery {
      column-count: 1;
    }
  }
    /*css panah */
  .scroll-top-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 999;
  background-color: #FF5722;
  color: white;
  padding: 10px 12px;
  border-radius: 50%;
  font-size: 20px;
  text-align: center;
  text-decoration: none;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
  display: none;
}
.scroll-top-btn:hover {
  background-color: #e64a19;
  transform: scale(1.1);
  transition: all 0.3s ease;
}
.btn-orange {
    width: 185px;
    background-color: #FF5722;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px;
    cursor: pointer;
    transition: transform 0.2s ease, background-color 0.2s ease;
}
.btn-orange:hover {
    background-color: #FF4500;
    transform: scale(1.05); /* efek zoom pas hover */
    color:#fff;
}

.btn-orange:active {
    transform: scale(0.98); /* efek sedikit mengecil pas diklik */
}
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="asset/img/logo.png">
        </div>

        <div class="center-menu">
            <a href="login.php">
                <i class="bi bi-cursor-fill" style="margin-right: 8px; color: #ff6600; font-size: 1.4rem;"></i> Login
            </a>
            <a href="register.php">
                <i class="bi bi-person-vcard" style="margin-right: 8px; color: #ff6600; font-size: 1.4rem;"></i> Daftar
            </a>
        </div>
</div>

    <div class="welcome-section">
    <h1>Welcome to NusaPustaka Library</h1>
    <p style="margin-top: 20px; font-size: 1.2rem;">
        "Temukan buku favoritmu, pinjam dengan mudah, dan nikmati perjalanan membaca tanpa batas!"
    </p>
</div>

    </div>
    <!-- konten1 -->
<div id="tentang">
<section class="py-5 bg-light" id="tentang">
  <div class="container">
    <div class="text-center mb-5">
    <h2 class="kategori-judul" style="font-size: 25px;font-family: 'Fredoka', sans-serif;">Tentang NusaPustaka</h2>
         </div>

    <div class="row g-4">
      <!-- Card 1 -->
      <div class="col-md-4">
        <div class="card h-100 shadow border-0">
          <div class="card-body text-center">
            <i class="bi bi-book-half" style="color: #FF5722; font-size: 2.5rem; margin-bottom: 1rem;"></i>
            <h5 class="card-title fw-semibold" style="color: #FF5722;">Koleksi Buku Lengkap</h5>
            <p class="card-text text-muted">Ribuan buku dari berbagai genre dan bidang pengetahuan tersedia untuk semua usia.</p>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-md-4">
        <div class="card h-100 shadow border-0">
          <div class="card-body text-center">
            <i class="bi bi-wifi" style="color: #FF5722; font-size: 2.5rem; margin-bottom: 1rem;"></i>
            <h5 class="card-title fw-semibold" style="color: #FF5722;">Akses Digital</h5>
            <p class="card-text text-muted">Nikmati fasilitas e-book, jurnal digital, dan akses perpustakaan daring 24/7.</p>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col-md-4">
        <div class="card h-100 shadow border-0">
          <div class="card-body text-center">
            <i class="bi bi-people" style="color: #FF5722; font-size: 2.5rem; margin-bottom: 1rem;"></i>
            <h5 class="card-title fw-semibold" style="color: #FF5722;">Ruang Baca Nyaman</h5>
            <p class="card-text text-muted">Suasana tenang, kursi empuk, dan pencahayaan ideal untuk kamu yang ingin fokus belajar.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</div>

<!-- Kategori-->
<div id="kategori">
<div class="container-fluid fruite py-2">
    <div class="container py-4">
        <div class="tab-class text-center">
            <div class="row g-2">
            <div class="col-lg-4 text-start">
    <h2 class="kategori-judul" style="font-size:25px; font-family: 'Fredoka', sans-serif;">Kategori Buku</h2>
</div>
            <div class="tab-content">
                <div id="tab-all" class="tab-pane fade show p-0 active">
                    <div class="row g-4">
                        <?php foreach ($buku as $bk) : ?>
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
    
    <!-- Kategori dengan background -->
    <span class="text-white px-3 py-1 rounded kategori-label" style="background-color: #334155;">
        <?= $bk['NamaKategori'] ?>
    </span>
</div>

                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                        <h5 class="fw-bold"><?= $bk['Judul'] ?></h5>
                                        <div class="book-info mb-3">
        <span class="badge   text-white me-1" style="background-color: #FF5722;">
            <i class="fa fa-user me-1"></i>by: <?= $bk['Penulis'] ?>
        </span>
    </div>
    <div class="d-flex justify-content-center gap-2">
    <a href="login.php?id=<?= $bk['BukuID'] ?>" class="btn border border-secondary rounded-pill px-2 py-1 text-orange fw-bold" style="font-size: 0.8rem; white-space: nowrap;">
        <i class="fa fa-book-open me-1 text-orange"></i> Pinjam Sekarang
    </a>
    <a href="login.php?id=<?= $bk['BukuID'] ?>" class="btn border border-secondary rounded-pill px-2 py-1 text-orange fw-bold" style="font-size: 0.8rem; white-space: nowrap;">
        <i class="fa fa-comment-alt me-1 text-orange"></i> Beri Ulasan
    </a>
</div>
<div class="d-flex justify-content-center mt-2">
<?php if (isset($_SESSION['user_id'])): ?>
         <form action="koleksi.php" method="post">
            <input type="hidden" name="buku_id" value="<?= $bk['BukuID'] ?>">
            <button type="submit" class="btn border border-secondary rounded-pill px-2 py-1 text-orange fw-bold" style="font-size: 0.8rem;">
                <i class="fa fa-bookmark me-1 text-orange"></i> Tambah ke Koleksi
            </button>
        </form>
    <?php else: ?>
        <a href="login.php" class="btn border border-secondary rounded-pill px-2 py-1 text-orange fw-bold" style="font-size: 0.8rem;">
            <i class="fa fa-bookmark me-1 text-orange"></i> Tambah ke Koleksi
        </a>
           <?php endif; ?>
           </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 </div>
 <!-- konten2 -->
<section class="text-white d-flex align-items-center" style="min-height: 50vh; background: linear-gradient(to right, #1e2a38, #ff6f00); margin-left:-18px; width:101%; margin-top:15px;">
  <div class="container">
    <div class="row align-items-center">
      <!-- Text Content -->
      <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
      <h1 class="fs-2 fw-bold mb-2" style="text-shadow: 1px 1px 4px rgba(0,0,0,0.4);">
  Pinjam Buku, Mulai Petualanganmu di <span style="color: #FF5722;">NusaPustaka</span>
</h1>
<p class="small" style="line-height: 1.6;">
Koleksi dulu, pinjam nanti! Semua buku favoritmu bisa kamu simpan di sini dulu sebelum dibawa pulang dari NusaPustaka.
</p>
        <a href="login.php" class="btn btn-orange text-white fw-semibold px-3 py-1 mt-2 shadow-sm">
  Koleksi Pribadi <i class="bi bi-book ms-2"></i>
</a>
      </div>
      
      <!-- Image / Illustration -->
      <div class="col-lg-6 text-center" data-aos="fade-left" data-aos-duration="1000">
        <img src="https://cdn-icons-png.flaticon.com/512/3330/3330310.png" alt="Library Illustration" class="img-fluid rounded" style="max-height: 220px; margin-left: 250px;">
      </div>
    </div>
  </div>
</section>

<!-- galerry -->
<div id="galerry">
<section class="py-5" style="background-color: #f8fafc;">
  <div class="container">
    <div class="masonry-gallery">
      <img src="asset/img/p1.jpeg" alt="img1">
      <img src="asset/img/p5.jpeg" alt="img2">
      <img src="asset/img/p3.jpeg" alt="img3">
      <img src="asset/img/p4.jpeg" alt="img4">
      <img src="asset/img/p1.jpeg" alt="img5" style="height:300px;">
      <img src="asset/img/p2.jpeg" alt="img6">
      <img src="asset/img/p5.jpeg" alt="img7">
    </div>
  </div>
</section>
  </div>
     </div>

 
<!-- Ulasan Buku -->
<div id="ulasan">
<div class="container py-5 position-relative">
<div class="text-center mb-4">
    <h2 class="kategori-judul" style="font-size: 25px; font-family: 'Fredoka', sans-serif; margin-top:-15px;">💬 Apa Kata Pembaca?</h2>
</div>
    <div class="konten2 pt-3" style="padding: 0 50px; position: relative;">
        <div id="ulasanCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <?php
                $stmt = $conn->query("
                    SELECT u.Ulasan, u.Rating, b.judul AS JudulBuku, usr.username AS NamaUser 
                    FROM ulasanbuku u
                    JOIN buku b ON u.BukuID = b.BukuID
                    JOIN user usr ON u.UserID = usr.UserID
                    ORDER BY u.UlasanID DESC
                ");
                $ulasan = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $chunked = array_chunk($ulasan, 2);
                foreach ($chunked as $index => $group) :
                ?>
                    <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                        <div class="row justify-content-center g-4">
                            <?php foreach ($group as $row) : ?>
                                <div class="col-md-5">
                                    <div class="testimonial-card">
                                        <div class="testimonial-header">
                                            <div class="avatar"><?= strtoupper(substr($row['NamaUser'], 0, 1)) ?></div>
                                            <div>
                                                <div class="user-info"><?= htmlspecialchars($row['NamaUser']) ?></div>
                                                <div class="judul-buku">Tentang: <strong><?= htmlspecialchars($row['JudulBuku']) ?></strong></div>
                                            </div>
                                        </div>
                                        <div class="ulasan-text">
                                            "<?= nl2br(htmlspecialchars($row['Ulasan'])) ?>"
                                        </div>
                                        <div class="rating">
                                            <?= str_repeat('⭐', intval($row['Rating'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Panah di luar area ulasan -->
        <button class="carousel-control-prev" type="button" data-bs-target="#ulasanCarousel" data-bs-slide="prev" style="position: absolute; top: 50%; left: 0; transform: translateY(-50%); z-index: 10;">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#ulasanCarousel" data-bs-slide="next" style="position: absolute; top: 50%; right: 0; transform: translateY(-50%); z-index: 10;">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>
     </div>
     </div> 
    </div>

  <!-- footer -->
<footer class="text-white py-4" style="background-color: #334155; width:100%; margin-left:-1px; margin-top:10px;">
  <div class="container text-center">
    <h4 class="fw-bold mb-2" style="color: #FF5722;">NusaPustaka Library</h4>
    <p class="fst-italic text-light mb-4">"Membaca adalah jendela dunia — dan kami bukakan untukmu."</p>
   <div class="mb-3">
  <a href="#" class="mx-2 fs-5" style="color: #FF5722;"><i class="bi bi-instagram"></i></a>
  <a href="#" class="mx-2 fs-5" style="color: #FF5722;"><i class="bi bi-facebook"></i></a>
  <a href="#" class="mx-2 fs-5" style="color: #FF5722;"><i class="bi bi-x"></i></a>
</div>
    <div class="text-secondary small">
      &copy; 2025 <span class="text-light">NusaPustaka</span>. All rights reserved.
    </div>
  </div>
</footer>

 <!-- js ulasan -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 <!-- js panah -->
<script>
  onscroll = () =>
    document.querySelector('.scroll-top-btn').style.display = scrollY > 100 ? 'block' : 'none';
</script>
<!-- js koleksi -->
<script>
document.querySelectorAll('.bookmark-btn').forEach(btn => {
  btn.onclick = e => {
    e.preventDefault();
    const icon = btn.querySelector('i');
    const label = btn.closest('.position-absolute').querySelector('.kategori-label');
    const isSaved = icon.classList.contains('fa-bookmark');

    if (isSaved) {
      // Balikin ke semula
      icon.className = 'fa fa-bookmark-o';
      label.style.backgroundColor = '#334155';
    } else {
      // Simpan
      icon.className = 'fa fa-bookmark';
      label.style.backgroundColor = '#16a34a';
    }

    // Tetap kirim request ke server (boleh kamu sesuaikan untuk simpan/hapus)
    fetch(btn.href);
  };
  fetch(btn.href)
  .then(res => res.text())
  .then(text => {
    console.log(text); // << LIHAT INI DI CONSOLE
  });

});
</script>

</body>
</html>