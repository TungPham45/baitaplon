<?php
// app/config/ConnectDB.php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'quanlyc2c';

// --- CÁCH 1: KẾT NỐI MYSQLI (Dành cho người khác dùng) ---
$conn_mysqli = new mysqli($host, $user, $pass, $db);
if ($conn_mysqli->connect_error) {
    die("MySQLi Connection failed: " . $conn_mysqli->connect_error);
}
$conn_mysqli->set_charset("utf8mb4");

// --- CÁCH 2: KẾT NỐI PDO (Dành cho bạn dùng) ---
try {
    $conn_pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}
?>