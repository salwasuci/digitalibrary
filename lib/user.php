<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "koneksi.php";

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conn;
    }

    // Registrasi Peminjam
    public function registerPeminjam($username, $password, $confirm_password, $email, $namaLengkap, $alamat) {
        if ($this->isUserExists($username, $email)) {
            return "Username atau email sudah terdaftar.";
        }
    
        // Cek apakah password dan confirm_password cocok
        if ($password !== $confirm_password) {
            return "Konfirmasi password tidak cocok.";
        }
    
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Simpan password asli ke kolom confirm_password
        $sql = "INSERT INTO user (Username, Password, confirm_password, Email, NamaLengkap, Alamat, Role) 
                VALUES (:username, :password, :confirm_password, :email, :namaLengkap, :alamat, 'peminjam')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":confirm_password", $password); // Simpan password asli
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":namaLengkap", $namaLengkap);
        $stmt->bindParam(":alamat", $alamat);
    
        if ($stmt->execute()) {
            return "Registrasi peminjam berhasil!";
        } else {
            return "Terjadi kesalahan, coba lagi.";
        }
    }
    

    // Cek apakah user sudah terdaftar
    private function isUserExists($username, $email) {
        $sql = "SELECT * FROM user WHERE Username = :username OR Email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Login User (Peminjam, Petugas, Admin)
    public function login($username, $password) {
        $sql = "SELECT * FROM user WHERE Username = :username AND Role IN ('peminjam', 'admin', 'petugas')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['NamaLengkap'] = $user['NamaLengkap']; // tambahkan ini
            $_SESSION['role'] = $user['Role'];
            return "Login berhasil sebagai " . $user['Role'] . ".";
        }         else {
            return "Username atau password salah.";
        }
    }

    // Ambil semua pengguna
    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM user");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUserById($id) {
        // Query ambil data berdasarkan UserID
        $sql = "SELECT * FROM user WHERE UserID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    //delete data
    public function deleteUser($userID) {
        $sql = "DELETE FROM user WHERE UserID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userID]); // ✅ PARAMETER DIKIRIM DENGAN ARRAY

    }
    
}
?>
