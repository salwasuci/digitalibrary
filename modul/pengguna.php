<?php
require_once "../lib/koneksi.php";

// Kelas User untuk mengelola data pengguna (petugas)
class User {
    private $conn;

    // Konstruktor untuk inisialisasi koneksi database
    public function __construct($db) {
        $this->conn = $db;
    }

    // Fungsi untuk menambahkan data petugas
    public function tambahUser($username, $password, $confirm_password, $email, $namaLengkap, $alamat) {
        if ($password !== $confirm_password) {
            return "Konfirmasi password tidak sesuai!";
        }
    
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'petugas';
    
        try {
            $sql = "INSERT INTO user (Username, Password, confirm_password, Email, NamaLengkap, Alamat, Role)
                    VALUES (:username, :password, :confirm_password, :email, :namaLengkap, :alamat, :role)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':confirm_password', $confirm_password); // Kolom baru untuk password asli
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':namaLengkap', $namaLengkap);
            $stmt->bindParam(':alamat', $alamat);
            $stmt->bindParam(':role', $role);
    
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
        }
    }
    
    // Fungsi untuk menampilkan data petugas
    public function tampilkanUser() {
        $sql = "SELECT UserID, Username, Email, NamaLengkap, Alamat, confirm_password FROM user WHERE Role = 'petugas'";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Fungsi untuk menghapus data petugas
    public function hapusUser($id) {
        $sql = "DELETE FROM user WHERE UserID = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("SQL Error saat menghapus data: " . $e->getMessage());
        }
    }
}

// Inisialisasi koneksi database
$db = new Database();
$conn = $db->conn;

// Buat objek User
$user = new User($conn);

// Proses tambah data petugas
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tambahPetugas'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $namaLengkap = $_POST['namaLengkap'];
    $alamat = $_POST['alamat'];

    $user->tambahUser($username, $password, $confirm_password, $email, $namaLengkap, $alamat);
header("Location: ?page=pengguna&status=added");
exit();

}

// Proses hapus data petugas
if (isset($_GET['hapus'])) {
    $user->hapusUser($_GET['hapus']);
    header("Location: ?page=pengguna&status=deleted");
    exit();
}

// Menampilkan data petugas
$dataPetugas = $user->tampilkanUser();

// Inisialisasi pesan (opsional, bisa langsung di dalam if HTML juga)
$message = '';
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'added':
            $message = " Petugas berhasil ditambahkan!";
            break;
        case 'deleted':
            $message = " Petugas berhasil dihapus!";
            break;
        case 'updated':
            $message = " Petugas berhasil diperbarui!";
            break;
    }
}

?>

<!-- Tampilan Halaman -->
<div class="container mt-3">
<h2 class="text-start" style="color: #FF5722; font-weight: 600; font-size: 1.5rem;">
    📋 Data Petugas
</h2>
<?php if (!empty($message)) : ?>
    <div id="success-message" class="alert <?= $alertClass; ?> alert-info alert-dismissible fade show text-center position-relative">
        <?= htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(function() {
            const alert = document.getElementById('success-message');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 300);
            }
        }, 3000);
    </script>
<?php endif; ?>

    <style>
          .card {
            max-width: 900px;
            margin: 0 auto;
        }
        .btn-orange {
            background-color: #FF5722 !important;
            color: #fff !important;
            border: none;
        }
        .btn-orange:hover {
            background-color: #E64A19;
            transform: scale(1.05);
        }
        .table th {
            background-color: #FF5722;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }
        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        .table tbody tr:hover {
            background-color: #FFF3E0;
        }
        .form-control {
            border: 2px solid #FF5722;
            border-radius: 8px;
            padding: 5px;
            font-size: 0.85rem;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #E64A19;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }
        .table {
            border-radius: 12px;
            overflow: hidden;
            font-size: 0.85rem;
        }
    </style>

    <div class="card shadow-lg p-4 border-3" style="border-color: #FF5722; margin-top: 30px;">
        <div class="row">
            <!-- Form Input Data -->
            <div class="col-md-5">
                <h5 style="color: #FF5722; font-weight: 600; text-align: center;">
                    Tambah Petugas
                </h5>
                <form method="POST" style="margin-top:15px;">
                    <div class="mb-3">
                        <input type="text" name="username" placeholder="Username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" placeholder="Password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="confirm_password" placeholder="Konfirmasi Password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" placeholder="Email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="namaLengkap" placeholder="Nama Lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="alamat" placeholder="Alamat" class="form-control" required>
                    </div>
                    <button type="submit" name="tambahPetugas" class="btn btn-orange w-100">
                        Tambah Petugas
                    </button>
                </form>
            </div>

            <!-- Tabel Data Petugas -->
            <div class="col-md-7">
                <h5 style="color: #FF5722; font-weight: 600; text-align: center;">
                    Daftar Petugas
                </h5>
                <div class="table-responsive" style="margin-top:15px;">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Nama Lengkap</th>
                                <th>Alamat</th>
                                <th>Password</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($dataPetugas as $petugas) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $petugas['Username'] ?></td>
                                    <td><?= $petugas['Email'] ?></td>
                                    <td><?= $petugas['NamaLengkap'] ?></td>
                                    <td><?= $petugas['Alamat'] ?></td>
                                    <td><?= $petugas['confirm_password'] ?></td>
                                    <td>
                                    <a href="?page=update&edit=<?= $petugas['UserID'] ?>" 
   class="btn btn-outline-warning btn-sm">✏️</a>
                                        <a href="?page=pengguna&hapus=<?= $petugas['UserID'] ?>" 
                                           class="btn btn-outline-danger btn-sm" 
                                           onclick="return confirm('Yakin ingin menghapus?')">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
