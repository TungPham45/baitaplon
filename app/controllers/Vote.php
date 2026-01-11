<?php
// app/controllers/Vote.php

require_once __DIR__ . '/../models/VoteModel.php';

class Vote {
    private $voteModel;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->voteModel = new VoteModel($conn);
    }

    // ==================================================
    // 1. HIỆN POPUP ĐÁNH GIÁ NGƯỜI DÙNG
    // URL: /Vote/dialog/{partner_id}
    // ==================================================
    public function dialog($partner_id) {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            echo "Lỗi: Bạn chưa đăng nhập.";
            return;
        }

        $my_id = $_SESSION['user_id'];

        // 2. Không cho tự đánh giá mình
        if ($partner_id === $my_id) {
            echo "Lỗi: Không thể tự đánh giá bản thân.";
            return;
        }
        
        // 3. Lấy thông tin người bị đánh giá
        $partnerInfo = $this->voteModel->getUserInfo($partner_id);

        if (!$partnerInfo) {
            echo "Lỗi: Người dùng không tồn tại.";
            return;
        }

        // 4. Chuẩn bị dữ liệu truyền sang View
        $target_id    = $partnerInfo['id_user']; 
        $target_name  = $partnerInfo['hoten'];
        
        // Gọi View dialog
        require __DIR__ . '/../views/Vote/dialog.php';
    }

    // ==================================================
    // 2. XỬ LÝ SUBMIT (CẬP NHẬT MỚI)
    // ==================================================
    public function submit() {
        // Đặt header JSON để JS nhận diện đúng
        header('Content-Type: application/json');

        // 1. Check Login
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
            return;
        }

        $reviewer_id = $_SESSION['user_id']; // Người đánh giá (Tôi)
        
        // 2. Lấy dữ liệu từ FormData gửi lên
        $rated_user_id = $_POST['target_id'] ?? ''; 
        $rating        = (int)($_POST['rating'] ?? 0);
        $comment       = trim($_POST['comment'] ?? '');
        
        // [MỚI] Lấy trạng thái "Đã giao dịch" (0 hoặc 1)
        $is_transacted = isset($_POST['is_transacted']) ? (int)$_POST['is_transacted'] : 0;

        // 3. Validate cơ bản
        if (empty($rated_user_id) || $rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ (Số sao phải từ 1-5).']);
            return;
        }

       // 4. 🛑 BẢO MẬT: Kiểm tra lịch sử chat
        $hasChatted = $this->voteModel->checkIfChatted($reviewer_id, $rated_user_id);

        if (!$hasChatted) {
            echo json_encode(['success' => false, 'message' => 'Bạn cần trao đổi/nhắn tin với người này trước khi đánh giá.']);
            return;
        }

        // 5. Gọi Model để lưu (Truyền thêm $is_transacted và $_FILES)
        // $_FILES chứa các file ảnh được gửi lên từ form
        $result = $this->voteModel->addReview(
            $reviewer_id, 
            $rated_user_id, 
            $rating, 
            $comment, 
            $is_transacted, 
            $_FILES // [MỚI] Truyền file sang Model
        );

        // 6. Trả kết quả về cho JS
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Đánh giá thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi lưu đánh giá.']);
        }
    }
}
?>