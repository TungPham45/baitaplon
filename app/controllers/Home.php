<?php
// app/controllers/Home.php

require_once __DIR__ . '/../models/SanphamModel.php';
require_once __DIR__ . '/../models/CategoriesModel.php';
require_once __DIR__ . '/../models/DuyetSPModel.php';

class Home
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function index($user_id = null)
    {
        // 1. Xử lý logout
        if (isset($_GET['logout']) && $_GET['logout'] == '1') {
            session_destroy();
            header("Location: /baitaplon/Home");
            exit();
        }
        
        // 2. Khởi tạo các Model
        $sanphamModel = new SanphamModel($this->conn);
        $cateModel = new CategoriesModel($this->conn);

        // [QUAN TRỌNG] Xác định User ID:
        // Nếu URL không truyền ID ($user_id là null), thì kiểm tra Session (đã đăng nhập chưa)
        // Nếu Session không có, mới kiểm tra $_GET hoặc coi là khách ('')
        if ($user_id === null) {
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_GET['user_id']) ? trim($_GET['user_id']) : '');
        }
        
        // 3. Lấy tham số tìm kiếm
        $keyword  = isset($_GET['q']) ? trim($_GET['q']) : '';
        $category = isset($_GET['danhmuc']) ? trim($_GET['danhmuc']) : '';
        $address  = isset($_GET['diachi']) ? trim($_GET['diachi']) : '';
        $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit  = 12;
        $offset = ($page - 1) * $limit;

        // 4. Lấy dữ liệu sản phẩm
        $totalProducts = $sanphamModel->countProducts($keyword, $category, $address, '');
        $totalPages    = ($totalProducts > 0) ? ceil($totalProducts / $limit) : 1;
        $products      = $sanphamModel->getProducts($keyword, $category, $address, $offset, $limit, '');

        // 5. Xây dựng cây danh mục
        $parents = $cateModel->getParentCategories();
        $categoryTree = [];
        foreach ($parents as $p) {
            $p['children'] = $cateModel->getSubCategories($p['id_danhmuc']);
            $categoryTree[] = $p;
        }

        // 6. Logic lấy tên danh mục hiện tại
        $currentCategoryName = 'Danh mục';
        if (!empty($category)) {
            foreach ($categoryTree as $cat) {
                if ($cat['id_danhmuc'] == $category) {
                    $currentCategoryName = $cat['ten_danhmuc'];
                    break;
                }
                foreach ($cat['children'] as $child) {
                    if ($child['id_danhmuc'] == $category) {
                        $currentCategoryName = $child['ten_danhmuc'];
                        break;
                    }
                }
            }
        }

        $data = [
            'products'       => $products,
            'categoryTree'   => $categoryTree,
            'currentCatName' => $currentCategoryName, 
            'keyword'        => $keyword,
            'category'       => $category,
            'address'        => $address,
            'page'           => 'list_sanpham',
            'pageNum'        => $page,
            'totalPages'     => $totalPages,
            'totalProducts'  => $totalProducts,
            'user_id'        => $user_id,
            'isLoggedIn'     => !empty($user_id)
        ];

        require_once __DIR__ . '/../views/home.php';
    }

    public function detail_Sanpham($id_sanpham, $user_id = '')
    {
        $productModel = new SanphamModel($this->conn);
        $duyetSPModel = new DuyetSPModel($this->conn); 
        
        $product = $productModel->getProductById($id_sanpham);
        $productImages = $productModel->getProductImages($id_sanpham);
        $productAttributes = $duyetSPModel->getProductAttributes($id_sanpham);
        
        // Logic lấy User ID trong trang chi tiết cũng tương tự
        $userId = !empty($user_id) ? $user_id : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '');

        $data = [
            'product'           => $product,
            'productImages'     => $productImages,
            'productAttributes' => $productAttributes,
            'page'              => 'detail_sanpham',
            'user_id'           => $userId,
            'isLoggedIn'        => !empty($userId)
        ];
        
        require_once __DIR__ . '/../views/home.php';
    }
}
?>