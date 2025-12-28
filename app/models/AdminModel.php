<?php
class AdminModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy tất cả tài khoản kèm họ tên từ bảng nguoidung
    public function getAllAccounts() {
        // JOIN bảng account và nguoidung
        $sql = "SELECT tk.id_user, nd.hoten, tk.email, tk.trangthai 
                FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccountById($id) {
        $sql = "SELECT tk.*, nd.* FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user 
                WHERE tk.id_user = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái (Phê duyệt, Khóa, Mở lại)
    public function updateStatus($id, $status) {
        $sql = "UPDATE account SET trangthai = :status WHERE id_user = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // Xóa tài khoản (Xóa cả 2 bảng)
    public function deleteAccount($id) {
        try {
            $this->db->beginTransaction();
            $this->db->prepare("DELETE FROM users WHERE id_user = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM account WHERE id_user = :id")->execute([':id' => $id]);
            return $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}