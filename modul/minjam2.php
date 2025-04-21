<?php
require_once '../lib/koneksi.php';
$db = new Database();
$pdo = $db->conn;

// Ambil status notifikasi dari URL (jika ada)
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Ambil tarif denda per hari dari tabel denda
$dendaQuery = $pdo->query("SELECT jumlah_denda FROM denda LIMIT 1");
$dendaData = $dendaQuery->fetch(PDO::FETCH_ASSOC);
$tarifDendaPerHari = $dendaData['jumlah_denda'] ?? 0;

// Proses Update Status Peminjaman
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $update = $pdo->prepare("UPDATE peminjaman SET StatusPeminjaman = ? WHERE PeminjamanID = ?");
    $update->execute([$status, $id]);

    // Redirect dengan notifikasi sukses update
    header("Location:?page=minjam&status=update");
    exit;    
}

// Proses Hapus Data Peminjaman
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $delete = $pdo->prepare("DELETE FROM peminjaman WHERE PeminjamanID = ?");
    $delete->execute([$id]);

    // Redirect dengan notifikasi sukses hapus
    header("Location:?page=minjam&status=hapus");
    exit;
}

// Ambil semua data peminjaman dari database
$query = $pdo->query("
    SELECT 
        p.PeminjamanID,
        u.NamaLengkap,
        b.Judul,
        p.TanggalPeminjaman,
        p.TanggalPengembalian,
        p.StatusPeminjaman
    FROM peminjaman p
    JOIN user u ON p.UserID = u.UserID
    JOIN buku b ON p.BukuID = b.BukuID
    ORDER BY p.PeminjamanID ASC
");

$editId = $_GET['edit'] ?? null;
?>
<!-- =========================
     Styling Tabel dan Button 
========================= -->
<style>
    .text-orange {
        color: #FF5722;
        font-weight: 600;
        font-size: 1.4rem;
        padding-bottom: 5px;
        text-align: left;
    }

    .table-responsive {
        overflow-y: auto;
        overflow-x: auto;
        margin-top: 10px;
    }

    .table {
        font-size: 1rem;
        white-space: nowrap;
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

    .table tbody tr:hover {
        background-color: #FFF3E0;
    }

    .table td, .table th {
        padding: 8px;
    }

    /* Tombol orange solid */
    .btn-orange {
        background-color: #FF5722 !important;
        color: #fff !important;
        border: none;
    }

    .btn-orange:hover {
        background-color: #FF5722;
        transform: scale(1.05);
    }

    /* Tombol batal outline merah */
    .btn-orange-outline {
        border: 1px solid red;
        color: red;
        transition: 0.3s;
    }

    .btn-orange-outline:hover,
    .btn-orange-outline:active {
        background-color: rgb(249, 18, 18);
        color: white;
    }
</style>
<!-- Tampilan Daftar Peminjaman dan Denda -->
<div class="col-12 mx-auto">
    <div class="card shadow-lg p-4 card-custom border-3"
         style="border-color: #FF5722; margin-top:10px; max-width: 900px; margin-left: auto; margin-right: auto;">
        
        <h4 class="text-orange">📚 Daftar Peminjam dan Denda</h4>

        <!--Notifikasi jika berhasil update atau hapus-->
        <?php if ($status == 'update'): ?>
        <div class="alert alert-info alert-dismissible fade show text-center position-relative alert-status">
            Status berhasil diperbarui!
            <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php elseif ($status == 'hapus'): ?>
        <div class="alert alert-info alert-dismissible fade show text-center position-relative alert-status">
            Data Peminjam berhasil dihapus!
            <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Script untuk auto-hide notifikasi -->
        <script>
            setTimeout(() => {
                document.querySelectorAll('.alert-status').forEach(el => el.remove());
            }, 3000);
        </script>

        <!-- Tabel data peminjam-->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Keterlambatan (Hari)</th>
                        <th>Denda (Rp)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $today = new DateTime();

                while ($data = $query->fetch(PDO::FETCH_ASSOC)):
                    $tglKembali = new DateTime($data['TanggalPengembalian']);
                    $status = $data['StatusPeminjaman'];
                    $telat = 0;
                    $denda = 0;

                    // Hitung denda jika terlambat
                    if ($status === 'Dipinjam' && $tglKembali < $today) {
                        $telat = $tglKembali->diff($today)->days;
                        $denda = $telat * $tarifDendaPerHari;
                    }

                    $id = $data['PeminjamanID'];
                ?>
                <tr class="text-center">
                    <td><?= $no++ ?></td>
                    <td><?= $data['NamaLengkap'] ?></td>
                    <td><?= $data['Judul'] ?></td>
                    <td><?= $data['TanggalPeminjaman'] ?></td>
                    <td><?= $data['TanggalPengembalian'] ?></td>
                    <td><?= $status ?></td>
                    <td><?= $telat ?></td>
                    <td>Rp <?= number_format($denda, 0, ',', '.') ?></td>
                    <td>
                        <a href="?page=minjam&edit=<?= $id ?>" class="btn btn-outline-warning btn-sm" style="border-color: #FF5722; color: #FF5722;">✏️</a>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="?page=minjam&hapus=<?= $id ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin hapus data?')" style="border-color: #FF5722; color: #FF5722;">🗑️  <?php endif; ?></a>
                    </td>
                </tr>

                <!-- Form Update Status (inline) -->
                <?php if ($editId == $id): ?>
                <tr>
                    <td colspan="9">
                        <div class="card border-3 p-2" style="overflow-x: auto; font-size: 0.90rem; border-color:#FF5722;">
                            <form method="POST" class="d-flex flex-wrap align-items-center gap-2">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="hidden" name="update" value="1">

                                <label class="mb-0 fw-bold">
                                    <i class="bi bi-pencil-square me-1"></i>Status:
                                </label>

                                <select name="status" class="form-select form-select-sm w-auto">
                                    <option value="Dipinjam" <?= $status === 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                                    <option value="Dikembalikan" <?= $status === 'Dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
                                </select>

                                <button type="submit" class="btn btn-sm btn-orange">
                                    <i class="bi bi-check-circle me-1"></i> Simpan
                                </button>

                                <a href="?page=minjam" class="btn btn-sm btn-orange-outline">
                                    <i class="bi bi-x-circle me-1"></i> Batal
                                </a>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
