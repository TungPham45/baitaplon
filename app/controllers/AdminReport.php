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

            if ($report_id && $reported_id && $action === 'BAN_USER') {
                
                // 1. Cập nhật trạng thái báo cáo
                $this->reportModel->updateStatus($report_id, 'PROCESSED', 'Đã khóa tài khoản người dùng.');
                
                // 2. ✅ GỌI HÀM KHÓA TÀI KHOẢN TỪ USER MODEL
                $isBanned = $this->userModel->banUser($reported_id);
                
                if ($isBanned) {
                    echo "<script>
                        alert('Đã khóa tài khoản US$reported_id thành công!'); 
                        window.location.href='/baitaplon/AdminReport';
                    </script>";
                } else {
                    echo "<script>
                        alert('Có lỗi xảy ra khi khóa tài khoản.'); 
                        window.location.href='/baitaplon/AdminReport';
                    </script>";
                }

            } elseif ($report_id && $action === 'IGNORE') {
                // Từ chối báo cáo
                $this->reportModel->updateStatus($report_id, 'REJECTED', 'Báo cáo không đủ căn cứ.');
                echo "<script>
                    alert('Đã hủy bỏ báo cáo.'); 
                    window.location.href='/baitaplon/AdminReport';
                </script>";
            } else {
                // Xử lý trường hợp thiếu dữ liệu
                header("Location: /baitaplon/AdminReport");
            }
        }
    }
}
?>