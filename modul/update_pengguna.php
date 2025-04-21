<?php
require_once "../lib/koneksi.php";

// Kelas User untuk mengelola data pengguna
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fungsi untuk menampilkan data petugas berdasarkan ID
    public function getUserById($id) {
        $sql = "SELECT * FROM user WHERE UserID = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fungsi untuk memperbarui data petugas
    public function updateUser($id, $username, $email, $namaLengkap, $alamat) {
        try {
            $sql = "UPDATE user SET Username = :username, Email = :email, NamaLengkap = :namaLengkap, Alamat = :alamat WHERE UserID = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':namaLengkap', $namaLengkap);
            $stmt->bindParam(':alamat', $alamat);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();
            return "Data berhasil diperbarui.";
        } catch (PDOException $e) {
            return "Gagal memperbarui data: " . $e->getMessage();
        }
    }
}

// Inisialisasi koneksi database
$db = new Database();
$conn = $db->conn;
$user = new User($conn);

// Proses ambil data untuk diedit
$petugas = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $id = $_GET['edit'];
    $petugas = $user->getUserById($id);
} else {
    echo "<script>alert('Parameter ID tidak valid!'); window.location.href='?page=pengguna';</script>";
    exit();
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatePetugas'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $namaLengkap = $_POST['namaLengkap'];
    $alamat = $_POST['alamat'];

    echo $user->updateUser($id, $username, $email, $namaLengkap, $alamat);
    header("Location: ?page=pengguna&status=updated");
    exit();
}
?>

<!-- Form Update Data -->
<div class="container mt-3">
    <div class="card shadow-lg p-4 border-3" style="border-color: #FF5722; margin-top: 10px; max-width: 500px; margin: 0 auto;">
        <h5 style="color: #FF5722; font-weight: 600; text-align: center;">Update Data Petugas</h5>
        <?php if ($petugas): ?>
        <form method="POST" style="margin-top:15px;">
            <input type="hidden" name="id" value="<?= $petugas['UserID'] ?>">
            <div class="mb-3">
                <label for="username" class="form-label" style="font-weight: bold; color: #FF5722;">Username:</label>
                <input type="text" id="username" name="username" value="<?= $petugas['Username'] ?>" placeholder="Username" class="form-control border-orange" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label" style="font-weight: bold; color: #FF5722;">Email:</label>
                <input type="email" id="email" name="email" value="<?= $petugas['Email'] ?>" placeholder="Email" class="form-control border-orange" required>
            </div>
            <div class="mb-3">
                <label for="namaLengkap" class="form-label" style="font-weight: bold; color: #FF5722;">Nama Lengkap:</label>
                <input type="text" id="namaLengkap" name="namaLengkap" value="<?= $petugas['NamaLengkap'] ?>" placeholder="Nama Lengkap" class="form-control border-orange" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label" style="font-weight: bold; color: #FF5722;">Alamat:</label>
                <input type="text" id="alamat" name="alamat" value="<?= $petugas['Alamat'] ?>" placeholder="Alamat" class="form-control border-orange" required>
            </div>
            <button type="submit" name="updatePetugas" class="btn btn-orange w-100">Perbarui Data</button>
        </form>
        <?php else: ?>
        <p class="text-center text-danger">Data tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<style>
    .border-orange {
        border: 2px solid #FF5722 !important;
        border-radius: 8px;
    }
    .btn-orange {
        background-color: #FF5722;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn-orange:hover {
    background-color:  #FF5722; /* Warna lebih gelap dari oranye utama */
    color: #fff; /* Tetap putih */
    transform: scale(1.05);
}
    .btn-orange:active {
        background-color: #FF5722;
        transform: scale(0.98);
    }
</style>
