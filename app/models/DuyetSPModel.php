<?php
class DuyetSPModel
{
    private $con;

    public function __construct($conn)
    {
        $this->con = $conn;
    }

    // Lấy tất cả sản phẩm chờ duyệt
    public function getPendingProducts()
    {
        // Dùng subquery thay vì JOIN để tránh duplicate rows
        $sql = "SELECT sp.id_sanpham, sp.ten_sanpham, sp.gia, sp.mota, sp.avatar, sp.id_danhmuc, sp.id_user, sp.ngaydang, sp.trangthai,
                        (SELECT ten_danhmuc FROM danhmuc WHERE id_danhmuc = sp.id_danhmuc LIMIT 1) as ten_danhmuc,
                        (SELECT hoten FROM users WHERE id_user = sp.id_user LIMIT 1) as hoten,
                        (SELECT sdt FROM users WHERE id_user = sp.id_user LIMIT 1) as sdt
                FROM sanpham sp
                WHERE sp.trangthai = 'Chờ duyệt'
                ORDER BY sp.ngaydang DESC";

        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            error_log("MySQL Error: " . mysqli_error($this->con));
            return [];
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        return $data;
    }

    // Lấy chi tiết sản phẩm để duyệt
    public function getProductDetail($id_sanpham)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        
        // Dùng subquery để tránh duplicate
        $sql = "SELECT sp.*, 
                       (SELECT ten_danhmuc FROM danhmuc WHERE id_danhmuc = sp.id_danhmuc LIMIT 1) as ten_danhmuc,
                       (SELECT hoten FROM users WHERE id_user = sp.id_user LIMIT 1) as hoten,
                       (SELECT sdt FROM users WHERE id_user = sp.id_user LIMIT 1) as sdt,
                       (SELECT diachi FROM users WHERE id_user = sp.id_user LIMIT 1) as diachi
                FROM sanpham sp
                WHERE sp.id_sanpham = '$id_sanpham'";

        $result = mysqli_query($this->con, $sql);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }

    // Lấy ảnh sản phẩm
    public function getProductImages($id_sanpham)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $sql = "SELECT id_anh, url_anh FROM sanpham_anh WHERE id_sanpham = '$id_sanpham'";
        
        $result = mysqli_query($this->con, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Lấy thuộc tính sản phẩm với tên thuộc tính và giá trị
    public function getProductAttributes($id_sanpham)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        
        // 1. Lấy id_danhmuc (giữ nguyên logic của bạn)
        $sql_sp = "SELECT id_danhmuc FROM sanpham WHERE id_sanpham = '$id_sanpham'";
        $result_sp = mysqli_query($this->con, $sql_sp);
        $product = mysqli_fetch_assoc($result_sp);
        
        if (!$product) {
            return [];
        }
        
        $id_danhmuc = $product['id_danhmuc'];
        
        // 2. Lấy thuộc tính (ĐÃ SỬA SQL)
        $sql = "SELECT 
                    tt.ten_thuoctinh,
                    CASE 
                        WHEN opt.id_option IS NOT NULL THEN opt.gia_tri_option
                        ELSE gvt.id_option
                    END as giatri,
                    tt.id_thuoctinh
                FROM gia_tri_thuoc_tinh gvt
                INNER JOIN thuoc_tinh tt ON gvt.id_thuoctinh = tt.id_thuoctinh
                LEFT JOIN thuoc_tinh_options opt 
                    ON gvt.id_option = CAST(opt.id_option AS CHAR) 
                    AND opt.id_thuoctinh = tt.id_thuoctinh  -- [QUAN TRỌNG]: Thêm dòng này để chặn khớp sai
                WHERE gvt.id_sanpham = '$id_sanpham'
                ORDER BY tt.id_thuoctinh";

        $result = mysqli_query($this->con, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[$row['id_thuoctinh']] = [
                    'ten_thuoctinh' => $row['ten_thuoctinh'],
                    'giatri' => $row['giatri']
                ];
            }
        }
        
        // 3. Fallback nếu không có dữ liệu (Giữ nguyên logic của bạn)
        if (empty($data)) {
            $sql_attrs = "SELECT tt.id_thuoctinh, tt.ten_thuoctinh 
                          FROM thuoc_tinh tt 
                          WHERE tt.id_danhmuc = '$id_danhmuc'
                          ORDER BY tt.id_thuoctinh";
            $result_attrs = mysqli_query($this->con, $sql_attrs);
            if ($result_attrs) {
                while ($row = mysqli_fetch_assoc($result_attrs)) {
                    $data[$row['id_thuoctinh']] = [
                        'ten_thuoctinh' => $row['ten_thuoctinh'],
                        'giatri' => 'Chưa cập nhật'
                    ];
                }
            }
        }
        
        return array_values($data);
    }

    // Duyệt sản phẩm
    public function approveProduct($id_sanpham)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $sql = "UPDATE sanpham SET trangthai = 'Đã duyệt' WHERE id_sanpham = '$id_sanpham'";
        return mysqli_query($this->con, $sql);
    }

    // Duyệt tất cả
    public function approveAllProducts()
    {
        $sql = "UPDATE sanpham SET trangthai = 'Đã duyệt' WHERE trangthai = 'Chờ duyệt'";
        return mysqli_query($this->con, $sql);
    }

    // Từ chối sản phẩm
    public function rejectProduct($id_sanpham, $lydo = '')
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $lydo = mysqli_real_escape_string($this->con, $lydo);
        $sql = "UPDATE sanpham SET trangthai = 'Từ chối', lydotuchoi = '$lydo' WHERE id_sanpham = '$id_sanpham'";
        return mysqli_query($this->con, $sql);
    }
}
?>