<?php
class ProfileModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getProfile($userId) {
        $sql = "SELECT a.username, a.email, a.role, a.trangthai, a.ngaytao, 
                    u.hoten, u.sdt, u.diachi, u.avatar, u.gioithieu, u.danhgia 
                FROM account a 
                JOIN users u ON a.id_user = u.id_user 
                WHERE a.id_user = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateProfile($userId, $data) {
        $sql = "UPDATE users SET 
                hoten = ?, 
                sdt = ?, 
                diachi = ?, 
                avatar = ?, 
                gioithieu = ? 
                WHERE id_user = ?";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssss", 
            $data['name'],
            $data['phone'],
            $data['address'],
            $data['avatar'],
            $data['bio'],
            $userId
        );
        return $stmt->execute();
    }
}
?>
