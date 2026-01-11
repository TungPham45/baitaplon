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
    public function getReviewsByUserId($userId) {
            // Query này lấy review + gộp nhiều ảnh thành 1 chuỗi cách nhau dấu phẩy
            $sql = "
                SELECT 
                    r.*,
                    u.hoten AS reviewer_name,
                    u.avatar AS reviewer_avatar,
                    GROUP_CONCAT(ri.image_path SEPARATOR ',') as list_images
                FROM reviews r
                JOIN users u ON r.user_id = u.id_user
                LEFT JOIN review_images ri ON r.id_review = ri.review_id
                WHERE r.seller_id = ? 
                GROUP BY r.id_review
                ORDER BY r.created_at DESC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $reviews = [];

            while ($row = $result->fetch_assoc()) {
                // Xử lý tách chuỗi ảnh thành mảng để View dễ dùng
                if (!empty($row['list_images'])) {
                    $row['images'] = explode(',', $row['list_images']);
                } else {
                    $row['images'] = [];
                }
                $reviews[] = $row;
            }

            return $reviews;
        }
}
?>
