<?php
// app/models/UserModel.php

class UserModel {
    private $db; // Đây là đối tượng MySQLi

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Lấy danh sách những người dùng khác có trạng thái Hoạt động
     */
    public function getOtherUsers($excludeId) {
        // Sử dụng mysqli_real_escape_string để bảo mật cho SQL thường
        $safeId = mysqli_real_escape_string($this->db, $excludeId);

        $sql = "SELECT tk.id_user, nd.hoten, nd.avatar, nd.diachi, nd.danhgia 
                FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user 
                WHERE tk.role = 'Người dùng' 
                AND tk.id_user != '$safeId' 
                AND tk.trangthai = 'Hoạt động'";
        
        $result = mysqli_query($this->db, $sql);
        
        // SỬA LỖI: Sử dụng $this->db->error của MySQLi thay vì errorInfo() của PDO
        if (!$result) {
            die("Lỗi SQL: " . $this->db->error); 
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    
    /**
     * Tìm kiếm người dùng theo tên
     */
    public function searchUsers($keyword, $excludeId) {
        $k = mysqli_real_escape_string($this->db, $keyword);
        $id = mysqli_real_escape_string($this->db, $excludeId);

        $sql = "SELECT tk.id_user, nd.hoten, nd.avatar, nd.diachi, nd.danhgia 
                FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user 
                WHERE nd.hoten LIKE '%$k%' 
                AND tk.id_user != '$id'";
        
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            die("Lỗi SQL: " . $this->db->error);
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}