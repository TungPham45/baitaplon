<?php
// app/controllers/Home.php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SanphamModel.php';

class Home {
    private $db; // Sử dụng duy nhất kết nối MySQLi
    private $userModel;
    private $sanphamModel;

    // SỬA: Chỉ nhận 1 tham số $conn từ index.php
    public function __construct($conn) {
        $this->db = $conn;

        // Khởi tạo các Model với kết nối MySQLi
        $this->userModel = new UserModel($conn); 
        $this->sanphamModel = new SanphamModel($conn);
    }

    /**
     * Trang chủ: Gộp logic Phân quyền + Tìm kiếm/Phân trang
     */
    public function index($user_id = null) {
        // 1. Xử lý logout
        if (isset($_GET['logout']) && $_GET['logout'] == '1') {
            session_unset();
            session_destroy();
            header("Location: /baitaplon/Home");
            exit();
        }

        // 2. Xác định User ID (Ưu tiên Session, sau đó tới tham số/GET)
        if ($user_id === null) {
            $user_id = $_SESSION['user_id'] ?? (isset($_GET['user_id']) ? trim($_GET['user_id']) : '');
        }
        $role = $_SESSION['role'] ?? 'Khách';

        // 3. Phân luồng Admin/User
        if ($role === 'Quản lý') {
            $functionTitle = "Bảng điều khiển Admin";
            require_once __DIR__ . '/../views/admin/dashboard.php';
        } else {
            // Lấy danh sách người dùng gợi ý
            $otherUsers = $this->userModel->getOtherUsers($user_id);

            // Logic tìm kiếm & Phân trang
            $keyword  = isset($_GET['q']) ? trim($_GET['q']) : '';
            $category = isset($_GET['danhmuc']) ? trim($_GET['danhmuc']) : '';
            $address  = isset($_GET['diachi']) ? trim($_GET['diachi']) : '';
            $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) $page = 1;

            $limit  = 12;
            $offset = ($page - 1) * $limit;

            // Gọi các hàm từ SanphamModel theo chuẩn MySQLi
            $totalProducts = $this->sanphamModel->countProducts($keyword, $category, $address, '');
            $totalPages    = ($totalProducts > 0) ? ceil($totalProducts / $limit) : 1;

            $products = $this->sanphamModel->getProducts($keyword, $category, $address, $offset, $limit, '');
            $categories = $this->sanphamModel->getAllCategories();

            // Gộp dữ liệu vào mảng $data
            $data = [
                'products'      => $products,
                'categories'    => $categories,
                'otherUsers'    => $otherUsers,
                'keyword'       => $keyword,
                'category'      => $category,
                'address'       => $address,
                'page'          => 'list_sanpham',
                'pageNum'       => $page,
                'totalPages'    => $totalPages,
                'totalProducts' => $totalProducts,
                'user_id'       => $user_id,
                'isLoggedIn'    => !empty($user_id)
            ];

            // Gọi View Layout chính
            require_once __DIR__ . '/../views/home.php';
        }
    }

    /**
     * Xem chi tiết sản phẩm
     */
    public function detail_Sanpham($id_sanpham, $user_id = '') {
        $userId = !empty($user_id) ? $user_id : ($_SESSION['user_id'] ?? '');

        $product = $this->sanphamModel->getProductById($id_sanpham);
        $productImages = $this->sanphamModel->getProductImages($id_sanpham);

        $data = [
            'product'       => $product,
            'productImages' => $productImages,
            'page'          => 'detail_sanpham',
            'user_id'       => $userId,
            'isLoggedIn'    => !empty($userId)
        ];
        
        require_once __DIR__ . '/../views/home.php';
    }
}