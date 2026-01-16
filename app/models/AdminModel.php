<?php
class AdminModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllAccounts() {
        $sql = "SELECT tk.id_user, tk.username, tk.email, tk.role, tk.trangthai, tk.ngaytao,
                       nd.hoten, nd.sdt, nd.diachi 
                FROM account tk 
                JOIN users nd ON CAST(tk.id_user AS CHAR) = CAST(nd.id_user AS CHAR)";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchAccounts($hoten = '', $trangthai = '') {
        $conditions = [];
        $params = [];
        $types = '';

        if (!empty($hoten)) {
            $conditions[] = "nd.hoten LIKE ?";
            $params[] = "%$hoten%";
            $types .= 's';
        }

        if (!empty($trangthai) && $trangthai !== 'all') {
            $conditions[] = "tk.trangthai = ?";
            $params[] = $trangthai;
            $types .= 's';
        }

        $sql = "SELECT tk.id_user, tk.username, tk.email, tk.role, tk.trangthai, tk.ngaytao,
                       nd.hoten, nd.sdt, nd.diachi 
                FROM account tk 
                JOIN users nd ON CAST(tk.id_user AS CHAR) = CAST(nd.id_user AS CHAR)";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY tk.id_user ASC";

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $result = $this->db->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    public function getAccountById($id) {
        $sql = "SELECT tk.id_user, nd.id_user as nd_id_user, tk.username, tk.email, tk.role, tk.trangthai, tk.ngaytao, 
                       nd.hoten, nd.sdt, nd.diachi, nd.avatar, nd.gioithieu, nd.danhgia 
                FROM account tk 
                JOIN users nd ON tk.id_user = nd.id_user 
                WHERE tk.id_user = CAST(? AS CHAR)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result;
    }

