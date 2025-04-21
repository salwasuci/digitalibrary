<?php
require_once "../lib/koneksi.php";
$db = new Database();
$pdo = $db->conn;
?>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  body {
    background-color: #f8f9fa;
  }

  .card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.3s;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }

  .card h4 {
    background: linear-gradient(90deg, #FF5722, #FF8A65);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: bold;
  }

  .display-6 {
    color: #334155;
    font-weight: bold;
  }

  .table {
    border: 2px solid #FF5722;
    border-radius: 12px;
    overflow: hidden;
  }

  .table th {
    background-color: #FF5722;
    color: white;
    border: 1px solid #ff784e;
  }

  .table td {
    border: 1px solid #ffd2c4;
  }

  .table-hover tbody tr:hover {
    background-color: #fdf3ee;
  }

  .list-group-item {
    background-color: #fdfdfd;
    transition: background-color 0.2s ease-in-out;
    border: 1px solid #f1f1f1;
  }

  .list-group-item:hover {
    background-color: #fff0e8;
  }
</style>


<div class="container py-5" style="margin-top:-40px;">
  <div class="row g-4">

    <!-- Total Buku -->
    <div class="col-md-4">
      <div class="card p-3 bg-light">
        <h4>Total Buku</h4>
        <p class="display-6">
        <?php
        $stmt = $pdo->query("SELECT COUNT(*) FROM buku");
        echo $stmt->fetchColumn();
        ?>
        </p>
      </div>
    </div>

    <!-- Total User per Role -->
    <div class="col-md-4">
      <div class="card p-3 bg-light">
        <h4>User Berdasarkan Role</h4>
        <ul class="list-group list-group-flush">
        <?php
        $stmt = $pdo->query("SELECT Role, COUNT(*) as jumlah FROM user GROUP BY Role");
        foreach($stmt as $row){
          echo "<li class='list-group-item d-flex justify-content-between'><span>{$row['Role']}</span><span>{$row['jumlah']}</span></li>";
        }
        ?>
        </ul>
      </div>
    </div>

    <!-- Jumlah Peminjaman Aktif -->
    <div class="col-md-4">
      <div class="card p-3 bg-light">
        <h4>Peminjaman Aktif</h4>
        <p class="display-6">
        <?php
        $stmt = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE StatusPeminjaman = 'Dipinjam'");
        echo $stmt->fetchColumn();
        ?>
        </p>
      </div>
    </div>

    <!-- Jumlah Denda -->
    <div class="col-md-4">
      <div class="card p-3 bg-light">
        <h4>Total Denda 1 hari</h4>
        <p class="display-6 text-danger">
        <?php
        $stmt = $pdo->query("SELECT SUM(jumlah_denda) FROM denda");
        echo "Rp " . number_format($stmt->fetchColumn(), 0, ',', '.');
        ?>
        </p>
      </div>
    </div>

    <!-- Rating Buku -->
    <div class="col-md-8">
      <div class="card p-3 bg-light">
        <h4>Buku dengan Rating Tertinggi</h4>
        <table class="table table-hover">
          <thead>
            <tr><th>Judul Buku</th><th>Rating Rata-rata</th></tr>
          </thead>
          <tbody>
          <?php
          $stmt = $pdo->query("
            SELECT b.Judul, AVG(u.Rating) AS RataRating
            FROM ulasanbuku u
            JOIN buku b ON u.BukuID = b.BukuID
            GROUP BY b.BukuID
            ORDER BY RataRating DESC
            LIMIT 5
          ");
          foreach($stmt as $row){
            echo "<tr><td>{$row['Judul']}</td><td>" . number_format($row['RataRating'], 2) . "</td></tr>";
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Koleksi Terpopuler -->
    <div class="col-md-12">
      <div class="card p-3 bg-light">
        <h4>Buku Paling Banyak Dijadikan Koleksi</h4>
        <table class="table table-striped">
          <thead><tr><th>Judul Buku</th><th>Jumlah Koleksi</th></tr></thead>
          <tbody>
          <?php
          $stmt = $pdo->query("
            SELECT b.Judul, COUNT(*) AS JumlahKoleksi
            FROM koleksipribadi k
            JOIN buku b ON k.BukuID = b.BukuID
            GROUP BY k.BukuID
            ORDER BY JumlahKoleksi DESC
            LIMIT 5
          ");
          foreach($stmt as $row){
            echo "<tr><td>{$row['Judul']}</td><td>{$row['JumlahKoleksi']}</td></tr>";
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

