<?php
// app/models/UserModel.php

class UserModel {
    private $con; // Biến lưu trữ kết nối MySQLi

    public function __construct($conn) {
        $this->con = $conn; // Nhận kết nối từ Controller
    }

    /**
     * Lấy thông tin chi tiết người dùng (Join bảng users và account)
     */
    public function getUserById($id_user)
    {
        $id_user = mysqli_real_escape_string($this->con, $id_user);
        
        // ĐÃ ĐỔI: Sử dụng bảng 'account' thay cho 'taikhoan'
        $sql = "SELECT * FROM users 
                LEFT JOIN account ON account.id_user = users.id_user 
                WHERE users.id_user = '$id_user'";
        
        $result = mysqli_query($this->con, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }

<<<<<<< HEAD
=======

>>>>>>> 2562b16aebed4df7dc3b06293e5d7411944c9081
    /**
     * Lấy danh sách những người dùng khác có trạng thái Hoạt động
     */
    public function getOtherUsers($excludeId) {
        $safeId = mysqli_real_escape_string($this->con, $excludeId);

        // ĐÃ ĐỔI: Sử dụng bảng 'account'
        $sql = "SELECT tk.id_user, nd.hoten, nd.avatar, nd.diachi, nd.danhgia 
                FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user 
                WHERE tk.role = 'Người dùng' 
                AND tk.id_user != '$safeId' 
                AND tk.trangthai = 'Hoạt động'";
        
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            die("Lỗi SQL: " . mysqli_error($this->con)); 
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /**
     * Tìm kiếm người dùng theo tên
     */
    public function searchUsers($keyword, $excludeId) {
        $k = mysqli_real_escape_string($this->con, $keyword);
        $id = mysqli_real_escape_string($this->con, $excludeId);

        // ĐÃ ĐỔI: Sử dụng bảng 'account'
        $sql = "SELECT tk.id_user, nd.hoten, nd.avatar, nd.diachi, nd.danhgia 
                FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user 
                WHERE nd.hoten LIKE '%$k%' 
                AND tk.id_user != '$id'
                AND tk.trangthai = 'Hoạt động'";
        
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            die("Lỗi SQL: " . mysqli_error($this->con));
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /**
     * Cập nhật thông tin cá nhân trong bảng users
     */
    public function updateUser($id_user, $hoten, $sdt, $diachi, $gioithieu, $avatarUrl = null) {
        $id_user = mysqli_real_escape_string($this->con, $id_user);
        $hoten = mysqli_real_escape_string($this->con, $hoten);
        $sdt = mysqli_real_escape_string($this->con, $sdt);
        $diachi = mysqli_real_escape_string($this->con, $diachi);
        $gioithieu = mysqli_real_escape_string($this->con, $gioithieu);

        $sql = "UPDATE users SET 
                hoten = '$hoten', 
                sdt = '$sdt', 
                diachi = '$diachi', 
                gioithieu = '$gioithieu'";

        if ($avatarUrl !== null) {
            $avatarUrl = mysqli_real_escape_string($this->con, $avatarUrl);
            $sql .= ", avatar = '$avatarUrl'";
        }

        $sql .= " WHERE id_user = '$id_user'";
        return mysqli_query($this->con, $sql);
    }

    /**
     * Kiểm tra quyền quản lý/admin dựa trên bảng account
     */
    public function isManager($id_user) {
        $user = $this->getUserById($id_user);
        if ($user && isset($user['loaitaikhoan'])) {
            return ($user['loaitaikhoan'] == 'quanly' || $user['loaitaikhoan'] == 'admin');
        }
        return false;
    }

    /**
     * Xác thực đăng nhập (Hỗ trợ cả Hash và Plain text)
     */
    public function authenticate($username, $password) {
        $username = mysqli_real_escape_string($this->con, trim($username));
        
        // ĐÃ ĐỔI: Truy vấn từ bảng 'account'
        $sql = "SELECT * FROM account WHERE email = '$username' AND trangthai = 'Hoạt động'";
        $result = mysqli_query($this->con, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $storedPassword = $user['matkhau'];
            
            // Kiểm tra password đã hash ($2y$ hoặc $2a$)
            if (strpos($storedPassword, '$2y$') === 0 || strpos($storedPassword, '$2a$') === 0) {
                if (password_verify($password, $storedPassword)) {
                    return $user;
                }
            } else {
                // Kiểm tra plain text cho dữ liệu cũ
                if ($password === $storedPassword) {
                    return $user;
                }
            }
        }
        return null;
    }
    public function banUser($userId, $reason) {
            // 1. Lấy thông tin người bị ban trước để kiểm tra quyền
            // Lưu ý: Đảm bảo bảng account có cột 'role' (hoặc 'loaitaikhoan' tùy database của bạn)
            $checkSql = "SELECT role FROM account WHERE id_user = ?";
            
            if ($stmtCheck = $this->con->prepare($checkSql)) {
                $stmtCheck->bind_param("s", $userId);
                $stmtCheck->execute();
                $resultCheck = $stmtCheck->get_result()->fetch_assoc();
                $stmtCheck->close();

                // 🔥 QUAN TRỌNG: Nếu là Admin hoặc Quản lý thì KHÔNG ĐƯỢC ban
                // Bạn hãy đổi 'Admin', 'Quản lý' đúng theo giá trị trong DB của bạn
                if ($resultCheck && ($resultCheck['role'] === 'Admin' || $resultCheck['role'] === 'Quản lý')) {
                    return false; // Trả về false ngay lập tức
                }
            }

            // 2. Nếu không phải Admin thì mới thực hiện lệnh Ban
            $sql = "UPDATE account SET trangthai = 'Bị khóa', ban_reason = ? WHERE id_user = ?";
            
            if ($stmt = $this->con->prepare($sql)) {
                $stmt->bind_param("ss", $reason, $userId);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            } else {
                // Fallback
                $safeReason = mysqli_real_escape_string($this->con, $reason);
                $safeId = mysqli_real_escape_string($this->con, $userId);
                $sqlRaw = "UPDATE account SET trangthai = 'Bị khóa', ban_reason = '$safeReason' WHERE id_user = '$safeId'";
                return mysqli_query($this->con, $sqlRaw);
            }
        }

}
?>