<?php
class AuthModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($identifier) {
        $sql = "SELECT * FROM account WHERE username = ? OR email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function checkExists($username, $email) {
        $sql = "SELECT * FROM account WHERE username = ? OR email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function register($accountData, $personalData) {
        try {
            $this->db->begin_transaction();

            $newId = 'US' . time();

            $sqlAcc = "INSERT INTO account (id_user, username, password, email, role, trangthai, ngaytao) 
                    VALUES (?, ?, ?, ?, 'Người dùng', 'Chờ duyệt', NOW())";
            
            $stmtAcc = $this->db->prepare($sqlAcc);
            $hashedPassword = password_hash($accountData['password'], PASSWORD_DEFAULT);
            $stmtAcc->bind_param("ssss", 
                $newId,
                $accountData['username'],
                $hashedPassword,
                $accountData['email']
            );
            $stmtAcc->execute();
            $stmtAcc->close();

            $sqlProfile = "INSERT INTO users (id_user, hoten, sdt, diachi, danhgia) 
                        VALUES (?, ?, ?, ?, 0.0)";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->bind_param("ssss", 
                $newId,
                $personalData['name'],
                $personalData['phone'],
                $personalData['address']
            );
            $stmtProfile->execute();
            $stmtProfile->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM account WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updatePassword($email, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE account SET password = ? WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $email);
        return $stmt->execute();
    }
}
?>
