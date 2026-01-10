<?php
class AdminModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllAccounts() {
        $sql = "SELECT tk.id_user, nd.hoten, tk.email, tk.trangthai 
                FROM account tk 
                JOIN users nd ON CAST(tk.id_user AS CHAR) = CAST(nd.id_user AS CHAR)";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAccountById($id) {
        $sql = "SELECT tk.id_user, nd.id_user as nd_id_user, tk.username, tk.email, tk.role, tk.trangthai, tk.ngaytao, 
                       nd.hoten, nd.sdt, nd.diachi, nd.avatar, nd.gioithieu, nd.danhgia 
                FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user 
                WHERE tk.id_user = CAST(? AS CHAR)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result;
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE account SET trangthai = ? WHERE id_user = CAST(? AS CHAR)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $status, $id);
        return $stmt->execute();
    }

    public function deleteAccount($id) {
        try {
            $this->db->begin_transaction();

            $stmt1 = $this->db->prepare("DELETE FROM users WHERE id_user = CAST(? AS CHAR)");
            $stmt1->bind_param("s", $id);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $this->db->prepare("DELETE FROM account WHERE id_user = CAST(? AS CHAR)");
            $stmt2->bind_param("s", $id);
            $stmt2->execute();
            $stmt2->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
}
?>
