<div class="admin-page-header">
    <h2 style="margin: 0;"><i class="fas fa-flag"></i> Quản lý Báo cáo Vi phạm</h2>
    <div class="header-actions">
        <button onclick="exportToExcel()" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Xuất Excel
        </button>
        <span class="badge-count"><?= !empty($reports) ? count($reports) : 0 ?> đơn</span>
    </div>
</div>

<div class="card-large">
    <div class="table-responsive">
        <table class="admin-table" id="reportTable">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="18%">Người Tố Cáo</th>
                    <th width="18%">Người Bị Tố Cáo</th>
                    <th width="25%">Lý Do & Mô Tả</th>
                    <th width="12%">Bằng Chứng</th>
                    <th width="10%">Trạng Thái</th>
                    <th width="12%" class="text-right">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reports)): ?>
                    <?php foreach ($reports as $r): ?>
                        <tr>
                            <td><span class="id-hash">#<?= $r['id_report'] ?></span></td>
                            
                            <td>
                                <div class="user-cell">
                                    <div class="avatar-circle bg-blue">
                                        <?= strtoupper(substr($r['reporter_name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div class="user-info">
                                        <div class="name"><?= htmlspecialchars($r['reporter_name'] ?? 'Unknown') ?></div>
                                        <div class="sub-id">ID: <?= $r['reporter_id'] ?></div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="user-cell">
                                    <div class="avatar-circle bg-red">
                                        <?= strtoupper(substr($r['reported_name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div class="user-info">
                                        <div class="name text-danger"><?= htmlspecialchars($r['reported_name'] ?? 'Unknown') ?></div>
                                        <div class="sub-id">ID: <?= $r['reported_id'] ?></div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="reason-cell">
                                    <span class="reason-tag"><?= htmlspecialchars($r['reason']) ?></span>
                                    <p class="description" title="<?= htmlspecialchars($r['description']) ?>">
                                        <?= htmlspecialchars(mb_strimwidth($r['description'], 0, 60, "...")) ?>
                                    </p>
                                </div>
                            </td>

                            <td>
                                <?php if (!empty($r['evidence_image'])): ?>
                                    <a href="/baitaplon/<?= $r['evidence_image'] ?>" target="_blank" class="evidence-link">
                                        <img src="/baitaplon/<?= $r['evidence_image'] ?>" alt="Evidence">
                                        <span class="zoom-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    </a>
                                <?php else: ?>
                                    <span class="no-evidence">Không có</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php 
                                    $statusClass = '';
                                    $statusLabel = '';
                                    switch($r['status']) {
                                        case 'PENDING': $statusClass = 'pending'; $statusLabel = 'Chờ xử lý'; break;
                                        case 'PROCESSED': $statusClass = 'processed'; $statusLabel = 'Đã xử lý'; break;
                                        default: $statusClass = 'rejected'; $statusLabel = 'Đã hủy'; break;
                                    }
                                ?>
                                <span class="status-badge status-<?= $statusClass ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </td>

                            <td class="text-right">
                                <?php if ($r['status'] == 'PENDING'): ?>
                                    <form method="POST" action="/baitaplon/AdminReport/process" class="action-buttons">
                                        <input type="hidden" name="report_id" value="<?= $r['id_report'] ?>">
                                        <input type="hidden" name="reported_id" value="<?= $r['reported_id'] ?>">
                                        
                                        <button type="submit" name="action" value="BAN_USER" class="btn-icon btn-ban" title="Khóa tài khoản" onclick="return confirm('⚠️ CẢNH BÁO: Bạn có chắc chắn muốn KHÓA vĩnh viễn tài khoản này?')">
                                            <i class="fa-solid fa-gavel"></i>
                                        </button>
                                        
                                        <button type="submit" name="action" value="IGNORE" class="btn-icon btn-ignore" title="Bỏ qua báo cáo">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <i class="fa-solid fa-check-circle text-success" title="Hoàn tất"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="60" alt="Empty">
                            <p>Hiện tại không có báo cáo nào cần xử lý.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.badge-count {
    background: #e74c3c;
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
}

.btn-excel {
    background: #27ae60;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.2s;
}

.btn-excel:hover {
    background: #229954;
}

.id-hash {
    font-weight: bold;
    color: #666;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.bg-blue { background: #3498db; }
.bg-red { background: #e74c3c; }

.user-info .name {
    font-weight: 500;
    color: #333;
}

.user-info .sub-id {
    font-size: 12px;
    color: #999;
}

.text-danger { color: #e74c3c !important; }

.reason-cell .reason-tag {
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    color: #495057;
    border: 1px solid #e9ecef;
}

.reason-cell .description {
    margin: 8px 0 0 0;
    font-size: 13px;
    color: #666;
}

.evidence-link {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 50px;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid #ddd;
}

.evidence-link img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.evidence-link .zoom-icon {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transition: opacity 0.2s;
}

.evidence-link:hover .zoom-icon {
    opacity: 1;
}

.no-evidence {
    color: #999;
    font-size: 13px;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-processed {
    background: #d4edda;
    color: #155724;
}

.status-rejected {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-ban {
    background: #e74c3c;
    color: white;
}

.btn-ban:hover {
    background: #c0392b;
}

.btn-ignore {
    background: #95a5a6;
    color: white;
}

.btn-ignore:hover {
    background: #7f8c8d;
}

.text-success {
    color: #27ae60;
    font-size: 20px;
}

.admin-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.header-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}
</style>

<script src="/baitaplon/public/js/exportExcel.js"></script>
