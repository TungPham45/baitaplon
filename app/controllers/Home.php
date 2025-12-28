<?php
// app/controllers/Home.php

// 1. Nạp UserModel sử dụng đường dẫn tuyệt đối dựa trên __DIR__
require_once __DIR__ . '/../models/UserModel.php';

class Home { // Đổi từ HomeController thành Home để khớp với logic định tuyến tại index.php
    private $userModel;

    // Nhận kết nối $conn từ index.php và truyền vào Model
    public function __construct($conn) {
        $this->userModel = new UserModel($conn);
    }

    public function index() {
        // 2. Kiểm tra nếu chưa đăng nhập thì điều hướng về trang Login theo luồng URL mới
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Auth/login"); // Đổi từ /quanlyc2c/Public/... sang /baitaplon/Auth/login
            exit();
        }

        $username = $_SESSION['username'];
        $role = $_SESSION['role'];

        // 3. Dựa vào role để gọi View tương ứng, sử dụng __DIR__ để tránh lỗi đường dẫn
        if ($role === 'Quản lý') {
            // Nạp view dashboard của admin
            require_once __DIR__ . '/../views/admin/dashboard.php';
        } else {
            // Lấy danh sách người dùng khác cho trang chủ người dùng
            $otherUsers = $this->userModel->getOtherUsers($_SESSION['user_id']);
            // Nạp view dashboard của user
            require_once __DIR__ . '/../views/user/dashboard.php';
        }
    }
}