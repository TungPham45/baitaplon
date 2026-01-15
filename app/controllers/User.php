<?php
class User
{
    protected $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    
    /**
     * Hàm load Model
     */
    protected function model($modelName)
    {
        $modelFile = __DIR__ . '/../models/' . $modelName . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            $model = new $modelName($this->conn);
            return $model;
        } else {
            die("Model $modelName không tồn tại!");
        }
    }
    
    /**
     * Hàm load View
     */
    protected function view($viewName, $data = [])
    {
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            // Giải nén mảng data thành các biến riêng biệt ($profile, $products...)
            extract($data);
            require_once $viewFile;
        } else {
            die("View $viewName không tồn tại!");
        }
    }
    
    // =================================================================
    // HIỂN THỊ TRANG PROFILE
    // URL: /User/Profile/US001/US002 (Xem profile US001 với tư cách US002)
    // =================================================================
    public function Profile($profileId, $loggedInId = '')
    {
        // 1. Load các Model cần thiết
        $userModel = $this->model('UserModel'); 
        $sanphamModel = $this->model('SanphamModel');
        
        // 🔥 Load ProfileModel để lấy đánh giá (Quan trọng)
        $profileModel = $this->model('ProfileModel'); 

        // 2. Xử lý ID người xem (loggedInId)
        // Nếu không truyền ID người xem, lấy từ session
        if (empty($loggedInId)) {
            if (isset($_SESSION['user_id'])) {
                $loggedInId = $_SESSION['user_id'];
            } else {
                // Nếu chưa đăng nhập thì coi như khách vãng lai
                $loggedInId = ''; 
            }
        }

        // 3. Lấy thông tin người được xem (Chủ Profile)
        $userProfile = $userModel->getUserById($profileId);

        // 6. Kiểm tra quyền sở hữu (Để hiện nút "Sửa trang cá nhân")
        $isOwner = (!empty($loggedInId) && $loggedInId === $profileId);

        // 4. Lấy danh sách sản phẩm của người đó
        // (Tham số thứ 6 là $profileId để lọc sản phẩm của user này)
        $trang_thai_filter = '';
        if (isset($_GET['trang_thai'])) {
            $trang_thai_filter = $_GET['trang_thai'];
        } elseif ($isOwner) {
            // Nếu là chủ tài khoản và không có GET parameter, hiển thị tất cả sản phẩm
            $trang_thai_filter = 'all';
        }
        $products = $sanphamModel->getProducts('', '', '', 0, 100, $profileId, $trang_thai_filter);

        // 5. 🔥 [MỚI] Lấy danh sách ĐÁNH GIÁ từ ProfileModel
        $reviews = $profileModel->getReviewsByUserId($profileId);

        // 6.5. Thống kê sản phẩm theo trạng thái
        $soldCount = $sanphamModel->countProducts('', '', '', $profileId, 'Đã bán');
        $approvedCount = $sanphamModel->countProducts('', '', '', $profileId, 'Đã duyệt');
        $totalActiveProducts = $soldCount + $approvedCount;

        // 7. Đóng gói dữ liệu gửi sang View
        $data = [
            'page'        => 'profile', // Để Navbar biết đang ở trang nào
            'profile'     => $userProfile,
            'products'    => $products,
            'reviews'     => $reviews, // <-- Truyền biến này sang View
            'isOwner'     => $isOwner,
            'user_id'     => $loggedInId,
            'isLoggedIn'  => !empty($loggedInId),
            'default_status' => $trang_thai_filter, // Truyền trạng thái mặc định để view hiển thị đúng
            'soldCount' => $soldCount, // Số sản phẩm đã bán
            'approvedCount' => $approvedCount, // Số sản phẩm đã duyệt
            'totalActiveProducts' => $totalActiveProducts // Tổng sản phẩm hoạt động
        ];

        // Load view home (View này sẽ include file profile.php)
        $this->view('home', $data);
    }

    // =================================================================
    // XỬ LÝ CẬP NHẬT THÔNG TIN (POST)
    // =================================================================
    public function Update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = $_POST['id_user'];
            $hoten = $_POST['hoten'];
            $sdt = $_POST['sdt'];
            $diachi = $_POST['diachi'];
            $gioithieu = $_POST['gioithieu'];
            
            // 1. Lấy thông tin cũ để giữ lại avatar nếu người dùng không up ảnh mới
            $userModel = $this->model('UserModel');
            $currentUser = $userModel->getUserById($id_user);
            $avatarUrl = $currentUser['avatar']; 

            // 2. Xử lý upload ảnh Avatar
            if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] == 0) {
                // Đường dẫn thư mục lưu ảnh
                $target_dir = __DIR__ . "/../../public/uploads/avatars/";
                
                // Tạo thư mục nếu chưa có
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                
                // Tạo tên file mới
                $fileName = time() . "_" . basename($_FILES["avatar_file"]["name"]);
                $target_file = $target_dir . $fileName;
                
                // Di chuyển file
                if (move_uploaded_file($_FILES["avatar_file"]["tmp_name"], $target_file)) {
                    // Lưu đường dẫn tương đối vào DB (public/...)
                    $avatarUrl = "public/uploads/avatars/" . $fileName; 
                }
            }

            // 3. Gọi Model cập nhật Database
            $userModel->updateUser($id_user, $hoten, $sdt, $diachi, $gioithieu, $avatarUrl);

            // 4. Chuyển hướng về lại trang Profile
            $redirectUrl = "/baitaplon/User/Profile/" . urlencode($id_user);
            
            // Nếu đang đăng nhập thì nối thêm ID người xem vào URL để giữ session
            if (isset($_SESSION['user_id'])) {
                $redirectUrl .= "/" . urlencode($_SESSION['user_id']);
            }
            
            header("Location: " . $redirectUrl);
            exit();
        }
    }
}
?>