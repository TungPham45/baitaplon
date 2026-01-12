<?php
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/ProfileModel.php';
require_once __DIR__ . '/../models/DuyetSPModel.php';

class Admin {
    private $adminModel;
    private $profileModel;
    private $duyetSPModel;

    public function __construct($conn) {
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $isApiRequest = strpos($url, 'getDetail') !== false || 
                        strpos($url, 'updateStatus') !== false ||
                        strpos($url, 'deleteAccount') !== false ||
                        strpos($url, 'getPendingProducts') !== false ||
                        strpos($url, 'getProductDetail') !== false ||
                        strpos($url, 'approve') !== false ||
                        strpos($url, 'reject') !== false ||
                        strpos($url, 'searchAccounts') !== false;

        if (!$isApiRequest) {
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Quản lý') {
                header("Location: /baitaplon/Auth/login");
                exit();
            }
        }
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
}
?>
