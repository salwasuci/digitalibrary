<?php
// Menghubungkan ke database
require_once "../lib/koneksi.php";

$db = new Database();
$conn = $db->conn;

// Inisialisasi pesan sukses
$pesan_sukses = "";

// Ambil daftar buku
$bukuList = $conn->query("SELECT BukuID, Judul FROM buku")->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar kategori
$kategoriList = $conn->query("SELECT KategoriID, NamaKategori FROM kategoribuku")->fetchAll(PDO::FETCH_ASSOC);

// Inisialisasi variabel untuk edit
$edit_id = "";
$edit_buku = "";
$edit_kategori = "";

// Proses Insert Data
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($_POST['id'])) {
    $buku_id = $_POST['buku_id'];
    $kategori_id = $_POST['kategori_id'];

    $stmt = $conn->prepare("INSERT INTO kategoribuku_relasi (BukuID, KategoriID) VALUES (:buku_id, :kategori_id)");
    $stmt->bindParam(':buku_id', $buku_id, PDO::PARAM_INT);
    $stmt->bindParam(':kategori_id', $kategori_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $pesan_sukses = "Data berhasil ditambahkan!";
        
        echo "<script>
            setTimeout(function() {";
        
        if ($_SESSION['role'] == 'petugas') {
            echo "window.location.href='petugas.php?page=relasi';";
        } elseif ($_SESSION['role'] == 'admin') {
            echo "window.location.href='admin.php?page=relasi';";
        }
    
        echo "}, 1500);
        </script>";
    }
    
}

// Proses Update Data
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['id'])) {
    $buku_id = $_POST['buku_id'];
    $kategori_id = $_POST['kategori_id'];
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("UPDATE kategoribuku_relasi SET BukuID = :buku_id, KategoriID = :kategori_id WHERE KategoriBukuID = :id");
    $stmt->bindParam(':buku_id', $buku_id, PDO::PARAM_INT);
    $stmt->bindParam(':kategori_id', $kategori_id, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $pesan_sukses = "Data berhasil diperbarui!";
        
        // Reset data form
        $edit_id = "";
        $edit_buku = "";
        $edit_kategori = "";
    
        echo "<script>
            setTimeout(function() {";
        
        if ($_SESSION['role'] == 'petugas') {
            echo "window.location.href='petugas.php?page=relasi';";
        } elseif ($_SESSION['role'] == 'admin') {
            echo "window.location.href='admin.php?page=relasi';";
        }
    
        echo "}, 1500);
        </script>";
    }    
}

// Proses Hapus Data
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $stmt = $conn->prepare("DELETE FROM kategoribuku_relasi WHERE KategoriBukuID = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $pesan_sukses = "Data berhasil dihapus!";
        echo "<script>
            setTimeout(function() {";
        
        if ($_SESSION['role'] == 'petugas') {
            echo "window.location.href='petugas.php?page=relasi';";
        } elseif ($_SESSION['role'] == 'admin') {
            echo "window.location.href='admin.php?page=relasi';";
        }
    
        echo "}, 1500);
        </script>";
    }
    
}

// Proses Ambil Data untuk Edit
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT * FROM kategoribuku_relasi WHERE KategoriBukuID = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $edit_id = $data['KategoriBukuID'];
            $edit_buku = $data['BukuID'];
            $edit_kategori = $data['KategoriID'];
        }
    }
}
?>


<style>
 .btn-orange {
    background-color: #FF5722 !important;
    color: #fff !important;
    border: none;
}

.btn-warning {
    background-color: #FF9800 !important;
    color: #fff !important;
    border: none;
}

.btn-orange:focus, .btn-orange:active,
.btn-warning:focus, .btn-warning:active {
    background-color: #FF5722 !important; /* Warna tetap stabil */
    color: #fff !important;
    border-color: #FF5722 !important;
    box-shadow: none !important;
    outline: none !important;
}

.btn-warning:focus, .btn-warning:active {
    background-color: #FF9800 !important; /* Warna tetap stabil */
    border-color: #FF9800 !important;
}
.btn-orange:hover {
    background-color: #FF5722; transform: scale(1.05); /* Warna tetap sama, hanya efek zoom */
}
.btn-warning:hover {
    background-color:  #FF9800; transform: scale(1.05); /* Warna tetap sama, hanya efek zoom */
}

    .form-select {
        border: 2px solid #FF5722;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        outline: none;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-select:focus {
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
    .btn-kembali {
        background-color: #fff;
        color: #FF5722;
        border: 2px solid #FF5722;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-kembali:hover {
        background-color: #FF5722;
        color: #fff;
        border: 2px solid #FF5722;
    }
</style>

<div class="container mt-3 d-flex justify-content-between align-items-center">
    <h2 style="color: #FF5722; font-weight: 600; font-size: 1.5rem;">📂 Relasi Kategori</h2>
    <a href="?page=kategori" class="btn btn-kembali">
    🔗 Kategori Buku
</a>
</div>  
    <!-- Pesan Sukses -->
    <?php if ($pesan_sukses): ?>
    <div class="alert alert-info alert-dismissible fade show text-center position-relative" role="alert">
        <span><?= htmlspecialchars($pesan_sukses); ?></span>
        <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
<div class="card shadow-lg p-4 border-3" style="border-color: #FF5722; margin-top:20px;">
    <div class="row">
        <div class="col-md-5">
            <h5 style="color: #FF5722; font-weight: 600; font-size: 1.2rem;">Tambah / Edit Relasi Kategori</h5>
            <form action="" method="POST" style="margin-top:15px;">
            <input type="hidden" name="id" value="<?= $edit_id ?>">
                <div class="mb-3">
                    <select name="buku_id" class="form-select" required>
                        <option value="" disabled selected>Pilih Buku</option>
                        <?php foreach ($bukuList as $buku) { ?>
                            <option value="<?= $buku['BukuID'] ?>" <?= ($buku['BukuID'] == $edit_buku) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($buku['Judul']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <select name="kategori_id" class="form-select" required>
                        <option value="" disabled selected>Pilih kategori</option>
                        <?php foreach ($kategoriList as $kategori) { ?>
                            <option value="<?= $kategori['KategoriID'] ?>" <?= ($kategori['KategoriID'] == $edit_kategori) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kategori['NamaKategori']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn <?= $edit_id ? 'btn-warning' : 'btn-orange' ?> w-100">
                    <?= $edit_id ? 'Update Data' : 'Simpan' ?>
                </button>
            </form>
        </div>

        <div class="col-md-7">
    <h5 style="color: #FF5722; font-weight: 600; font-size: 1.2rem;">Daftar Relasi Kategori</h5>
    <div class="table-responsive" style="max-height: 400px; overflow-x: auto; margin-top:15px;">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Kategori</th>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT kbr.KategoriBukuID, b.Judul, kb.NamaKategori 
                                    FROM kategoribuku_relasi kbr
                                    JOIN buku b ON kbr.BukuID = b.BukuID
                                    JOIN kategoribuku kb ON kbr.KategoriID = kb.KategoriID");
                $no = 1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr class='text-center'>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['Judul']) ?></td>
                        <td><?= htmlspecialchars($row['NamaKategori']) ?></td>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <td>
                            <a href="?page=relasi&edit=<?= $row['KategoriBukuID'] ?>" class="btn btn-outline-warning btn-sm" style="border-color: #FF5722; color: #FF5722;">✏️</a>
                            <a href="?page=relasi&delete=<?= $row['KategoriBukuID'] ?>" class="btn btn-outline-danger btn-sm" style="border-color: #FF5722; color: #FF5722;" onclick="return confirm('Yakin hapus data?')">🗑️</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
