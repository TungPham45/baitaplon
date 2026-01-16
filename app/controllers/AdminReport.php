<?php
// app/controllers/AdminReport.php

require_once __DIR__ . '/../models/AdminReportModel.php';
require_once __DIR__ . '/../models/UserModel.php'; // Đã require UserModel

class AdminReport {
    private $reportModel;
    private $userModel; // Khai báo property
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->reportModel = new AdminReportModel($conn);
        
        // ✅ KHỞI TẠO USER MODEL
        $this->userModel = new UserModel($conn); 
    }

    public function index() {
        // Kiểm tra quyền Admin ở đây nếu cần
        
        $reports = $this->reportModel->getAllReports();
        
        $active_page = 'report_management';
        $functionTitle = "Quản lý Báo cáo Vi phạm";
        // Đảm bảo đường dẫn view đúng
        $contentView = __DIR__ . '/../views/admin/report_management.php'; 
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

        public function process() {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $report_id   = $_POST['report_id'] ?? null;
                    $action      = $_POST['action'] ?? null;
                    $reported_id = $_POST['reported_id'] ?? null;
                    // Nhận lý do từ form
                    $ban_reason  = $_POST['ban_reason'] ?? 'Vi phạm điều khoản sử dụng'; 

                    if ($report_id && $reported_id && $action === 'BAN_USER') {
                        
                        // 1. Cập nhật trạng thái báo cáo (AdminReportModel)
                        $this->reportModel->updateStatus($report_id, 'PROCESSED', 'Đã khóa tài khoản. Lý do: ' . $ban_reason);
                        

                        $isBanned = $this->userModel->banUser($reported_id, $ban_reason);
                        if ($isBanned) {
                            echo "<script>
                                alert('Đã khóa tài khoản ID: $reported_id thành công!'); 
                                window.location.href='/baitaplon/AdminReport';
                            </script>";
                        } else {
                            // Sửa thông báo lỗi
                            echo "<script>
                                alert('Thất bại! Có thể lỗi hệ thống hoặc BẠN KHÔNG THỂ KHÓA TÀI KHOẢN ADMIN/QUẢN LÝ.'); 
                                window.location.href='/baitaplon/AdminReport';
                            </script>";
                        }

                    } elseif ($report_id && $action === 'IGNORE') {
                        // ... (Giữ nguyên phần ignore) ...
                        $this->reportModel->updateStatus($report_id, 'REJECTED', 'Báo cáo không đủ căn cứ.');
                        echo "<script>alert('Đã hủy bỏ báo cáo.'); window.location.href='/baitaplon/AdminReport';</script>";
                    } else {
                        header("Location: /baitaplon/AdminReport");
                    }
                }
            }




        public function exportExcel() {
        // 1. Lấy TOÀN BỘ dữ liệu từ Model (Không phân trang)
        // Giả sử hàm getAllReports() của bạn đã lấy đủ thông tin (Join bảng users để lấy tên)
        $reports = $this->reportModel->getAllReports(); 

        // 2. Đặt tên file
        $filename = "Bao_Cao_Full_" . date('Y-m-d_H-i') . ".csv";

        // 3. Cấu hình Header để trình duyệt hiểu đây là file tải về
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // 4. Mở luồng ghi dữ liệu (Output Stream)
        $output = fopen('php://output', 'w');

        // --- QUAN TRỌNG: Thêm BOM để Excel hiển thị đúng Tiếng Việt ---
        fputs($output, "\xEF\xBB\xBF");

        // 5. Viết dòng Tiêu đề (Header)
        // Dùng dấu chấm phẩy (;) để Excel tự chia cột
        fputcsv($output, [
            'ID', 
            'Người Tố Cáo', 
            'Người Bị Tố Cáo', 
            'Lý Do', 
            'Mô Tả Chi Tiết', 
            'Bằng Chứng (Link)', 
            'Trạng Thái',
            'Ngày Tạo'
        ], ';'); 

        // 6. Duyệt dữ liệu và ghi từng dòng
        foreach ($reports as $row) {
            
            // Xử lý dữ liệu thô thành dữ liệu đẹp (nếu cần)
            $statusLabel = '';
            switch ($row['status']) {
                case 'PENDING': $statusLabel = 'Chờ xử lý'; break;
                case 'PROCESSED': $statusLabel = 'Đã xử lý'; break;
                case 'REJECTED': $statusLabel = 'Đã hủy'; break;
                default: $statusLabel = $row['status'];
            }

            // Tạo link ảnh đầy đủ (nếu có)
            $evidenceLink = !empty($row['evidence_image']) ? "http://localhost/baitaplon/" . $row['evidence_image'] : "Không có";

            // Ghi dòng dữ liệu vào file
            fputcsv($output, [
                $row['id_report'],
                $row['reporter_name'] . " (ID: " . $row['reporter_id'] . ")", // Gộp tên + ID
                $row['reported_name'] . " (ID: " . $row['reported_id'] . ")",
                $row['reason'],
                $row['description'],
                $evidenceLink,
                $statusLabel,
                $row['created_at'] // Giả sử trong DB có cột này
            ], ';');
        }

        // 7. Đóng luồng và kết thúc
        fclose($output);
        exit;
    }

}
?>