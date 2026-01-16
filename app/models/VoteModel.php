<?php
// app/models/VoteModel.php

class VoteModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy thông tin cơ bản của user để hiện lên popup
    public function getUserInfo($user_id) {
        $sql = "SELECT id_user, hoten, avatar FROM users WHERE id_user = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 🔥 KIỂM TRA QUAN HỆ: Hai người này có chung cuộc hội thoại nào không?
    public function checkIfChatted($user1, $user2) {
        $sqlFindConv = "
            SELECT c1.id_conversation 
            FROM conversation_users c1
            JOIN conversation_users c2 ON c1.id_conversation = c2.id_conversation
            WHERE c1.id_user = ? 
            AND c2.id_user = ?
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($sqlFindConv);
        $stmt->bind_param("ss", $user1, $user2);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $conversation_id = $row['id_conversation'];
            $sqlCheckMessages = "
                SELECT COUNT(DISTINCT sender_id) as num_senders
                FROM messages
                WHERE id_conversation = ?
                AND (sender_id = ? OR sender_id = ?)";
            $stmt2 = $this->conn->prepare($sqlCheckMessages);
            $stmt2->bind_param("iss", $conversation_id, $user1, $user2);
            $stmt2->execute();
            $res2 = $stmt2->get_result()->fetch_assoc();
            return ($res2['num_senders'] >= 2);
        }
        return false; // Không tìm thấy hội thoại chung
    }
    // =========================================================================
    // 🔥 [UPDATE] HÀM LƯU ĐÁNH GIÁ (Bao gồm Xác nhận giao dịch & Hình ảnh)
    // =========================================================================
    public function addReview($reviewer_id, $rated_user_id, $rating, $comment, $is_transacted, $files = null) {
        
        // 1. Insert vào bảng REVIEWS trước
        // Thêm cột 'is_transacted' vào câu lệnh
        $sql = "INSERT INTO reviews (user_id, seller_id, rating, comment, is_transacted, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        // s: string, s: string, i: int, s: string, i: int (is_transacted)
        $stmt->bind_param("ssisi", $reviewer_id, $rated_user_id, $rating, $comment, $is_transacted);
        
        if ($stmt->execute()) {
            // Lấy ID của review vừa tạo để dùng cho việc lưu ảnh
            $review_id = $stmt->insert_id;

            // 2. Xử lý lưu ảnh (Nếu có file gửi lên)
            if ($files && !empty($files['review_images']['name'][0])) {
$this->saveReviewImages($review_id, $files);
            }

            return true;
        }

        return false;
    }

    // Hàm phụ: Xử lý upload và lưu ảnh vào bảng review_images
    private function saveReviewImages($review_id, $files) {
        // Đường dẫn thư mục lưu ảnh (Bạn phải tạo thư mục này trước: public/uploads/reviews)
        $target_dir = "public/uploads/reviews/";
        
        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $sqlImg = "INSERT INTO review_images (id_review, image_path) VALUES (?, ?)";
        $stmtImg = $this->conn->prepare($sqlImg);

        $count_files = count($files['review_images']['name']);

        for ($i = 0; $i < $count_files; $i++) {
            // Kiểm tra lỗi upload
            if ($files['review_images']['error'][$i] === 0) {
                
                // Tạo tên file độc nhất để tránh trùng
                $file_extension = pathinfo($files['review_images']['name'][$i], PATHINFO_EXTENSION);
                $new_filename = time() . "_" . uniqid() . "." . $file_extension;
                $target_file = $target_dir . $new_filename;

                // Di chuyển file từ bộ nhớ tạm vào thư mục đích
                if (move_uploaded_file($files['review_images']['tmp_name'][$i], $target_file)) {
                    
                    // Lưu đường dẫn vào database (Lưu đường dẫn tương đối để dễ gọi view)
                    // Lưu: uploads/reviews/ten_file.jpg
                    $db_path = "uploads/reviews/" . $new_filename;
                    
                    $stmtImg->bind_param("is", $review_id, $db_path);
                    $stmtImg->execute();
                }
            }
        }
    }
}
?>