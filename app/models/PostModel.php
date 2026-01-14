<?php
class PostModel
{
    private $con; // Đặt tên biến là $con cho giống convention của duchaitest

    public function __construct($conn)
    {
        $this->con = $conn;
    }

    // Insert sản phẩm
    public function insertProduct($ten_sanpham, $id_danhmuc, $id_user, $gia, $mota, $anh_dai_dien, $khu_vuc_ban)
    {
        // Escape dữ liệu để tránh SQL Injection
        $ten_sanpham = mysqli_real_escape_string($this->con, $ten_sanpham);
        $id_user = mysqli_real_escape_string($this->con, $id_user); // US002...
        $mota = mysqli_real_escape_string($this->con, $mota);
        $anh_dai_dien = mysqli_real_escape_string($this->con, $anh_dai_dien);
        $khu_vuc_ban = mysqli_real_escape_string($this->con, $khu_vuc_ban);

            // SQL: id_user là VARCHAR nên cần dấu nháy đơn '$id_user'
        $sql = "INSERT INTO sanpham (ten_sanpham, id_danhmuc, id_user, gia, mota, avatar, khu_vuc_ban, ngaydang, trangthai) 
                VALUES ('$ten_sanpham', '$id_danhmuc', '$id_user', '$gia', '$mota', '$anh_dai_dien', '$khu_vuc_ban', NOW(), N'Chờ duyệt')";

        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            error_log("SQL Error: " . mysqli_error($this->con) . " | SQL: " . $sql);
        }
        return $result ? mysqli_insert_id($this->con) : false;
    }

    // Insert ảnh chi tiết sản phẩm
    public function insertProductImage($id_sanpham, $url_anh)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $url_anh = mysqli_real_escape_string($this->con, $url_anh);

        $sql = "INSERT INTO sanpham_anh (id_sanpham, url_anh) VALUES ('$id_sanpham', '$url_anh')";
        return mysqli_query($this->con, $sql);
    }

    // Insert giá trị thuộc tính (Nếu DB duchaitest có bảng này)
    public function insertAttributeValue($id_sanpham, $id_thuoctinh, $id_option)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $id_thuoctinh = mysqli_real_escape_string($this->con, $id_thuoctinh);
        $id_option = mysqli_real_escape_string($this->con, $id_option);

        $sql = "INSERT INTO gia_tri_thuoc_tinh (id_sanpham, id_thuoctinh, id_option) 
                VALUES ('$id_sanpham', '$id_thuoctinh', '$id_option')";
        return mysqli_query($this->con, $sql);
    }
    // Cập nhật trạng thái (Dùng cho nút "Đã bán")
    public function updateStatus($id, $status) {
        $id = mysqli_real_escape_string($this->con, $id);
        $status = mysqli_real_escape_string($this->con, $status);
        $sql = "UPDATE sanpham SET trangthai = '$status' WHERE id_sanpham = '$id'";
        return mysqli_query($this->con, $sql);
    }

    // Xóa sản phẩm
    public function deleteProduct($id) {
        $id = mysqli_real_escape_string($this->con, $id);
        // Xóa ảnh phụ trước
        mysqli_query($this->con, "DELETE FROM sanpham_anh WHERE id_sanpham = '$id'");
        // Xóa thuộc tính
        mysqli_query($this->con, "DELETE FROM gia_tri_thuoc_tinh WHERE id_sanpham = '$id'");
        // Xóa sản phẩm
        $sql = "DELETE FROM sanpham WHERE id_sanpham = '$id'";
        return mysqli_query($this->con, $sql);
    }

    // Cập nhật thông tin (Dùng cho nút "Lưu thay đổi")
    public function updateProduct($id, $data) {
        $sets = [];
        foreach ($data as $key => $val) {
            $val = mysqli_real_escape_string($this->con, $val);
            $sets[] = "$key = '$val'";
        }
        $setString = implode(', ', $sets);
        $sql = "UPDATE sanpham SET $setString WHERE id_sanpham = '$id'";
        
        return mysqli_query($this->con, $sql);
    }
}
?>