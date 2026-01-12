<?php
// Đảm bảo load Model trước
require_once __DIR__ . '/../models/PostModel.php';

class PostController {
        private $postModel;
        private $conn; // Cần biến này để quản lý Transaction
        private $uploadDir;
        private $dbPublicPath;

        public function __construct($conn) {
            $this->conn = $conn;
            $this->postModel = new PostModel($conn);
            
            // 1. Đường dẫn vật lý trên Server (Dùng để move_uploaded_file)
            // __DIR__ là app/controllers -> ra ngoài 2 cấp là root -> vào public/images
            $this->uploadDir = __DIR__ . '/../../public/images/';
            
            // 2. Đường dẫn lưu vào Database (Dùng để hiển thị thẻ <img src="...">)
            // Giả sử thư mục gốc web trỏ vào folder chứa index.php
            $this->dbPublicPath = 'public/images/'; 
        }

        public function index($id_user = 0) {
            // Truyền biến $id_user_url sang view để dùng
            $id_user_url = $id_user;
            
            // Cập nhật đường dẫn mới (đã chuyển vào thư mục Page)
            require_once __DIR__ . '/../views/Page/View_ThemSP.php';
        }

        public function add() {
        // 1. Không dùng ob_clean() nếu chưa start buffer để tránh Notice hỏng JSON
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $this->conn->begin_transaction();

            // 2. Lấy danh mục linh hoạt: Ưu tiên cấp 2, nếu không có thì lấy cấp 1
            $id_danhmuc = !empty($_POST['catLevel2']) ? intval($_POST['catLevel2']) : intval($_POST['catLevel1']);
            
            if (empty($_POST['title']) || empty($_POST['price']) || $id_danhmuc <= 0) {
                throw new Exception("Vui lòng nhập đầy đủ: Tiêu đề, Giá, Danh mục");
            }

            // 3. Lấy ID user (giữ nguyên chuỗi 'US002' vì DB đã là VARCHAR)
            $id_user = $_POST['id_user_posted'] ?? $_SESSION['user_id'] ?? '1';

            $ten_sanpham = trim($_POST['title']);
            $gia = floatval($_POST['price']);
            $mota = trim($_POST['description'] ?? '');
            $address = trim($_POST['address'] ?? '');

            // Xử lý Upload ảnh
            $uploadedImages = [];
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['name'] as $key => $val) {
                    $fileName = $this->uploadImage($_FILES['images'], $key);
                    if ($fileName) {
                        $uploadedImages[] = $this->dbPublicPath . $fileName;
                    }
                }
            }
            $avatar = count($uploadedImages) > 0 ? $uploadedImages[0] : 'public/images/default.jpg';

            // 4. Gọi Model để Insert
            $id_sanpham = $this->postModel->insertProduct($ten_sanpham, $id_danhmuc, $id_user, $gia, $mota, $avatar, $address);

            if (!$id_sanpham) {
                // Chắc chắn lấy được lỗi từ MySQLi
                $dbError = mysqli_error($this->conn);
                $dbErrno = mysqli_errno($this->conn);
                throw new Exception("Lỗi Database [" . $dbErrno . "]: " . $dbError);
            }

            // Insert Album ảnh phụ
            foreach ($uploadedImages as $imgUrl) {
                $this->postModel->insertProductImage($id_sanpham, $imgUrl);
            }

            // 5. Lưu thuộc tính sản phẩm
            if (isset($_POST['thuoctinh']) && is_array($_POST['thuoctinh'])) {
                foreach ($_POST['thuoctinh'] as $id_thuoctinh => $id_option) {
                    if (!empty($id_option)) {
                        if (!$this->postModel->insertAttributeValue($id_sanpham, $id_thuoctinh, $id_option)) {
                            throw new Exception("Lỗi khi lưu thuộc tính sản phẩm");
                        }
                    }
                }
            }

            $this->conn->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Đăng tin thành công!'
            ]);

        } catch (Exception $e) {
            if ($this->conn) $this->conn->rollback();
            http_response_code(400); //
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Hàm chỉ trả về Tên file, việc ghép đường dẫn để controller lo
    private function uploadImage($files, $index) {
        // Kiểm tra lỗi upload từ PHP
        if ($files['error'][$index] !== UPLOAD_ERR_OK) {
            // Nếu không có file (lỗi số 4) thì bỏ qua, lỗi khác thì báo
            if ($files['error'][$index] == UPLOAD_ERR_NO_FILE) return null;
            throw new Exception("Lỗi upload file (Mã lỗi: " . $files['error'][$index] . ")");
        }

        $tmp_file = $files['tmp_name'][$index];
        $filename = basename($files['name'][$index]);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Validate file type
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed)) {
            throw new Exception("File '$filename' không đúng định dạng ảnh.");
        }

        // Validate file size (ví dụ max 5MB)
        if ($files['size'][$index] > 5 * 1024 * 1024) {
             throw new Exception("File '$filename' quá lớn (Max 5MB).");
        }

        // Tạo tên file unique
        $newFilename = 'sp_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $this->uploadDir . $newFilename;

        // Tạo thư mục nếu chưa có
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0777, true)) {
                throw new Exception("Không thể tạo thư mục upload.");
            }
        }

        if (!move_uploaded_file($tmp_file, $destPath)) {
            throw new Exception("Không thể di chuyển file tới: " . $destPath);
        }

        return $newFilename; // Chỉ trả về tên file
    }
}
?>