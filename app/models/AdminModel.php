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
        $sql = "UPDATE account SET trangthai = ? WHERE id_user = CAST(? AS CHAR)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $status, $id);
        return $stmt->execute();
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

    public function getProductStatisticsAdvanced($statusFilters = [], $categoryFilters = [], $month = '', $year = '', $seller = '') {
        $where = [];

        // Lọc theo trạng thái (multiple selection)
        if (!empty($statusFilters) && !in_array('all', $statusFilters)) {
            $statusPlaceholders = str_repeat('?,', count($statusFilters) - 1) . '?';
            $where[] = "sp.trangthai IN ($statusPlaceholders)";
        }

        // Lọc theo danh mục (multiple selection)
        if (!empty($categoryFilters)) {
            $categoryPlaceholders = str_repeat('?,', count($categoryFilters) - 1) . '?';
            $where[] = "sp.id_danhmuc IN ($categoryPlaceholders)";
        }

        // Lọc theo tháng/năm
        if ($month && $year) {
            $where[] = "MONTH(sp.ngaydang) = ? AND YEAR(sp.ngaydang) = ?";
        } elseif ($year) {
            $where[] = "YEAR(sp.ngaydang) = ?";
        }

        // Lọc theo người bán
        if ($seller) {
            $where[] = "u.hoten LIKE ?";
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

        $stmt = $this->db->prepare($sql);

        // Bind parameters
        $paramTypes = '';
        $paramValues = [];

        // Status filters
        if (!empty($statusFilters) && !in_array('all', $statusFilters)) {
            foreach ($statusFilters as $status) {
                $paramTypes .= 's';
                $paramValues[] = $status;
            }
        }

        // Category filters
        if (!empty($categoryFilters)) {
            foreach ($categoryFilters as $category) {
                $paramTypes .= 'i';
                $paramValues[] = (int)$category;
            }
        }

        // Month/Year filters
        if ($month && $year) {
            $paramTypes .= 'ii';
            $paramValues[] = $month;
            $paramValues[] = $year;
        } elseif ($year) {
            $paramTypes .= 'i';
            $paramValues[] = $year;
        }

        // Seller filter
        if ($seller) {
            $paramTypes .= 's';
            $paramValues[] = "%$seller%";
        }

        if (!empty($paramValues)) {
            $stmt->bind_param($paramTypes, ...$paramValues);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
