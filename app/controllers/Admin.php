<?php
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/ProfileModel.php';
require_once __DIR__ . '/../models/DuyetSPModel.php';

class Admin {
    private $adminModel;
    private $profileModel;
    private $duyetSPModel;
    private $conn;

    public function __construct($conn) {
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $isApiRequest = strpos($url, 'getDetail') !== false ||
                        strpos($url, 'updateStatus') !== false ||
                        strpos($url, 'deleteAccount') !== false ||
                        strpos($url, 'getPendingProducts') !== false ||
                        strpos($url, 'getProductDetail') !== false ||
                        strpos($url, 'approve') !== false ||
                        strpos($url, 'reject') !== false ||
                        strpos($url, 'searchAccounts') !== false ||
                        strpos($url, 'exportProductStatistics') !== false ||
                        strpos($url, 'stopSelling') !== false ||
                        strpos($url, 'approveProduct') !== false ||
                        strpos($url, 'rejectProduct') !== false;

        if (!$isApiRequest) {
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Quản lý') {
                header("Location: /baitaplon/Auth/login");
                exit();
            }
        }
        $this->conn = $conn;
        $this->adminModel = new AdminModel($conn);
        $this->profileModel = new ProfileModel($conn);
        $this->duyetSPModel = new DuyetSPModel($conn);
    }

    public function profile() {
        $userId = $_SESSION['user_id'];
        $user = $this->profileModel->getProfile($userId); 

        $functionTitle = "Hồ sơ Quản trị viên";
        $contentView = __DIR__ . '/../views/admin/profile/show.php'; 
        require_once __DIR__ . '/../views/admin/dashboard.php'; 
    }

    public function editProfile() {
        $userId = $_SESSION['user_id'];
        $user = $this->profileModel->getProfile($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $avatarName = $user['avatar']; 

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
                        if ($avatarName && $avatarName !== 'default.png' && file_exists($uploadFileDir . $avatarName)) {
                            unlink($uploadFileDir . $avatarName);
                        }
                        $avatarName = $newFileName;
                    }
                }
            }

            $data = [
                'name'    => $_POST['fullname'],
                'phone'   => $_POST['sdt'],
                'address' => $_POST['diachi'],
                'avatar'  => $avatarName,
                'bio'     => $_POST['gioithieu']
            ];

            if ($this->profileModel->updateProfile($userId, $data)) {
                $_SESSION['success'] = "Cập nhật thành công!";
                header("Location: /baitaplon/Admin/profile");
                exit();
            }
        }
        
        $functionTitle = "Chỉnh sửa hồ sơ";
        $contentView = __DIR__ . '/../views/admin/profile/edit.php'; 
        require_once __DIR__ . '/../views/admin/dashboard.php'; 
    }

    public function dashboard() {
        $active_page = 'user_management';
        
        $hoten = isset($_GET['hoten']) ? trim($_GET['hoten']) : '';
        $trangthai = isset($_GET['trangthai']) ? $_GET['trangthai'] : 'all';
        
        if (!empty($hoten) || $trangthai !== 'all') {
            $accounts = $this->adminModel->searchAccounts($hoten, $trangthai);
        } else {
            $accounts = $this->adminModel->getAllAccounts();
        }
        
        $functionTitle = "Hệ thống quản lý tài khoản";
        $contentView = __DIR__ . '/../views/admin/user_management.php';
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function getDetail($id = null) {
        if (!$id) {
            echo json_encode(['error' => 'Thiếu ID người dùng']);
            return;
        }

        $user = $this->adminModel->getAccountById($id);
        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(['error' => 'Không tìm thấy người dùng']);
        }
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $status = $_POST['status'];

            $result = $this->adminModel->updateStatus($id, $status);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật trạng thái vào cơ sở dữ liệu.']);
            }
        }
    }

    public function deleteAccount($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->adminModel->deleteAccount($id);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản. Vui lòng kiểm tra lại.']);
            }
        }
    }

    public function searchAccounts() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $hoten = isset($_GET['hoten']) ? trim($_GET['hoten']) : '';
            $trangthai = isset($_GET['trangthai']) ? $_GET['trangthai'] : 'all';
            
            $accounts = $this->adminModel->searchAccounts($hoten, $trangthai);
            
            echo json_encode([
                'success' => true,
                'data' => $accounts
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function manageProducts() {
        try {
            $products = $this->duyetSPModel->getPendingProducts();
            $active_page = 'product_management';
            $contentView = __DIR__ . '/../views/admin/product_approval.php';
            require_once __DIR__ . '/../views/admin/dashboard.php';
        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }

    public function getPendingProducts() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $products = $this->duyetSPModel->getPendingProducts();
            echo json_encode([
                'success' => true,
                'data' => $products
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getProductDetail() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!isset($_GET['id_sanpham'])) {
                throw new Exception("Thiếu ID sản phẩm");
            }

            $id_sanpham = intval($_GET['id_sanpham']);
            $product = $this->duyetSPModel->getProductDetail($id_sanpham);
            $images = $this->duyetSPModel->getProductImages($id_sanpham);
            $attributes = $this->duyetSPModel->getProductAttributes($id_sanpham);

            if (!$product) {
                throw new Exception("Sản phẩm không tồn tại");
            }

            echo json_encode([
                'success' => true,
                'product' => $product,
                'images' => $images,
                'attributes' => $attributes
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function approve() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!isset($_POST['id_sanpham'])) {
                throw new Exception("Thiếu ID sản phẩm");
            }

            $id_sanpham = intval($_POST['id_sanpham']);
            $this->duyetSPModel->approveProduct($id_sanpham);

            echo json_encode([
                'success' => true,
                'message' => 'Duyệt sản phẩm thành công!'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function approveAll() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $this->duyetSPModel->approveAllProducts();

            echo json_encode([
                'success' => true,
                'message' => 'Duyệt tất cả sản phẩm thành công!'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function reject() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!isset($_POST['id_sanpham'])) {
                throw new Exception("Thiếu ID sản phẩm");
            }

            $id_sanpham = intval($_POST['id_sanpham']);
            $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

            $this->duyetSPModel->rejectProduct($id_sanpham, $reason);

            echo json_encode([
                'success' => true,
                'message' => 'Từ chối sản phẩm thành công!'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function productStatistics() {
        try {
            // Xử lý bộ lọc mới với checkbox arrays
            $statusFilters = isset($_GET['status']) ? $_GET['status'] : [];
            $categoryFilters = isset($_GET['categories']) ? $_GET['categories'] : [];
            $month = isset($_GET['month']) ? intval($_GET['month']) : '';
            $year = isset($_GET['year']) ? intval($_GET['year']) : '';
            $seller = isset($_GET['seller']) ? trim($_GET['seller']) : '';

            // Lấy dữ liệu thống kê sản phẩm từ Model với filters mới
            $products = $this->adminModel->getProductStatisticsAdvanced($statusFilters, $categoryFilters, $month, $year, $seller);

            // Lấy danh sách danh mục để mapping
            $categories = $this->adminModel->getCategoriesMapping();

            // Lấy cây danh mục cho filter
            $categoryTree = $this->adminModel->getCategoryTree();

            $functionTitle = "Thống kê sản phẩm";
            $contentView = __DIR__ . '/../views/admin/product_statistics.php';
            require_once __DIR__ . '/../views/admin/dashboard.php';

            $functionTitle = "Thống kê sản phẩm";
            $contentView = __DIR__ . '/../views/admin/product_statistics.php';
            require_once __DIR__ . '/../views/admin/dashboard.php';
        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }


    public function stopSelling() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!isset($_POST['id_sanpham'])) {
                throw new Exception("Thiếu ID sản phẩm");
            }

            $id_sanpham = intval($_POST['id_sanpham']);
            $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

            // Sử dụng AdminModel để cập nhật trạng thái
            $result = $this->adminModel->stopSellingProduct($id_sanpham, $reason);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã dừng bán sản phẩm!'
                ]);
            } else {
                throw new Exception("Không thể cập nhật trạng thái sản phẩm");
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function approveProduct() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!isset($_POST['id_sanpham'])) {
                throw new Exception("Thiếu ID sản phẩm");
            }

            $id_sanpham = intval($_POST['id_sanpham']);

            // Sử dụng AdminModel để duyệt sản phẩm
            $result = $this->adminModel->approveProduct($id_sanpham);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã duyệt sản phẩm thành công!'
                ]);
            } else {
                throw new Exception("Không thể cập nhật trạng thái sản phẩm");
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function rejectProduct() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!isset($_POST['id_sanpham'])) {
                throw new Exception("Thiếu ID sản phẩm");
            }

            $id_sanpham = intval($_POST['id_sanpham']);
            $reason = isset($_POST['reason']) ? trim($_POST['reason']) : 'Từ chối bởi admin';

            // Sử dụng AdminModel để từ chối sản phẩm
            $result = $this->adminModel->rejectProduct($id_sanpham, $reason);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã từ chối sản phẩm!'
                ]);
            } else {
                throw new Exception("Không thể cập nhật trạng thái sản phẩm");
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportProductStatistics() {
        try {
            // Xử lý bộ lọc mới với checkbox arrays
            $statusFilters = isset($_GET['status']) ? $_GET['status'] : [];
            $categoryFilters = isset($_GET['categories']) ? $_GET['categories'] : [];
            $month = isset($_GET['month']) ? intval($_GET['month']) : '';
            $year = isset($_GET['year']) ? intval($_GET['year']) : '';
            $seller = isset($_GET['seller']) ? trim($_GET['seller']) : '';

            $products = $this->adminModel->getProductStatisticsAdvanced($statusFilters, $categoryFilters, $month, $year, $seller);

            // Set headers cho file Excel
            header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
            header('Content-Disposition: attachment; filename="thong_ke_san_pham_' . date('Y-m-d') . '.xls"');
            header('Cache-Control: max-age=0');

            // Tạo nội dung Excel
            echo "<html><head><meta charset='UTF-8'></head><body>";
            echo "<table border='1'>";
            echo "<tr><th>Mã SP</th><th>Tên sản phẩm</th><th>Giá</th><th>Danh mục</th><th>Người bán</th><th>Trạng thái</th><th>Ngày đăng</th><th>Thao tác</th></tr>";

            foreach ($products as $product) {
                // Convert trạng thái database thành text hiển thị
                $displayStatus = '';
                switch ($product['trangthai']) {
                    case 'Đã duyệt':
                        $displayStatus = 'Đang bán';
                        break;
                    case 'Chờ duyệt':
                        $displayStatus = 'Chưa duyệt';
                        break;
                    case 'Dừng bán':
                        $displayStatus = 'Dừng bán';
                        break;
                    case 'Đã bán':
                        $displayStatus = 'Đã bán';
                        break;
                    default:
                        $displayStatus = $product['trangthai'];
                }

                // Xử lý cột thao tác
                $actionText = '';
                switch ($product['trangthai']) {
                    case 'Đã duyệt':
                        $actionText = 'Có thể dừng bán';
                        break;
                    case 'Chờ duyệt':
                        $actionText = 'Chưa duyệt';
                        break;
                    case 'Dừng bán':
                        // Trích xuất lý do dừng bán
                        $reason = 'Đã dừng bán';
                        if (isset($product['mota']) && !empty($product['mota'])) {
                            if (preg_match('/\[Lý do dừng bán: ([^\]]+)\]/', $product['mota'], $matches)) {
                                $reason = trim($matches[1]);
                            }
                        }
                        $actionText = 'Đã dừng: ' . $reason;
                        break;
                    case 'Đã bán':
                        $actionText = 'Đã bán';
                        break;
                    default:
                        $actionText = 'N/A';
                }

                echo "<tr>";
                echo "<td>{$product['id_sanpham']}</td>";
                echo "<td>" . htmlspecialchars($product['ten_sanpham']) . "</td>";
                echo "<td>" . number_format($product['gia']) . "</td>";
                echo "<td>" . htmlspecialchars($product['ten_danhmuc'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($product['nguoi_ban']) . "</td>";
                echo "<td>" . htmlspecialchars($displayStatus) . "</td>";
                echo "<td>" . date('d/m/Y H:i', strtotime($product['ngaydang'])) . "</td>";
                echo "<td>" . htmlspecialchars($actionText) . "</td>";
                echo "</tr>";
            }

            echo "</table></body></html>";
            exit();

        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }
}
?>
