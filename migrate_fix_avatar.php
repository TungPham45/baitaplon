<?php
/**
 * Script migration để sửa đường dẫn avatar trong database
 * Chạy script này một lần để cập nhật các avatar có đường dẫn đầy đủ
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'quanlyc2c';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Cập nhật bảng users - trích xuất tên file từ đường dẫn đầy đủ
$sql = "SELECT id_user, avatar FROM users WHERE avatar LIKE 'public/uploads/avatars/%'";
$result = $conn->query($sql);

$updatedCount = 0;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_user = $row['id_user'];
        $oldAvatar = $row['avatar'];
        
        // Trích xuất tên file từ đường dẫn
        $newAvatar = basename($oldAvatar);
        
        // Cập nhật database
        $updateSql = "UPDATE users SET avatar = '" . $conn->real_escape_string($newAvatar) . "' WHERE id_user = '" . $conn->real_escape_string($id_user) . "'";
        
        if ($conn->query($updateSql) === TRUE) {
            echo "Đã cập nhật user $id_user: $oldAvatar -> $newAvatar<br>";
            $updatedCount++;
        }
    }
}

echo "<br>";
echo "Tổng số avatar đã cập nhật: $updatedCount";
$conn->close();
