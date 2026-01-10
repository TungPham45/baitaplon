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

