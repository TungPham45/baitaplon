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
    // --- 1. ĐÁNH DẤU ĐÃ BÁN ---
    public function markSold($id) {
        if (!isset($_SESSION['user_id'])) header("Location: /baitaplon/Auth/login");
        
        // Cần kiểm tra quyền sở hữu trước khi update (gọi model check)
        // Ở đây mình làm nhanh gọi updateStatus luôn
        $this->postModel->updateStatus($id, 'Đã bán');
        
        // Quay lại trang chi tiết
        header("Location: /baitaplon/Home/detail_Sanpham/$id");
        exit();
    }

    // --- 2. XÓA BÀI VIẾT ---
    public function delete($id) {
        if (!isset($_SESSION['user_id'])) header("Location: /baitaplon/Auth/login");

        // Gọi Model để xóa
        if ($this->postModel->deleteProduct($id)) {
            echo "<script>alert('Đã xóa bài viết!'); window.location.href='/baitaplon/Home';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa!'); window.history.back();</script>";
        }
    }

    // --- 3. XỬ LÝ FORM SỬA (Từ Modal) ---
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
<<<<<<< HEAD
            // Lấy trạng thái cũ
            $oldStatus = $this->getProductStatus($id);
            
=======
>>>>>>> 2562b16aebed4df7dc3b06293e5d7411944c9081
            // 1. Cập nhật thông tin cơ bản
            $catId = !empty($_POST['catLevel2']) ? $_POST['catLevel2'] : 
                     (!empty($_POST['catLevel1']) ? $_POST['catLevel1'] : null);

            $data = [
                'ten_sanpham' => $_POST['title'],
                'gia' => $_POST['price'],
                'mota' => $_POST['description'],
                'khu_vuc_ban' => $_POST['address']
            ];
            
            if ($catId) $data['id_danhmuc'] = $catId;

            // Bắt đầu Transaction
            $this->conn->begin_transaction();
            try {
                // Update bảng sanpham
                if (!$this->postModel->updateProduct($id, $data)) {
                    throw new Exception("Lỗi cập nhật thông tin sản phẩm");
                }

                // 2. Cập nhật thuộc tính (NẾU người dùng có gửi lên)
                // Lưu ý: Nếu user đổi danh mục, thuộc tính cũ sẽ vô nghĩa -> Xóa đi thêm lại là chuẩn nhất
                if (isset($_POST['thuoctinh']) && is_array($_POST['thuoctinh'])) {
                    
                    // Xóa hết thuộc tính cũ
                    mysqli_query($this->conn, "DELETE FROM gia_tri_thuoc_tinh WHERE id_sanpham = '$id'");

                    // Thêm thuộc tính mới
                    foreach ($_POST['thuoctinh'] as $id_thuoctinh => $val) {
                        
                        if (!empty($val)) {
                             // Gọi hàm insertAttributeValue trong PostModel
                             // Lưu ý: Hàm này cần insert id_option
                             $this->postModel->insertAttributeValue($id, $id_thuoctinh, $val);
                        }
                    }
                }

                $this->conn->commit();
<<<<<<< HEAD
                
                // Nếu sản phẩm bị từ chối, sau khi update set thành Chờ duyệt
                if ($oldStatus == 'Từ chối') {
                    $this->postModel->updateStatus($id, 'Chờ duyệt');
                }
                
=======
>>>>>>> 2562b16aebed4df7dc3b06293e5d7411944c9081
                echo "<script>alert('Cập nhật thành công!'); window.location.href='/baitaplon/Home/detail_Sanpham/$id';</script>";

            } catch (Exception $e) {
                $this->conn->rollback();
                echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.history.back();</script>";
            }
        }
    }
    
<<<<<<< HEAD
    private function getProductStatus($id) {
        $sql = "SELECT trangthai FROM sanpham WHERE id_sanpham = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['trangthai'] : null;
    }
    
=======
>>>>>>> 2562b16aebed4df7dc3b06293e5d7411944c9081
}
?>