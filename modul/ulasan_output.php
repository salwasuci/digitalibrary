<?php
require_once "../lib/koneksi.php"; // Pastikan path benar

$db = new Database();
$conn = $db->conn; 

$ulasanQuery = $conn->query("SELECT ulasanbuku.*, user.NamaLengkap, buku.Judul 
FROM ulasanbuku
JOIN user ON ulasanbuku.UserID = user.UserID
JOIN buku ON ulasanbuku.BukuID = buku.BukuID
ORDER BY ulasanbuku.UlasanID DESC");
?>

<style>
    .text-orange {
        color: #FF5722;
        font-weight: 600;
        font-size: 1.4rem;
        position: relative;
        display: inline-block;
        padding-bottom: 5px;
        text-align:left;
    }
      .table-responsive {
            max-height: 300px;
            overflow-y: auto;
            margin-top:10px;
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
        </style>
<div class="col-12 mx-auto">
    <div class="card shadow-lg p-4 card-custom border-3" style="border-color: #FF5722; margin-top:10px;">
        <h4 class="text-orange">📚 Daftar Ulasan Buku</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
            <thead>
    <tr>
        <th>No</th>
        <th>Peminjam</th>
        <th>Judul Buku</th>
        <th>Ulasan</th>
        <th>Rating</th>
        <?php if ($_SESSION['role'] === 'admin') : ?>
            <th>Aksi</th>
        <?php endif; ?>
    </tr>
</thead>

<tbody>
<?php $no = 1; ?>
<?php while ($row = $ulasanQuery->fetch(PDO::FETCH_ASSOC)) { ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($row['NamaLengkap']); ?></td>
        <td><?= htmlspecialchars($row['Judul']); ?></td>
        <td><?= htmlspecialchars($row['Ulasan']); ?></td>
        <td><?= htmlspecialchars($row['Rating']); ?></td>
        <?php if ($_SESSION['role'] === 'admin') : ?>
            <td>
                <a href="ulasan_inputan.php?delete=<?= $row['UlasanID'] ?>" 
                   class="btn btn-outline-danger btn-sm" 
                   onclick="return confirm('Yakin hapus data?')" 
                   style="border-color: #FF5722; color: #FF5722;">🗑️</a>
            </td>
        <?php endif; ?>
    </tr>
<?php } ?>
</tbody>

            </table>
        </div>
    </div>
</div>


