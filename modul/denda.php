<?php
require_once "../lib/koneksi.php";
$db = new Database();
$conn = $db->conn;

$level = isset($_SESSION['level']) ? $_SESSION['level'] : '';

// Notifikasi
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Mode edit
$editMode = false;
$editData = ['jumlah_hari' => '', 'jumlah_denda' => '', 'dendaID' => ''];

// Cek jika sedang edit
if (isset($_GET['edit'])) {
    $editMode = true;
    $dendaID = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM denda WHERE dendaID = :dendaID");
    $stmt->bindParam(':dendaID', $dendaID, PDO::PARAM_INT);
    $stmt->execute();
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Ambil semua data
$stmt = $conn->query("SELECT * FROM denda");
$data_denda = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_denda = count($data_denda);

// Proses tambah data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    if ($total_denda >= 1) {
        $message = "⚠️ Data denda hanya boleh satu!";
    } else {
        $jumlah_hari = intval($_POST['jumlah_hari']);
        $jumlah_denda = intval($_POST['jumlah_denda']);

        $stmt = $conn->prepare("INSERT INTO denda (jumlah_hari, jumlah_denda) VALUES (:jumlah_hari, :jumlah_denda)");
        $stmt->bindParam(':jumlah_hari', $jumlah_hari, PDO::PARAM_INT);
        $stmt->bindParam(':jumlah_denda', $jumlah_denda, PDO::PARAM_INT);
        $stmt->execute();

        header("Location:?page=denda&status=tambah");
        exit;        
    }
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $dendaID = intval($_POST['dendaID']);
    $jumlah_hari = intval($_POST['jumlah_hari']);
    $jumlah_denda = intval($_POST['jumlah_denda']);

    $stmt = $conn->prepare("UPDATE denda SET jumlah_hari = :jumlah_hari, jumlah_denda = :jumlah_denda WHERE dendaID = :dendaID");
    $stmt->bindParam(':dendaID', $dendaID, PDO::PARAM_INT);
    $stmt->bindParam(':jumlah_hari', $jumlah_hari, PDO::PARAM_INT);
    $stmt->bindParam(':jumlah_denda', $jumlah_denda, PDO::PARAM_INT);
    $stmt->execute();

    header("Location:?page=denda&status=update");
    exit;    
}

// Ambil data dari database
$stmt = $conn->query("SELECT * FROM denda");
$data_denda = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML dan CSS untuk tampilan -->

<style>
      .table td, .table th { 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            max-width: 200px; 
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

        .table thead th { background-color: #FF5722; color: #fff; text-align: center; }
        .table tbody tr:hover { background-color: #FFF3E0; }
        .table td, .table th { padding: 8px; }
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
    </style>
<div class="container mt-3">
    <h2 class="text-center" style="color: #FF5722; font-weight: 600; font-size: 1.5rem; position: relative; display: inline-block; padding-bottom: 5px;">
        📚 Denda Buku
    </h2>
    <?php if ($status == 'tambah'): ?>
<div class="alert alert-info alert-dismissible fade show text-center position-relative alert-status">
    Data denda berhasil ditambahkan!
    <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php elseif ($status == 'update'): ?>
<div class="alert alert-info alert-dismissible fade show text-center position-relative alert-status">
    Data denda berhasil diperbarui!
    <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
   <!-- Script notifikasi -->
   <script>
    setTimeout(() => {
        document.querySelectorAll('.alert-status').forEach(el => el.remove());
    }, 3000);
</script>


</div>
<div class="card shadow-lg p-4 border-3" style="border-color: #FF5722; margin-top:10px;">
    <div class="row">
    <div class="col-md-5">
    <h5 style="color: #FF5722; font-weight: 600; font-size: 1.2rem;">
        Tambah / Edit Denda
    </h5>
    <?php if ($total_denda >= 1 && !$editMode): ?>
    <div class="alert alert-info" style="color: #FF5722; border: 1px solid #FF5722; background: #FFF3E0;">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            📌 Data denda sudah ditambahkan. Edit data jika perlu.
        <?php else: ?>
            ⚠️ Hanya admin yang bisa mengakses fitur ini.
        <?php endif; ?>
    </div>
<?php else: ?>
        <form method="POST">
            <input type="hidden" name="<?= $editMode ? 'update' : 'tambah' ?>" value="1">
            <input type="hidden" name="dendaID" value="<?= $editMode ? $editData['dendaID'] : '' ?>">

            <input type="number" name="jumlah_hari" id="jumlah_hari" class="form-control mb-3" value="<?= $editMode ? $editData['jumlah_hari'] : '' ?>" placeholder="Jumlah Hari" required>
            <input type="number" name="jumlah_denda" id="jumlah_denda" class="form-control mb-3" value="<?= $editMode ? $editData['jumlah_denda'] : '' ?>" placeholder="Jumlah Denda" required>

            <button type="submit" class="btn w-100 <?= $editMode ? 'btn-warning' : 'btn-orange' ?>">
                <?= $editMode ? 'Update Denda' : 'Tambah Denda' ?>
            </button>
        </form>
    <?php endif; ?>
</div>
<div class="col-md-7">
        <h4 style="color: #FF5722; font-weight: 600; font-size: 1.2rem; position: relative; display: inline-block; padding-bottom: 5px;">
                                Data Denda
                            </h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                <thead>
    <tr class="text-center">
        <th>Jumlah Hari</th>
        <th>Jumlah Denda</th>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <th>Aksi</th>
        <?php endif; ?>
    </tr>
</thead>
<tbody>
    <?php foreach ($data_denda as $denda) : ?>
        <tr>
            <td><?= $denda['jumlah_hari']; ?></td>
            <td>Rp <?= number_format($denda['jumlah_denda'], 0, ',', '.'); ?></td>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <td>
                    <a href="?page=denda&edit=<?= $denda['dendaID']; ?>" class="btn btn-outline-warning btn-sm" style="border-color: #FF5722; color: #FF5722;">✏️</a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>
