<?php
// Pastikan session dimulai
session_start();
require_once "../lib/koneksi.php";

$db = new Database();
$conn = $db->conn;

// Inisialisasi pesan sukses
$pesan_sukses = "";

// Ambil data dari URL
$selectedUser = $_SESSION['user_id'] ?? '';  // Mengambil ID pengguna dari session
$selectedBuku = $_GET['id'] ?? '';

// Pastikan $selectedUser terisi
if (!$selectedUser) {
    // Redirect jika tidak ada user yang login
    echo "<script>alert('Anda belum login.'); window.location.href='login.php';</script>";
    exit;
}

// Ambil nama pengguna dari database jika $selectedUser ada
$namaUser = '';
if ($selectedUser) {
    $stmt = $conn->prepare("SELECT NamaLengkap FROM user WHERE UserID = ?");
    $stmt->execute([$selectedUser]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    $namaUser = $userData ? $userData['NamaLengkap'] : '';
}

// Ambil judul buku dari database jika $selectedBuku ada
$judulBuku = '';
if ($selectedBuku) {
    $stmt = $conn->prepare("SELECT Judul FROM buku WHERE BukuID = ?");
    $stmt->execute([$selectedBuku]);
    $bukuData = $stmt->fetch(PDO::FETCH_ASSOC);
    $judulBuku = $bukuData ? $bukuData['Judul'] : '';
}

// Ambil daftar buku
$bukuList = $conn->query("SELECT BukuID, Judul FROM buku")->fetchAll(PDO::FETCH_ASSOC);

// Proses tambah/edit data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['UserID'];
    $buku_id = $_POST['BukuID'];
    $ulasan = $_POST['Ulasan'];
    $rating = $_POST['Rating'];

    if (isset($_POST["tambah"])) {
        $sql = "INSERT INTO ulasanbuku (UserID, BukuID, Ulasan, Rating) 
                VALUES (:user_id, :buku_id, :ulasan, :rating)";
    } elseif (isset($_POST["edit"])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE ulasanbuku 
                SET UserID = :user_id, BukuID = :buku_id, Ulasan = :ulasan, Rating = :rating 
                WHERE UlasanID = :id";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':buku_id', $buku_id, PDO::PARAM_INT);
    $stmt->bindParam(':ulasan', $ulasan, PDO::PARAM_STR);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);

    if (isset($id)) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }

    if ($stmt->execute()) {
        $pesan_sukses = isset($_POST["tambah"]) ? "Data berhasil ditambahkan!" : "Data berhasil diperbarui!";
        echo "<script>
            setTimeout(function() {
                window.location.href='peminjam.php';
            }, 1500);
        </script>";
    }
}
?>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
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
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 130vh;
        background-color: #f5f5f5;
        margin-top:-80px;
    }

    .card {
        background-color: #fff;
        border: 3px solid #FF5722;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        width: 700px;
        padding: 30px;
        text-align: center;
    }

    h5 {
        color: #FF5722;
        font-weight: 600;
        margin-bottom: 20px;
    }

    label {
        display: block;
        text-align: left;
        font-weight: 500;
        color: #FF5722;
        margin-top: 10px;
    }

    input[type="text"],
    input[type="email"] {
        width: 100%;
        padding: 10px;
        border: 2px solid #FF5722;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.3s ease;
    }

    input:focus {
        border-color: #FF4500;
    }

    .btn-orange {
        width: 100%;
        background-color: #FF5722;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-orange:hover {
        background-color: #FF4500;
    }
</style>
<div class="navbar">
    <div class="logo">
        <img src="../asset/img/logo.png">
    </div>

    <div class="center-menu">
        <a href="peminjam.php"><i class="bi bi-cursor-fill" style="margin-right: 8px; color: #ff6600; font-size: 1.4rem;"></i> Home</a>
        <a href="../logout.php"><i class="bi bi-person-vcard" style="margin-right: 8px; color: #ff6600; font-size: 1.4rem;"></i> Logout</a>
    </div>
</div>
<?php if (!empty($pesan_sukses)) : ?>
<div class="alert alert-info alert-dismissible fade show text-center position-relative" role="alert">
    <span>  <?= $pesan_sukses; ?></span>
    <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
<div class="container">
    <div class="card">
        <h5>Beri Ulasan</h5>
        <form method="post">
            <input type="hidden" name="id" value="<?= $edit_id ?? '' ?>">

         <!-- USER -->
        <div class="mb-3">
            <label class="form-label">Peminjam:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($namaUser) ?>" readonly>
            <input type="hidden" name="UserID" value="<?= htmlspecialchars($selectedUser) ?>">
        </div>

        <!-- BUKU -->
        <div class="mb-3">
            <label class="form-label">Buku:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($judulBuku) ?>" readonly>
            <input type="hidden" name="BukuID" value="<?= htmlspecialchars($selectedBuku) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ulasan:</label>
            <textarea name="Ulasan" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Rating:</label>
            <input type="number" name="Rating" class="form-control" min="1" max="5" required>
        </div>

        <button type="submit" name="tambah" class="btn-orange w-100">
            Simpan
        </button>
    </form>
</div>
</div>
