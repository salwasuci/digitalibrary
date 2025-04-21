<?php
require_once "../lib/koneksi.php";

$db = new Database();
$conn = $db->conn;

// Inisialisasi variabel
$pesan_sukses = "";
$edit_id = "";
$edit_user = "";
$edit_buku = "";

// Ambil daftar pengguna (peminjam)
$userStmt = $conn->query("SELECT UserID, NamaLengkap FROM user WHERE Role = 'peminjam'");
$userList = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar buku
$bukuStmt = $conn->query("SELECT BukuID, Judul FROM buku");
$bukuList = $bukuStmt->fetchAll(PDO::FETCH_ASSOC);

// Proses Tambah Data
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($_POST['id'])) {
    $user_id = isset($_POST['UserID']) ? $_POST['UserID'] : null;
    $buku_id = isset($_POST['BukuID']) ? $_POST['BukuID'] : null;

    if ($user_id && $buku_id) {
        $sql = "INSERT INTO koleksipribadi (UserID, BukuID) VALUES (:user_id, :buku_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':buku_id', $buku_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $pesan_sukses = "Data berhasil ditambahkan!";
            header("Location: koleksi_outputan.php");
            exit;
        }
    }        
}

// Menangani proses hapus data
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $stmt = $conn->prepare("DELETE FROM koleksipribadi WHERE KoleksiID = :id");
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        $pesan_sukses = "Data berhasil dihapus!";
        echo "<script>
            setTimeout(function() {";
        
        if ($_SESSION['role'] == 'petugas') {
            echo "window.location.href='petugas.php?page=koleksi';";
        } elseif ($_SESSION['role'] == 'admin') {
            echo "window.location.href='admin.php?page=koleksi';";
        }
    
        echo "}, 1500);
        </script>";
    }
}


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $buku_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id']; // Pastikan ini udah diset pas login

    // Cek apakah buku sudah ada di koleksi user
    $cekStmt = $conn->prepare("SELECT * FROM koleksipribadi WHERE UserID = :user_id AND BukuID = :buku_id");
    $cekStmt->execute(['user_id' => $user_id, 'buku_id' => $buku_id]);

    if ($cekStmt->rowCount() == 0) {
        $insertStmt = $conn->prepare("INSERT INTO koleksipribadi (UserID, BukuID) VALUES (:user_id, :buku_id)");
        $insertStmt->execute(['user_id' => $user_id, 'buku_id' => $buku_id]);
        echo "<script>alert('Buku berhasil ditambahkan ke koleksi!'); window.location.href='koleksi_outputan.php';</script>";
    } else {
        echo "<script>alert('Buku sudah ada di koleksimu!'); window.location.href='koleksi_outputan.php';</script>";
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
    margin-top:-57px;
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
.faded-placeholder {
    color: rgba(0, 0, 0, 0.6); /* semua text 60% transparan */
}

.faded-placeholder option {
    color: #000; /* pas dropdown dibuka, semua pilihan normal */
}

/* Kalau mau yang dipilih aja yang transparan */
.faded-placeholder:invalid {
    color: rgba(0, 0, 0, 0.50);
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
<div class="card shadow-lg p-4 border-3 mx-auto" style="border-color: #FF5722; margin-top: 20px; max-width: 500px;">
    <div class="row">
        <div class="col-12">
            <h5 style="color: #FF5722; font-weight: 600; font-size: 1.2rem; position: relative; display: inline-block; padding-bottom: 5px;">
                Tambah Koleksi Buku
            </h5>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit_id ?>">

                <div class="mb-3">
                    <select name="UserID" class="form-select faded-placeholder" required>
                        <option value="" disabled selected>Namamu</option>
                        <?php foreach ($userList as $user) { ?>
                            <option value="<?= $user['UserID'] ?>" <?= ($user['UserID'] == $edit_user) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['NamaLengkap']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <select name="BukuID" class="form-select  faded-placeholder" required>
                        <option value="" disabled selected>Pilih buku</option>
                        <?php foreach ($bukuList as $buku) { ?>
                            <option value="<?= $buku['BukuID'] ?>" <?= ($buku['BukuID'] == $edit_buku) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($buku['Judul']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit" class="btn w-100 <?= $edit_id ? 'btn-warning' : 'btn-orange' ?>">
                    <?= $edit_id ? 'Update Data' : 'Tambah Koleksi' ?>
                </button>
            </form>
        </div>
    </div>
</div>
        
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

