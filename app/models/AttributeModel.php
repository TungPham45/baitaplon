<?php
class AttributeModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAttributesByCategory($id_danhmuc) {
        $id_parent = null;

        // 1. Lấy ID cha của danh mục để lấy thuộc tính kế thừa (ví dụ: Điện thoại kế thừa từ Đồ điện tử)
        $stmtParent = $this->conn->prepare("SELECT id_parent FROM danhmuc WHERE id_danhmuc = ?");
        $stmtParent->bind_param("i", $id_danhmuc);
        $stmtParent->execute();
        $resultParent = $stmtParent->get_result();
        $row = $resultParent->fetch_assoc();
        
        if ($row && !empty($row['id_parent'])) {
            $id_parent = $row['id_parent'];
        }
        $stmtParent->close();

        // 2. Lấy thuộc tính của chính nó HOẶC của cha nó
        $sql = "SELECT * FROM thuoc_tinh WHERE id_danhmuc = ? OR (id_danhmuc IS NOT NULL AND id_danhmuc = ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id_danhmuc, $id_parent);
        $stmt->execute();
        $attributes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // 3. Lấy Options cho từng thuộc tính
        foreach ($attributes as &$attr) {
            $stmtOpt = $this->conn->prepare("SELECT id_option, gia_tri_option FROM thuoc_tinh_options WHERE id_thuoctinh = ?");
            $stmtOpt->bind_param("i", $attr['id_thuoctinh']);
            $stmtOpt->execute();
            $attr['options'] = $stmtOpt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmtOpt->close();
        }
        unset($attr);

        return $attributes;
    }
}
?>
