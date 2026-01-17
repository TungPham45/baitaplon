<?php
// app/models/ReportModel.php

class ReportModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createReport($reporter_id, $target_id, $reason, $description, $evidence_image) {
        $sql = "INSERT INTO reports (reporter_id, reported_id, reason, description, evidence_image, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'PENDING', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        // sssss: 5 chuỗi (reporter, reported, reason, description, image)
        $stmt->bind_param("sssss", $reporter_id, $target_id, $reason, $description, $evidence_image);
        
        return $stmt->execute();
    }

    public function checkPendingReport($reporter_id, $target_id) {
        // Logic: Tìm xem có đơn nào của Reporter gửi cho Target mà trạng thái đang là 'PENDING' không?
        $sql = "SELECT id_report 
                FROM reports 
                WHERE reporter_id = ? 
                AND reported_id = ? 
                AND status = 'PENDING'";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $reporter_id, $target_id);
        $stmt->execute();
        $stmt->store_result();
        
        // Trả về TRUE nếu tìm thấy (tức là ĐANG CÓ đơn chưa xử lý -> bị trùng)
        return $stmt->num_rows > 0;
    }
        public function checkPendingReportByReason($reason) {
        // Logic: Tìm xem có bất kỳ đơn nào có lý do này đang ở trạng thái 'PENDING' không
        $sql = "SELECT id_report 
                FROM reports 
                WHERE reason = ? 
                AND status = 'PENDING'";
                
        $stmt = $this->conn->prepare($sql);
        
        // "s" đại diện cho string (lý do thường là văn bản)
        $stmt->bind_param("s", $reason); 
        
        $stmt->execute();
        $stmt->store_result();
        
        // Trả về TRUE nếu lý do này đã tồn tại và đang chờ xử lý
        return $stmt->num_rows > 0;
    }
        public function checkPendingReportByTargetAndReason($target_id, $reason) {
        // Logic: Tìm xem Người bị báo cáo (Target) đã bị báo cáo cùng 1 lý do (Reason) 
        // mà đơn đó vẫn đang chờ xử lý (PENDING) hay chưa?
        $sql = "SELECT id_report 
                FROM reports 
                WHERE reported_id = ? 
                AND reason = ? 
                AND status = 'PENDING'";
                
        $stmt = $this->conn->prepare($sql);
        
        // "ss" vì cả target_id và reason đều là chuỗi (string)
        $stmt->bind_param("ss", $target_id, $reason);
        
        $stmt->execute();
        $stmt->store_result();
        
        // Nếu num_rows > 0 tức là đã tồn tại một đơn trùng y hệt đang chờ duyệt
        return $stmt->num_rows > 0;
    }

}
?>