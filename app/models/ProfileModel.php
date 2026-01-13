<?php
class ProfileModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getProfile($userId) {
        $sql = "SELECT a.username, a.email, a.role, a.trangthai, a.ngaytao, 
                    u.hoten, u.sdt, u.diachi, u.avatar, u.gioithieu, u.danhgia 
                FROM account a 
                JOIN users u ON a.id_user = u.id_user 
                WHERE a.id_user = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateProfile($userId, $data) {
        $sql = "UPDATE users SET 
                hoten = ?, 
                sdt = ?, 
                diachi = ?, 
                avatar = ?, 
                gioithieu = ? 
                WHERE id_user = ?";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssss", 
            $data['name'],
            $data['phone'],
            $data['address'],
            $data['avatar'],
            $data['bio'],
            $userId
        );
        return $stmt->execute();
    }
    //Hải thêm
    // ... các hàm khác ...

      public function getReviewsByUserId($profileId)
        {
            // 1. Lấy dữ liệu thô từ Database (Giữ nguyên SQL chuẩn)
            $sql = "SELECT 
                        r.*, 
                        u.hoten AS reviewer_name, 
                        u.avatar AS reviewer_avatar,
                        ri.image_path AS images 
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id_user 
                    LEFT JOIN review_images ri ON r.id_review = ri.review_id 
                    WHERE r.seller_id = ? 
                    ORDER BY r.created_at DESC";

            $stmt = $this->db->prepare($sql);
            if ($stmt === false) {
                die('Lỗi SQL: ' . $this->db->error);
            }
            
            $stmt->bind_param("s", $profileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $raw_data = $result->fetch_all(MYSQLI_ASSOC);

            // 2. XỬ LÝ GỘP DÒNG (Group by Review ID)
            // Mục đích: Gom các dòng trùng review lại, gộp ảnh vào một mảng
            $reviews = [];

            foreach ($raw_data as $row) {
                $r_id = $row['id_review'];

                // Nếu review này chưa có trong danh sách kết quả thì tạo mới
                if (!isset($reviews[$r_id])) {
                    $reviews[$r_id] = $row;
                    $reviews[$r_id]['images'] = []; // Khởi tạo mảng ảnh rỗng
                }

                // Nếu dòng dữ liệu này có ảnh, thêm đường dẫn ảnh vào mảng 'images'
                if (!empty($row['images'])) {
                    // Kiểm tra để tránh trùng ảnh (nếu cần)
                    if (!in_array($row['images'], $reviews[$r_id]['images'])) {
                        $reviews[$r_id]['images'][] = $row['images'];
                    }
                }
            }

            // 3. Trả về mảng tuần tự (để View dễ foreach)
            return array_values($reviews);
        }
}
?>
