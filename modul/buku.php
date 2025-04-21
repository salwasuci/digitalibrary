<?php
// session_start(); // Sudah dipanggil di petugas.php atau admin.php
require_once "../lib/koneksi.php";

$db = new Database();
$conn = $db->conn;

// Inisialisasi pesan sukses
$pesan_sukses = "";

class Buku {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function tambahBuku($data) {
        $sql = "INSERT INTO buku (Judul, gambar, Penulis, Penerbit, TahunTerbit) 
                VALUES (:judul, :gambar, :penulis, :penerbit, :tahunTerbit)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function getAllBuku() {
        $stmt = $this->conn->prepare("SELECT * FROM buku");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBukuById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM buku WHERE BukuID = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBuku($data) {
        $sql = "UPDATE buku SET Judul = :judul, gambar = :gambar, Penulis = :penulis, 
                Penerbit = :penerbit, TahunTerbit = :tahunTerbit WHERE BukuID = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }
}

$buku = new Buku();
$books = $buku->getAllBuku();

// Tambah Buku
if (isset($_POST["tambah"])) {
    if (!empty($_FILES['gambar']['name'])) {
        $folder = "../asset/upload/";
        $gambar = uniqid() . '_' . $_FILES['gambar']['name'];
        $path = $folder . $gambar;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $path)) {
            $data = [
                ':judul' => $_POST['judul'],
                ':gambar' => $gambar,
                ':penulis' => $_POST['penulis'],
                ':penerbit' => $_POST['penerbit'],
                ':tahunTerbit' => $_POST['tahunTerbit']
            ];
            if ($buku->tambahBuku($data)) {
                header("Location: petugas.php?page=buku&status=added");
                exit();
            }
        }
    }
}

// Hapus data
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $stmt = $conn->prepare("DELETE FROM buku WHERE BukuID = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // Cek siapa yang login: admin atau petugas
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            header("Location: admin.php?page=buku&status=deleted");
        } else {
            header("Location: petugas.php?page=buku&status=deleted");
        }
        exit();
    } else {
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            header("Location: admin.php?page=buku&status=failed");
        } else {
            header("Location: petugas.php?page=buku&status=failed");
        }
        exit();
    }
}
?>

<style>
    .btn-tambah, .btn-update {
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-tambah:hover, .btn-update:hover {
        transform: scale(1.05);
    }

    .btn-tambah:hover { background-color: #E64A19; }
    .btn-update:hover { background-color: #F57C00; }

    .btn-tambah:active { background-color: #D84315; transform: scale(0.98); }
    .btn-update:active { background-color: #EF6C00; transform: scale(0.98); }

    .form-control {
        border: 2px solid #FF5722;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        outline: none;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: #E64A19;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    }

    .table-responsive {
        max-height: 300px;
        overflow-y: auto;
    }

    .table {
        font-size: 0.9rem;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        background-color: #FF5722;
        color: #fff;
        text-align: center;
    }

    .table tbody tr:hover { background-color: #FFF3E0; }

    .table td, .table th {
        padding: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }
    .btn-orange {
        background-color: #fff;
        color: #FF5722;
        border: 2px solid #FF5722;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-orange:hover {
        background-color: #FF5722;
        color: #fff;
        border: 2px solid #FF5722;
    }
</style>
<div class="container mt-3">
    <h2 class="text-center" style="color: #FF5722; font-weight: 600; font-size: 1.5rem; position: relative; display: inline-block; padding-bottom: 5px;">
        📚 Kelola Buku
    </h2>
 <!-- Pesan Sukses -->
 <?php 
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($status) : 
    $pesan = '';
    $alertClass = 'alert-info';

    switch ($status) {
        case 'updated':
            $pesan = 'Data berhasil diperbarui!';
            $alertClass = 'alert-success';
            break;
        case 'deleted':
            $pesan = 'Data berhasil dihapus!';
            $alertClass = 'alert-danger';
            break;
        case 'added':
            $pesan = 'Data berhasil ditambahkan!';
            $alertClass = 'alert-primary';
            break;
    }
?>
    <div id="success-message" class="alert <?= $alertClass; ?> alert-info alert-dismissible fade show text-center position-relative">
        <?= $pesan; ?>
        <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        setTimeout(function() {
            document.getElementById('success-message').style.display = 'none';
        }, 3000); // Muncul 3 detik
    </script>
<?php endif; ?>


    <div class="card shadow-lg p-4 border-3" style="border-color: #FF5722; margin-top: 15px; max-width: 900px; margin-left: auto; margin-right: auto;">
        <div class="row mt-1">
            <div class="col-md-5">
                <h5 style="color: #FF5722; font-weight: 600;">Tambah Buku</h5>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="text" name="judul" id="judul" class="form-control mb-3" placeholder="Judul Buku" required>
                    <input type="file" name="gambar" id="gambar" class="form-control mb-3" required>
                    <input type="text" name="penulis" id="penulis" class="form-control mb-3" placeholder="Penulis Buku" required>
                    <input type="text" name="penerbit" id="penerbit" class="form-control mb-3" placeholder="Penerbit Buku" required>
                    <input type="text" name="tahunTerbit" id="tahunTerbit" class="form-control mb-3" placeholder="Tahun Terbit" required>
                    <button type="submit" name="tambah" class="btn w-100 btn-tambah" style="background-color: #FF5722; color: #fff;">
                        Tambah Buku
                    </button>
                </form>
            </div>

            <div class="col-md-7">
    <h5 style="color: #FF5722; font-weight: 600;">Daftar Buku</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Gambar</th>
                    <th>Penulis</th>
                    <th>Penerbit</th>
                    <th>Tahun Terbit</th>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>    
                <?php $no = 1; ?>
                <?php foreach ($books as $row): ?>
                    <?php
                    $checkRelasi = $conn->prepare("
                        SELECT 1 FROM kategoribuku_relasi WHERE BukuID = :id
                        UNION
                        SELECT 1 FROM ulasanbuku WHERE BukuID = :id
                        UNION
                        SELECT 1 FROM koleksipribadi WHERE BukuID = :id
                    ");
                    $checkRelasi->bindParam(':id', $row['BukuID']);
                    $checkRelasi->execute();
                    $adaRelasi = $checkRelasi->fetch();
                    ?>
                    <tr>      
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['Judul']) ?></td>
                        <td><img src="../asset/upload/<?= $row['gambar'] ?>" width="50"></td>
                        <td><?= htmlspecialchars($row['Penulis']) ?></td>
                        <td><?= htmlspecialchars($row['Penerbit']) ?></td>
                        <td><?= htmlspecialchars($row['TahunTerbit']) ?></td>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <td class="text-center">
                                <a href="?page=updatebuku&id=<?= $row['BukuID'] ?>" class="btn btn-outline-warning btn-sm" title="Edit">✏️</a>
                                <?php if (!$adaRelasi): ?>
                                    <a href="?page=buku&delete=<?= $row['BukuID'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus buku ini?')" title="Hapus">🗑️</a>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary btn-sm" disabled title="Buku sedang dipinjam atau terkait data lain">🔒</button>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

