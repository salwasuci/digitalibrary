<?php
require_once "../lib/koneksi.php";

class KategoriBuku {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function getAllKategori() {
        $stmt = $this->conn->prepare("SELECT * FROM kategoribuku ORDER BY KategoriID ASC"); 
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tambahKategori($namaKategori) {
        $sql = "INSERT INTO kategoribuku (NamaKategori) VALUES (:namaKategori)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":namaKategori", $namaKategori);
        return $stmt->execute();
    }

    public function updateKategori($id, $namaKategori) {
        $sql = "UPDATE kategoribuku SET NamaKategori=:namaKategori WHERE KategoriID=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":namaKategori", $namaKategori);
        return $stmt->execute();
    }

    public function hapusKategori($id) {
        $sql = "DELETE FROM kategoribuku WHERE KategoriID=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function kategoriTerpakai($id) {
        $sql = "SELECT 1 FROM kategoribuku_relasi WHERE KategoriID = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }
}

$kategoriBuku = new KategoriBuku();
$message = "";

// Proses Tambah (admin & petugas)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tambah"])) {
    if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'petugas'])) {
        $nama = trim($_POST["namaKategori"]);
        if ($kategoriBuku->tambahKategori($nama)) {
            header("Location: ?page=kategori&status=added");
            exit;
        }
    } else {
        header("Location: ?page=kategori&status=unauthorized");
        exit;
    }
}

// Proses Update (hanya admin)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $id = intval($_POST["id"]);
        $nama = trim($_POST["namaKategori"]);
        if ($kategoriBuku->updateKategori($id, $nama)) {
            header("Location: ?page=kategori&status=updated");
            exit;
        }
    } else {
        header("Location: ?page=kategori&status=unauthorized");
        exit;
    }
}

// Proses Hapus (hanya admin)
if (isset($_GET["delete"])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $id = intval($_GET["delete"]);
        if ($kategoriBuku->hapusKategori($id)) {
            header("Location: ?page=kategori&status=deleted");
            exit;
        }
    } else {
        header("Location: ?page=kategori&status=unauthorized");
        exit;
    }
}

// Notifikasi berdasarkan status di URL
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'added':
            $message = "Kategori berhasil ditambahkan!";
            break;
        case 'updated':
            $message = "Kategori berhasil diperbarui!";
            break;
        case 'deleted':
            $message = "Kategori berhasil dihapus!";
            break;
        case 'unauthorized':
            $message = "Anda tidak memiliki izin untuk melakukan aksi ini!";
            break;
    }
}

$kategoriList = $kategoriBuku->getAllKategori();
?>


<!-- STYLE -->
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

<!-- HEADER -->
<div class="container mt-3 d-flex justify-content-between align-items-center">
    <h2 style="color: #FF5722; font-weight: 600; font-size: 1.5rem;">📂 Kategori Buku</h2>
    <a href="?page=relasi" class="btn btn-orange">🔗 Relasi Kategori</a>
</div>
<?php if ($message): ?>
    <div id="success-message" class="alert alert-info alert-dismissible fade show text-center" role="alert">
        <?= htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(function() {
            const alert = document.getElementById('success-message');
            if (alert) {
                alert.classList.remove('show'); // animasi fade
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 300); // hapus dari DOM setelah animasi
            }
        }, 3000); // 3 detik
    </script>
<?php endif; ?>

<!-- FORM DAN DAFTAR -->
<div class="card shadow-lg p-4 border-3" style="border-color: #FF5722; margin-top:20px;">
    <div class="row">
        <!-- FORM -->
        <div class="col-md-5">
            <h5 style="color: #FF5722; font-weight: 600; font-size: 1.2rem;">Tambah / Edit Kategori Buku</h5>
            <form action="" method="POST" style="margin-top:15px;">
                <input type="hidden" name="id" id="kategoriId">
                <input type="text" name="namaKategori" id="namaKategori" class="form-control mb-3" placeholder="Nama Kategori" required>
                <button type="submit" name="tambah" class="btn w-100 btn-tambah" style="background-color: #FF5722; color: #fff;">Tambah Kategori</button>
                <button type="submit" name="update" class="btn w-100 mt-2 btn-update" style="background-color: #FF9800; color: #fff; display: none;">Update Kategori</button>
            </form>
        </div>

        <!-- TABEL -->
        <div class="col-md-7">
            <h5 style="color: #FF5722; font-weight: 600; font-size: 1.2rem;">Daftar Kategori Buku</h5>
            <div class="table-responsive" style="max-height: 400px; overflow-x: auto; margin-top:15px;">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($kategoriList as $row): 
                            $adaRelasi = $kategoriBuku->kategoriTerpakai($row['KategoriID']);
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row["NamaKategori"]) ?></td>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <td class="text-center">
                                <button class="btn btn-outline-warning btn-sm" onclick="editKategori('<?= $row['KategoriID'] ?>', '<?= $row['NamaKategori'] ?>')" style="border-color: #FF5722; color: #FF5722;">✏️</button>
                                <?php if (!$adaRelasi): ?>
                                    <a href="?page=kategori&delete=<?= $row['KategoriID'] ?>" class="btn btn-outline-danger btn-sm" style="border-color: #FF5722; color: #FF5722;" onclick="return confirm('Yakin ingin menghapus kategori ini?')">🗑️</a>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary btn-sm" disabled title="Kategori sedang digunakan">🔒</button>
                                <?php endif; ?>
                                
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($kategoriList)): ?>
                            <tr>
                                <td colspan="<?= ($_SESSION['role'] === 'admin') ? '3' : '2'; ?>" class="text-center">Tidak ada kategori terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT EDIT -->
<script>
    function editKategori(id, nama) {
        document.getElementById('kategoriId').value = id;
        document.getElementById('namaKategori').value = nama;
        document.querySelector("[name='tambah']").style.display = "none";
        document.querySelector("[name='update']").style.display = "block";
    }
</script>
