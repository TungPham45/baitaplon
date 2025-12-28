<?php
class AuthModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. CHỨC NĂNG ĐĂNG NHẬP
    public function login($identifier) {
        // Chỉ nhận 1 tham số để tìm tài khoản
        $sql = "SELECT * FROM account WHERE username = :id OR email = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $identifier]);
        
        // Trả về toàn bộ dòng dữ liệu của tài khoản đó (bao gồm cả cột matkhau và trangthai)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkExists($username, $email) {
        $sql = "SELECT * FROM account WHERE username = :user OR email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user' => $username, ':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($accountData, $personalData) {
        try {
            $this->db->beginTransaction();

            // 1. Chèn vào bảng account
            $sqlAcc = "INSERT INTO account (id_user, username, password, email, role, trangthai, ngaytao) 
                    VALUES (:id, :user, :pass, :email, 'Người dùng', 'Chờ duyệt', NOW())";
            
            // Tạo ID thủ công nếu id_user là VARCHAR (ví dụ US + timestamp)
            $newId = 'US' . time(); 
            
            $stmtAcc = $this->db->prepare($sqlAcc);
            $stmtAcc->execute([
                ':id'    => $newId,
                ':user'  => $accountData['username'],
                ':pass'  => password_hash($accountData['password'], PASSWORD_DEFAULT),
                ':email' => $accountData['email']
            ]);

            // 2. Chèn vào bảng users (Đảm bảo đủ các cột: gioitinh, ngaysinh)
            $sqlProfile = "INSERT INTO users (id_user, hoten, sdt, diachi, danhgia) 
                        VALUES (:id, :name, :phone, :address, 0.0)";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->execute([
                ':id'      => $newId,
                ':name'    => $personalData['name'],
                ':phone'   => $personalData['phone'],
                ':address' => $personalData['address']
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // 3. CHỨC NĂNG QUÊN MẬT KHẨU
    // Kiểm tra email tồn tại để gửi OTP
    public function findByEmail($email) {
        $sql = "SELECT * FROM account WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật mật khẩu mới sau khi xác thực OTP thành công
    public function updatePassword($email, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        // Sửa tên bảng từ taikhoan thành account
        $sql = "UPDATE account SET password = :password WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':password' => $hashedPassword, ':email' => $email]);
    }
}