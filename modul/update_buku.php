<?php
require_once "../lib/koneksi.php";

class Buku {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function getBukuById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM buku WHERE BukuID = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBuku($data) {
        try {
            $sql = "UPDATE buku SET 
                        Judul = :judul, 
                        gambar = :gambar, 
                        Penulis = :penulis, 
                        Penerbit = :penerbit, 
                        TahunTerbit = :tahunTerbit 
                    WHERE BukuID = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id'          => $data['id'],
                ':judul'       => $data['judul'],
                ':gambar'      => $data['gambar'],
                ':penulis'     => $data['penulis'],
                ':penerbit'    => $data['penerbit'],
                ':tahunTerbit' => $data['tahunTerbit']
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error saat update: " . $e->getMessage();
            return false;
        }
    }
}

$buku = new Buku();

// Ambil data buku berdasarkan ID
if (isset($_GET['id'])) {
    $dataBuku = $buku->getBukuById($_GET['id']);
    if (!$dataBuku) {
        echo "Data buku tidak ditemukan!";
        exit;
    }
}

// Proses update data buku
if (isset($_POST["update"])) {
    $gambar = $dataBuku['gambar'];

    // Jika ada gambar baru yang diunggah
    if (!empty($_FILES['gambar']['name'])) {
        $folder = "../asset/upload/";

        // Buat folder jika belum ada
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $gambar = uniqid() . '_' . $_FILES['gambar']['name'];
        $path = $folder . $gambar;

        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $path)) {
            echo "Gagal mengupload gambar.";
            exit;
        }
    }

    $data = [
        'id'          => $_POST['id'],
        'judul'       => $_POST['judul'],
        'gambar'      => $gambar,
        'penulis'     => $_POST['penulis'],
        'penerbit'    => $_POST['penerbit'],
        'tahunTerbit' => $_POST['tahunTerbit']
    ];
    $role = $_SESSION['role']; // pastikan ini diset waktu login

    if ($buku->updateBuku($data)) {
        if ($role == 'admin') {
            header("Location: admin.php?page=buku&status=updated");
        } else {
            header("Location: petugas.php?page=buku&status=updated");
        }
        exit();
    } else {
        echo "Gagal memperbarui data.";
    }
    
}
?>

<style>
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 130vh;
    background-color: #f5f5f5;
}

.card {
    background-color: #fff;
    border: 3px solid #FF5722;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 600px;
    padding: 30px;
    text-align: center;
}

h5 {
    color: #FF5722;
    font-weight: 600;
    margin-bottom: 20px;
}

label {
    display: block;
    text-align: left;
    font-weight: 500;
    color: #FF5722;
    margin-top: 10px;
}

input[type="text"],
input[type="email"] {
    width: 100%;
    padding: 10px;
    border: 2px solid #FF5722;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.3s ease;
}

input:focus {
    border-color: #FF4500;
}

.btn-orange {
    width: 100%;
    background-color: #FF5722;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-orange:hover {
    background-color: #FF4500;
}
</style>

<div class="container">
    <div class="card">
        <h5>Update Data Buku</h5>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($dataBuku['BukuID']) ?>">

            <label for="judul">Judul Buku</label>
            <input type="text" name="judul" value="<?= htmlspecialchars($dataBuku['Judul']) ?>" class="form-control mb-3" required>

            <label for="gambar">Gambar Buku</label><br>
            <img src="../asset/upload/<?= htmlspecialchars($dataBuku['gambar']) ?>" width="100" height="100" class="mb-2" style="border-radius: 8px;">
            <input type="file" name="gambar" class="form-control mb-3">

            <label for="penulis">Penulis Buku</label>
            <input type="text" name="penulis" value="<?= htmlspecialchars($dataBuku['Penulis']) ?>" class="form-control mb-3" required>

            <label for="penerbit">Penerbit Buku</label>
            <input type="text" name="penerbit" value="<?= htmlspecialchars($dataBuku['Penerbit']) ?>" class="form-control mb-3" required>

            <label for="tahunTerbit">Tahun Terbit</label>
            <input type="text" name="tahunTerbit" value="<?= htmlspecialchars($dataBuku['TahunTerbit']) ?>" class="form-control mb-3" required>

            <button type="submit" name="update" class="btn-orange">
                Perbarui Data
            </button>
        </form>
    </div>
</div>
