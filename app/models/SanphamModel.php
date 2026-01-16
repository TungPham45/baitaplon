<?php
class SanphamModel
{
    private $con;

    // Nhận biến kết nối từ Controller truyền sang
    public function __construct($conn)
    {
        $this->con = $conn;
    }

    public function getAllCategories()
    {
        $sql = "SELECT id_danhmuc, ten_danhmuc FROM danhmuc ORDER BY ten_danhmuc ASC";
        $result = mysqli_query($this->con, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }
    public function getProducts($keyword = '', $category = '', $address = '', $offset = 0, $limit = 12, $userId = '', $status = '')
    {
        $keyword = mysqli_real_escape_string($this->con, trim($keyword));
        $category = mysqli_real_escape_string($this->con, trim($category));
        $address = mysqli_real_escape_string($this->con, trim($address));
        $userId = mysqli_real_escape_string($this->con, trim($userId));
        
        // Xử lý status để tránh lỗi SQL injection
        $status = mysqli_real_escape_string($this->con, trim($status));

        // --- SỬA ĐỔI Ở ĐÂY ---
        $where = " WHERE 1"; 

        // 1. Nếu chọn "all", ta KHÔNG thêm điều kiện lọc trạng thái (để hiện tất cả)
            if ($status === 'all') {
                // Không làm gì cả, để nó hiện hết
            }
            // 2. Nếu có chọn status cụ thể (và khác 'all')
            elseif ($status !== '') {
                // Thêm chữ N phía trước '$status' để hỗ trợ tiếng Việt tốt nhất
                $where .= " AND sp.trangthai = N'$status'";
            } 
            // 3. Mặc định (khi mới vào trang, status rỗng) -> Chỉ hiện 'Đã duyệt'
            else {
                $where .= " AND sp.trangthai = N'Đã duyệt'";
            }
        // ---------------------

        if ($keyword !== '') $where .= " AND sp.ten_sanpham LIKE '%$keyword%'";
        if ($category !== '') {
            $where .= " AND (sp.id_danhmuc = '$category' OR sp.id_danhmuc IN (SELECT id_danhmuc FROM danhmuc WHERE id_parent = '$category'))";
        }
        if ($address !== '') $where .= " AND sp.khu_vuc_ban LIKE '%$address%'";
        if ($userId !== '')  $where .= " AND sp.id_user = '$userId' ";
<<<<<<< HEAD
=======
        
        // Thêm điều kiện kiểm tra tài khoản không bị khóa
        $where .= " AND (acc.trangthai IS NULL OR acc.trangthai != N'Bị khóa')";
>>>>>>> 2562b16aebed4df7dc3b06293e5d7411944c9081

        $sql = "SELECT sp.id_sanpham, sp.ten_sanpham, sp.gia, sp.mota, sp.avatar, sp.khu_vuc_ban, sp.ngaydang, sp.trangthai,
        dm.ten_danhmuc, dm.id_parent, acc.role,
        COALESCE(MIN(spa.url_anh), sp.avatar) AS anh_hienthi
        FROM sanpham sp
        LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
        LEFT JOIN account acc ON sp.id_user = acc.id_user
        LEFT JOIN sanpham_anh spa ON sp.id_sanpham = spa.id_sanpham
        " . $where . "
        GROUP BY sp.id_sanpham
        ORDER BY sp.ngaydang DESC
        LIMIT $offset, $limit";

        $result = mysqli_query($this->con, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }
    // Đếm tổng số sản phẩm (phục vụ phân trang)
    public function countProducts($keyword = '', $category = '', $address = '', $userId = '', $status = '')
    {
        $keyword = mysqli_real_escape_string($this->con, trim($keyword));
        $category = mysqli_real_escape_string($this->con, trim($category));
        $address = mysqli_real_escape_string($this->con, trim($address));
        $userId = mysqli_real_escape_string($this->con, trim($userId));
        $status = mysqli_real_escape_string($this->con, trim($status));

        $where = " WHERE 1";

        // Thêm điều kiện trạng thái nếu có
        if ($status !== '') {
            $where .= " AND sp.trangthai = N'$status'";
        } else {
            // Mặc định chỉ đếm sản phẩm đã duyệt (cho trang list)
            $where .= " AND sp.trangthai = N'Đã duyệt'";
        }

        if ($keyword !== '') $where .= " AND sp.ten_sanpham LIKE '%$keyword%'";
        if ($category !== '') {
            $where .= " AND (sp.id_danhmuc = '$category' OR sp.id_danhmuc IN (SELECT id_danhmuc FROM danhmuc WHERE id_parent = '$category'))";
        }
        if ($address !== '')  $where .= " AND sp.khu_vuc_ban LIKE '%$address%'";
        if ($userId !== '')   $where .= " AND sp.id_user = '$userId' ";

<<<<<<< HEAD
        $sql = "SELECT COUNT(*) AS total FROM sanpham sp" . $where;
=======
        $sql = "SELECT COUNT(*) AS total FROM sanpham sp
        LEFT JOIN account acc ON sp.id_user = acc.id_user
        " . $where . " AND (acc.trangthai IS NULL OR acc.trangthai != N'Bị khóa')";
>>>>>>> 2562b16aebed4df7dc3b06293e5d7411944c9081
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return (int)$row['total'];
        }
        return 0;
    }

     
    public function getProductById($id)
    {
        $id = mysqli_real_escape_string($this->con, $id);
        
        // Sử dụng LEFT JOIN thay vì JOIN (INNER JOIN) để tránh mất dữ liệu khi thiếu liên kết
        $sql = "SELECT s.*, u.hoten, u.sdt, u.avatar AS avatar_user, d.ten_danhmuc 
                FROM sanpham s
                LEFT JOIN users u ON s.id_user = u.id_user
                LEFT JOIN danhmuc d ON s.id_danhmuc = d.id_danhmuc
                WHERE s.id_sanpham = '$id'"; 
                
        $result = mysqli_query($this->con, $sql);
        
        if (!$result) {
            die("Lỗi SQL: " . mysqli_error($this->con));
        }
        
        return mysqli_fetch_assoc($result);
    }
    
    public function getProductImages($id)
    {
        $id = mysqli_real_escape_string($this->con, $id);
        $sql = "SELECT * FROM sanpham_anh WHERE id_sanpham = '$id'";
        $result = mysqli_query($this->con, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows; 
    }
    public function tangLuotXem($id_sanpham) {
        // Tăng view lên 1 đơn vị
        $id = mysqli_real_escape_string($this->con, $id_sanpham);
        $sql = "UPDATE sanpham SET luot_xem = luot_xem + 1 WHERE id_sanpham = '$id'";
        return mysqli_query($this->con, $sql);
    }
}
?>

