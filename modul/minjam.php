<?php
session_start();
include '../lib/koneksi.php';
$db = new Database();
$pdo = $db->conn;

$today = date('Y-m-d');
// Notifikasi
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Ambil data dari URL
$selectedUser = $_SESSION['user_id'] ?? '';
$selectedBuku = $_GET['id'] ?? '';

// Proses simpan data
if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['tambah'])) {
    $UserID = $_POST['UserID'];
    $BukuID = $_POST['BukuID'];
    $TanggalPeminjaman = $_POST['TanggalPeminjaman'];
    $TanggalPengembalian = $_POST['TanggalPengembalian'];
    $StatusPeminjaman = 'dipinjam'; // Otomatis set status

    // Validasi tanggal
    $tglPinjam = new DateTime($TanggalPeminjaman);
    $tglKembali = new DateTime($TanggalPengembalian);
    $selisih = $tglPinjam->diff($tglKembali)->days;

    if ($tglKembali < $tglPinjam) {
        $status = "⛔ Tanggal pengembalian tidak boleh lebih awal dari tanggal peminjaman!";
    } elseif ($selisih > 14) {
        $status = "⛔ Lama peminjaman tidak boleh lebih dari 14 hari!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO peminjaman (UserID, BukuID, TanggalPeminjaman, TanggalPengembalian, StatusPeminjaman) 
                               VALUES (?, ?, ?, ?, ?)");
        $simpan = $stmt->execute([$UserID, $BukuID, $TanggalPeminjaman, $TanggalPengembalian, $StatusPeminjaman]);

        if ($simpan) {
            header("Location: peminjam.php?status=tambah");
            exit();
        } else {
            $status = "❌ Gagal menyimpan data!";
        }
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

<?php if ($status == 'tambah'): ?>
<div class="alert alert-info alert-dismissible fade show text-center position-relative alert-status">
    Data Peminjam berhasil ditambahkan!
    <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php elseif (!empty($status)): ?>
<div class="alert alert-danger alert-dismissible fade show text-center position-relative alert-status">
    <?= $status ?>
    <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

   <!-- Script notifikasi -->
   <script>
    setTimeout(() => {
        document.querySelectorAll('.alert-status').forEach(el => el.remove());
    }, 3000);
</script>
    <div class="container">
    <div class="card">
        <h5>Form Peminjam</h5>
<form action="" method="POST">
<!-- USER -->
<div class="mb-3">
    <label class="form-label">Peminjam:</label>
    <?php
    // Ambil nama user dari database berdasarkan UserID
    $namaUser = '';
    if ($selectedUser) {
        $stmt = $pdo->prepare("SELECT NamaLengkap FROM user WHERE UserID = ?");
        $stmt->execute([$selectedUser]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        $namaUser = $userData ? $userData['NamaLengkap'] : '';
    }
    ?>
    <input type="text" class="form-control" value="<?= htmlspecialchars($namaUser) ?>" readonly>
    <input type="hidden" name="UserID" value="<?= htmlspecialchars($selectedUser) ?>">
</div>

<!-- BUKU -->
<div class="mb-3">
    <label class="form-label">Buku:</label>
    <?php
    // Ambil judul buku dari database berdasarkan BukuID
    $judulBuku = '';
    if ($selectedBuku) {
        $stmt = $pdo->prepare("SELECT Judul FROM buku WHERE BukuID = ?");
        $stmt->execute([$selectedBuku]);
        $bukuData = $stmt->fetch(PDO::FETCH_ASSOC);
        $judulBuku = $bukuData ? $bukuData['Judul'] : '';
    }
    ?>
    <input type="text" class="form-control" value="<?= htmlspecialchars($judulBuku) ?>" readonly>
    <input type="hidden" name="BukuID" value="<?= htmlspecialchars($selectedBuku) ?>">
</div>

    <div class="mb-3">
    <label for="TanggalPeminjaman" class="form-label">Tanggal Peminjaman:</label>
    <input type="text" name="TanggalPeminjaman" class="form-control" value="<?= $today ?>" readonly>
    </div>
    <div class="mb-3">
    <label for="TanggalPengembalian" class="form-label">Tanggal Pengembalian:</label>
    <input type="date" name="TanggalPengembalian" class="form-control" required>
    </div>
    <button type="submit" name="tambah" class="btn-orange w-100">
    Simpan
</button>
</form>

</body>
</html>
