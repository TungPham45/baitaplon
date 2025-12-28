<?php
// app/controllers/User.php

// 1. Nạp ProfileModel với đường dẫn mới
require_once __DIR__ . '/../models/ProfileModel.php';

class User { // Đổi tên từ UserController thành User để khớp index.php
    private $profileModel;
    private $db; // Lưu lại kết nối để dùng cho các Model nạp thêm

    public function __construct($conn) {
        // Kiểm tra đăng nhập theo luồng URL mới
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Auth/login");
            exit();
        }
        $this->db = $conn;
        $this->profileModel = new ProfileModel($conn);
    }

    /**
     * Xem hồ sơ cá nhân
     */
    public function profile($targetId = null) {
        $myId = $_SESSION['user_id'];
        $viewId = $targetId ?? $myId; 
        
        $user = $this->profileModel->getProfile($viewId);
        if (!$user) { 
            echo "Người dùng không tồn tại"; 
            return; 
        }

        $isMine = ($myId == $viewId);

        // DỮ LIỆU MINH HỌA SẢN PHẨM
        $mockActiveProducts = [
            ['id' => 1, 'title' => 'iPhone 13 Pro Max', 'price' => '15.000.000 đ', 'img' => 'p1.jpg', 'time' => '2 giờ trước', 'loc' => 'Cần Thơ'],
            ['id' => 2, 'title' => 'Macbook Pro M1', 'price' => '22.500.000 đ', 'img' => 'p2.jpg', 'time' => '5 giờ trước', 'loc' => 'TP.HCM']
        ];
        $mockSoldProducts = [
            ['id' => 3, 'title' => 'Máy ảnh Canon 700D', 'price' => '6.000.000 đ', 'img' => 'p3.jpg', 'time' => '1 ngày trước', 'loc' => 'Cần Thơ']
        ];

        // Nạp view bằng đường dẫn tuyệt đối
        require_once __DIR__ . '/../views/user/profile_view.php';
    }
    
    /**
     * Chỉnh sửa thông tin cá nhân
     */
    public function editProfile() {
        $userId = $_SESSION['user_id'];
        $user = $this->profileModel->getProfile($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $avatarName = $user['avatar']; 

            // Xử lý Upload ảnh vào folder public mới
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName = $_FILES['avatar']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../../public/uploads/avatars/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0777, true);
                    }

                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        if ($avatarName && $avatarName != 'default.png' && file_exists($uploadFileDir . $avatarName)) {
                            unlink($uploadFileDir . $avatarName);
                        }
                        $avatarName = $newFileName;
                    }
                }
            }

            // Loại bỏ gioitinh và ngaysinh vì CSDL không có
            $data = [
                'name'     => $_POST['fullname'],
                'phone'    => $_POST['sdt'],
                'address'  => $_POST['diachi'],
                'avatar'   => $avatarName,
                'bio'      => $_POST['gioithieu']
            ];

            if ($this->profileModel->updateProfile($userId, $data)) {
                $_SESSION['success'] = "Cập nhật hồ sơ thành công!";
                header("Location: /baitaplon/User/profile");
                exit();
            }
        }

        require_once __DIR__ . '/../views/user/edit_profile_view.php';
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword() {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldPass = $_POST['old_password'];
            $newPass = $_POST['new_password'];
            $confirmPass = $_POST['confirm_password'];

            $user = $this->profileModel->getProfile($_SESSION['user_id']);
            
            if (!password_verify($oldPass, $user['password'])) {
                $error = "Mật khẩu cũ không chính xác!";
            } elseif ($newPass !== $confirmPass) {
                $error = "Xác nhận mật khẩu mới không khớp!";
            } else {
                // Nạp AuthModel theo cấu trúc folder mới
                require_once __DIR__ . '/../models/AuthModel.php';
                $authModel = new AuthModel($this->db);
                if ($authModel->updatePassword($user['email'], $newPass)) {
                    $success = "Đổi mật khẩu thành công!";
                } else {
                    $error = "Có lỗi xảy ra, vui lòng thử lại.";
                }
            }
        }

        require_once __DIR__ . '/../views/user/change_password.php';
    }
}