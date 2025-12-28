<?php
// app/models/UserModel.php

class UserModel {
    private $db;

    // SỬA: Nhận kết nối $db từ Controller truyền vào thay vì tự tạo mới
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Lấy danh sách những người dùng khác có trạng thái Hoạt động
     */
    public function getOtherUsers($excludeId) {
        // Đảm bảo tên bảng account và users đã được tạo trong DB
        $sql = "SELECT tk.id_user, nd.hoten, nd.avatar, nd.diachi, nd.danhgia 
                FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user 
                WHERE tk.role = 'Người dùng' 
                AND tk.id_user != :excludeId 
                AND tk.trangthai = 'Hoạt động'";
        
        $stmt = $this->db->prepare($sql);
        
        // Kiểm tra nếu prepare thất bại (nguyên nhân gây lỗi execute() on bool)
        if (!$stmt) {
            $errorInfo = $this->db->errorInfo();
            die("Lỗi SQL trong getOtherUsers: " . $errorInfo[2]);
        }

        $stmt->execute([':excludeId' => $excludeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tìm kiếm người dùng theo tên
     */
    public function searchUsers($keyword, $excludeId) {
        $sql = "SELECT tk.id_user, nd.hoten, nd.avatar, nd.diachi, nd.danhgia 
                FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user 
                WHERE nd.hoten LIKE :keyword 
                AND tk.id_user != :excludeId";
        
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            $errorInfo = $this->db->errorInfo();
            die("Lỗi SQL trong searchUsers: " . $errorInfo[2]);
        }

        $stmt->execute([':keyword' => "%$keyword%", ':excludeId' => $excludeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}