    public function updateStatus($id, $status) {
        try {
            // Bắt đầu transaction để đảm bảo an toàn dữ liệu
            $this->db->begin_transaction();

            // 1. Cập nhật trạng thái tài khoản
            $sql = "UPDATE account SET trangthai = ? WHERE id_user = CAST(? AS CHAR)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ss", $status, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi cập nhật trạng thái tài khoản");
            }

            // 2. Logic xử lý phụ: Nếu khóa tài khoản -> Dừng bán các sản phẩm đang duyệt
            // Kiểm tra các từ khóa có thể dùng cho việc khóa (tùy thuộc vào value bên view gửi về)
            if ($status === 'Bị khóa' || $status === 'Khóa') {
                
                // Cập nhật trạng thái sản phẩm:
                // Chỉ cập nhật những sản phẩm đang 'Đã duyệt' -> 'Dừng bán'
                // Đồng thời ghi chú vào phần mô tả lý do dừng bán
                $sqlProduct = "UPDATE sanpham 
                               SET trangthai = N'Dừng bán', 
                                   mota = CONCAT(IFNULL(mota, ''), '\n\n[Hệ thống: Dừng bán tự động do tài khoản người bán bị khóa]') 
                               WHERE id_user = CAST(? AS CHAR) 
                               AND trangthai = N'Đã duyệt'";
                
                $stmtProduct = $this->db->prepare($sqlProduct);
                $stmtProduct->bind_param("s", $id);
                
                if (!$stmtProduct->execute()) {
                    throw new Exception("Lỗi khi cập nhật trạng thái sản phẩm");
                }
            }

            // Nếu mọi thứ ổn, xác nhận thay đổi
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Nếu có lỗi, hoàn tác mọi thay đổi
            $this->db->rollback();
            // Bạn có thể log lỗi ra file nếu cần: error_log($e->getMessage());
            return false;
        }
    }

    public function deleteAccount($id) {
        try {
            $this->db->begin_transaction();

            $stmt1 = $this->db->prepare("DELETE FROM users WHERE id_user = CAST(? AS CHAR)");
            $stmt1->bind_param("s", $id);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $this->db->prepare("DELETE FROM account WHERE id_user = CAST(? AS CHAR)");
            $stmt2->bind_param("s", $id);
            $stmt2->execute();
            $stmt2->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    // ==================== PRODUCT STATISTICS METHODS ====================

    public function getProductStatistics($status = 'all', $month = '', $year = '', $seller = '') {
        $where = [];

        // Lọc theo trạng thái
        if ($status !== 'all' && $status !== '') {
            $where[] = "sp.trangthai = N'$status'";
        }

        // Lọc theo tháng/năm
        if ($month && $year) {
            $where[] = "MONTH(sp.ngaydang) = $month AND YEAR(sp.ngaydang) = $year";
        } elseif ($year) {
            $where[] = "YEAR(sp.ngaydang) = $year";
        }

        // Lọc theo người bán
        if ($seller) {
            $where[] = "u.hoten LIKE '%$seller%'";
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "SELECT sp.id_sanpham, sp.ten_sanpham, sp.gia, sp.trangthai, sp.ngaydang, sp.mota,
                       u.hoten as nguoi_ban, u.id_user,
                       dm.ten_danhmuc, dm.id_parent,
                       COALESCE(MIN(spa.url_anh), sp.avatar) AS anh_hienthi
                FROM sanpham sp
                LEFT JOIN users u ON sp.id_user = u.id_user
                LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
                LEFT JOIN sanpham_anh spa ON sp.id_sanpham = spa.id_sanpham
                $whereClause
                GROUP BY sp.id_sanpham
                ORDER BY sp.ngaydang DESC";

        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductStatisticsAdvanced($statusFilters = [], $categoryFilters = [], $month = '', $year = '', $seller = '', $keyword = '') {
        
        if (!$this->db) { die("Lỗi kết nối"); }

        $where = " WHERE 1=1 "; 

        // 1. Keyword (Tên sản phẩm)
        if (!empty($keyword)) {
            $keyword = mysqli_real_escape_string($this->db, $keyword);
            $where .= " AND sp.ten_sanpham LIKE '%$keyword%' ";
        }

        // 2. Trạng thái
        if (!empty($statusFilters) && !in_array('all', $statusFilters)) {
            $safeStatus = [];
            foreach ($statusFilters as $s) {
                $safeStatus[] = mysqli_real_escape_string($this->db, $s);
            }
            $statusStr = implode("','", $safeStatus);
            $where .= " AND sp.trangthai IN ('$statusStr') ";
        }

        // 3. Danh mục
        if (!empty($categoryFilters)) {
            $catIds = implode(',', array_map('intval', $categoryFilters));
            $where .= " AND (sp.id_danhmuc IN ($catIds) OR dm.id_parent IN ($catIds)) ";
        }

        // 4. Thời gian
        if (!empty($month)) {
            $month = intval($month);
            $where .= " AND MONTH(sp.ngaydang) = $month ";
        }
        if (!empty($year)) {
            $year = intval($year);
            $where .= " AND YEAR(sp.ngaydang) = $year ";
        }

        // 5. Người bán (Sửa: Tìm theo username vì không có hoten)
        if (!empty($seller)) {
            $seller = mysqli_real_escape_string($this->conn, $seller);
            $where .= " AND acc.username LIKE '%$seller%' ";
        }

        // Sửa query: acc.username AS nguoi_ban
        $sql = "SELECT sp.id_sanpham, sp.ten_sanpham, sp.gia, sp.mota, sp.avatar, sp.khu_vuc_ban, sp.ngaydang, sp.trangthai, sp.id_user,
                dm.ten_danhmuc, dm.id_parent, acc.role, acc.username AS nguoi_ban,
                COALESCE(MIN(spa.url_anh), sp.avatar) AS anh_hienthi
                FROM sanpham sp
                LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
                LEFT JOIN account acc ON sp.id_user = acc.id_user
                LEFT JOIN sanpham_anh spa ON sp.id_sanpham = spa.id_sanpham
                $where
                GROUP BY sp.id_sanpham
                ORDER BY sp.ngaydang DESC";

        $result = mysqli_query($this->db, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getCategoriesMapping() {
        $sql = "SELECT id_danhmuc, ten_danhmuc FROM danhmuc";
        $result = $this->db->query($sql);

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[$row['id_danhmuc']] = $row['ten_danhmuc'];
        }

        return $categories;
    }

    public function getCategoryTree() {
        $sql = "SELECT id_danhmuc, ten_danhmuc, id_parent FROM danhmuc ORDER BY id_parent ASC, ten_danhmuc ASC";
        $result = $this->db->query($sql);

        $categories = [];
        $parents = [];

        while ($row = $result->fetch_assoc()) {
            if ($row['id_parent'] == null || $row['id_parent'] == 0) {
                // Parent category
                $parents[$row['id_danhmuc']] = [
                    'id' => $row['id_danhmuc'],
                    'name' => $row['ten_danhmuc'],
                    'children' => []
                ];
            } else {
                // Child category - will be added to parent later
                $categories[] = $row;
            }
        }

        // Add children to parents
        foreach ($categories as $category) {
            if (isset($parents[$category['id_parent']])) {
                $parents[$category['id_parent']]['children'][] = [
                    'id' => $category['id_danhmuc'],
                    'name' => $category['ten_danhmuc']
                ];
            }
        }

        return array_values($parents);
    }

    public function stopSellingProduct($id_sanpham, $reason) {
            $sql = "UPDATE sanpham SET trangthai = N'Dừng bán', mota = ? WHERE id_sanpham = ?";
            $stmt = $this->db->prepare($sql);
            $newMota = "[Lý do dừng bán: " . $reason . "]";
            $stmt->bind_param('si', $newMota, $id_sanpham);
            return $stmt->execute();
        }

    public function approveProduct($id_sanpham) {
        $sql = "UPDATE sanpham SET trangthai = N'Đã duyệt' WHERE id_sanpham = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id_sanpham);
        return $stmt->execute();
    }

    public function rejectProduct($id_sanpham, $reason) {
        $sql = "UPDATE sanpham SET trangthai = N'Chưa duyệt' WHERE id_sanpham = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id_sanpham);
        return $stmt->execute();
    }
}
?>
