<?php
// app/controllers/Home.php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SanphamModel.php';

class Home {
    private $pdo;    // Dùng cho logic của bạn (Quản lý)
    private $mysqli; // Dùng cho logic người khác (Sản phẩm/Người dùng)
    private $userModel;
    private $sanphamModel;

    // SỬA: Nhận 2 tham số kết nối
    public function __construct($pdo, $mysqli) {
        $this->pdo = $pdo;
        $this->mysqli = $mysqli;

        // Model nào dùng SQL thường thì truyền $mysqli vào
        $this->userModel = new UserModel($mysqli); 
        $this->sanphamModel = new SanphamModel($mysqli);
    }

    /**
     * Trang chủ: Gộp logic Phân quyền (của bạn) + Tìm kiếm/Phân trang (người khác)
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
            // Lấy danh sách người dùng gợi ý (Logic của bạn)
            $otherUsers = $this->userModel->getOtherUsers($user_id);

            // Logic tìm kiếm & Phân trang (Logic người khác)
            $keyword  = isset($_GET['q']) ? trim($_GET['q']) : '';
            $category = isset($_GET['danhmuc']) ? trim($_GET['danhmuc']) : '';
            $address  = isset($_GET['diachi']) ? trim($_GET['diachi']) : '';
            $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) $page = 1;

            $limit  = 12;
            $offset = ($page - 1) * $limit;

            $totalProducts = $this->sanphamModel->countProducts($keyword, $category, $address, '');
            $totalPages    = ($totalProducts > 0) ? ceil($totalProducts / $limit) : 1;

            $products = $this->sanphamModel->getProducts($keyword, $category, $address, $offset, $limit, '');
            $categories = $this->sanphamModel->getAllCategories();

            // Gộp tất cả dữ liệu vào mảng $data theo cấu trúc người khác yêu cầu
            $data = [
                'products'      => $products,
                'categories'    => $categories,
                'otherUsers'    => $otherUsers, // Thêm dữ liệu của bạn vào đây
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
        // Lấy User ID từ session nếu không truyền vào
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