<?php
require_once '../lib/koneksi.php'; // Pastikan koneksi sudah ada sebelum dipakai

class User {
    private $conn;
    private $table = "user";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPeminjamUsers() {
        try {
            $query = "SELECT NamaLengkap FROM " . $this->table . " WHERE Role = 'peminjam'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Query Error: " . $e->getMessage());
        }
    }
}

// Pastikan objek User dibuat setelah $db tersedia
if (!$db) {
    die("Koneksi database gagal.");
}

$user = new User($db);
$peminjamUsers = $user->getPeminjamUsers();

echo "<h2>Daftar Pengguna dengan Role Peminjam</h2>";

if (!empty($peminjamUsers)) {
    echo "<ul>";
    foreach ($peminjamUsers as $row) {
        echo "<li><strong>" . htmlspecialchars($row['NamaLengkap']) . "</strong> - " .
             htmlspecialchars($row['Email']) . " - " .
             htmlspecialchars($row['Alamat']) . "</li>";
    
    }
    echo "</ul>";
} else {
    echo "<p>Tidak ada pengguna dengan role peminjam.</p>";
}
?>
