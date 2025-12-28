<?php
class ProfileModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function getProfile($userId) {
        // Sử dụng tên bảng account và users mới
        $sql = "SELECT a.username, a.email, a.role, a.trangthai, a.ngaytao, 
                    u.hoten, u.sdt, u.diachi, u.avatar, u.gioithieu, u.danhgia 
                FROM account a 
                JOIN users u ON a.id_user = u.id_user 
                WHERE a.id_user = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Trả về mảng hoặc false nếu lỗi
    }

    public function updateProfile($userId, $data) {
        // Cập nhật các trường mới bao gồm ảnh đại diện và giới thiệu
        $sql = "UPDATE users SET 
                hoten = :name, 
                sdt = :phone, 
                diachi = :address, 
                avatar = :avatar, 
                gioithieu = :bio 
                WHERE id_user = :id";
    
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name'    => $data['name'],
            ':phone'   => $data['phone'],
            ':address' => $data['address'],
            ':avatar'  => $data['avatar'],
            ':bio'     => $data['bio'],
            ':id'      => $userId
        ]);
    }
}