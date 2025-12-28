<?php
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/ProfileModel.php';

class Admin {
    private $adminModel;
    private $profileModel;

    public function __construct($conn) {
        // 1. Kiểm tra quyền truy cập: Chỉ cho phép 'Quản lý'
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Quản lý') {
            header("Location: /baitaplon/Auth/login");
            exit();
        }
        $this->adminModel = new AdminModel($conn);
        $this->profileModel = new ProfileModel($conn);
    }

    /**
     * Xem hồ sơ cá nhân của Admin
     */
    public function profile() {
        $userId = $_SESSION['user_id'];
        $user = $this->profileModel->getProfile($userId); 

        $functionTitle = "Hồ sơ Quản trị viên";
        // Cập nhật đường dẫn View theo cấu trúc folder views mới
        $contentView = __DIR__ . '/../views/admin/profile/show.php'; 
        require_once __DIR__ . '/../views/admin/dashboard.php'; 
    }

    public function editProfile() {
        $userId = $_SESSION['user_id'];
        $user = $this->profileModel->getProfile($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $avatarName = $user['avatar']; 

            // Xử lý Upload ảnh
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName = $_FILES['avatar']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
                    // Cập nhật đường dẫn upload vào folder public mới
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
                // Điều hướng về Admin/profile theo luồng Class Admin
                header("Location: /baitaplon/Admin/profile");
                exit();
            }
        }
        
        $functionTitle = "Chỉnh sửa hồ sơ";
        $contentView = __DIR__ . '/../views/admin/profile/edit.php'; 
        require_once __DIR__ . '/../views/admin/dashboard.php'; 
    }
    /**
     * Trang chủ quản trị (Dashboard)
     */
    public function dashboard() {
        $active_page = 'user_management';
        $accounts = $this->adminModel->getAllAccounts(); 
        $functionTitle = "Hệ thống quản lý tài khoản";
        $contentView = __DIR__ . '/../views/admin/user_management.php';
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    /**
     * AJAX: Lấy thông tin chi tiết tài khoản để hiển thị Modal
     */
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

    /**
     * AJAX: Cập nhật trạng thái (Phê duyệt, Khóa, Mở lại)
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $status = $_POST['status'];

            // Gọi model cập nhật cột 'trangthai' trong bảng 'taikhoan'
            $result = $this->adminModel->updateStatus($id, $status);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật trạng thái vào cơ sở dữ liệu.']);
            }
        }
    }

    /**
     * AJAX: Xóa tài khoản vĩnh viễn
     */
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
}