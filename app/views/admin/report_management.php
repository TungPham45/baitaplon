<link rel="stylesheet" href="/baitaplon/public/css/AdminReport.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="admin-page-header">
    <h2 style="margin: 0; color: #2c3e50;"><i class="fas fa-flag text-danger"></i> Quản lý Báo cáo Vi phạm</h2>
    <div class="header-actions">
        <button onclick="exportToExcel()" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Xuất Excel
        </button>
        <span class="badge-count"><?= !empty($reports) ? count($reports) : 0 ?> đơn</span>
    </div>
</div>

<div class="card-large">
    
    <div class="filter-toolbar">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Tìm tên người tố cáo hoặc bị tố cáo..." onkeyup="filterTable()">
        </div>

        <div class="filter-select">
            <select id="statusFilter" onchange="filterTable()">
                <option value="all">-- Tất cả trạng thái --</option>
                <option value="PENDING">⏳ Chờ xử lý</option>
                <option value="PROCESSED">✅ Đã xử lý</option>
                <option value="REJECTED">❌ Đã hủy</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table" id="reportTable">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="20%">Người Tố Cáo</th>
                    <th width="20%">Người Bị Tố Cáo</th>
                    <th width="25%">Lý Do & Mô Tả</th>
                    <th width="10%">Bằng Chứng</th>
                    <th width="10%">Trạng Thái</th>
                    <th width="10%" class="text-right">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reports)): ?>
                    <?php foreach ($reports as $r): ?>
                        <tr data-status="<?= $r['status'] ?>">
                            <td><span class="id-hash">#<?= $r['id_report'] ?></span></td>
                            
                            <td>
                                <div class="user-cell">
                                    <div class="avatar-circle bg-blue">
                                        <?= strtoupper(substr($r['reporter_name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div class="user-info">
                                        <div class="name reporter-name"><?= htmlspecialchars($r['reporter_name'] ?? 'Unknown') ?></div>
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
                                        <div class="name text-danger reported-name"><?= htmlspecialchars($r['reported_name'] ?? 'Unknown') ?></div>
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
                                    <div class="action-buttons">
                                        <button type="button" 
                                                class="btn-icon btn-ban" 
                                                title="Khóa tài khoản" 
                                                onclick="openBanModal('<?= $r['id_report'] ?>', '<?= $r['reported_id'] ?>', '<?= htmlspecialchars($r['reported_name']) ?>')">
                                            <i class="fa-solid fa-gavel"></i>
                                        </button>
                                        
                                        <form method="POST" action="/baitaplon/AdminReport/process" style="display:inline;">
                                            <input type="hidden" name="report_id" value="<?= $r['id_report'] ?>">
                                            <input type="hidden" name="action" value="IGNORE">
                                            <button type="submit" class="btn-icon btn-ignore" title="Bỏ qua báo cáo">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <i class="fa-solid fa-check-circle text-success" title="Hoàn tất"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty-state" style="text-align: center; padding: 60px 20px;">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="60" alt="Empty" style="opacity: 0.5; margin-bottom: 10px;">
                            <p style="color: #999;">Hiện tại không có báo cáo nào cần xử lý.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="banModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <i class="fa-solid fa-triangle-exclamation"></i> Xác nhận Khóa Tài Khoản
        </div>
        <form method="POST" action="/baitaplon/AdminReport/process">
            <input type="hidden" name="action" value="BAN_USER">
            <input type="hidden" name="report_id" id="modal_report_id">
            <input type="hidden" name="reported_id" id="modal_reported_id">
            
            <div class="form-group">
                <label>Người bị khóa:</label>
                <input type="text" id="modal_user_name" class="form-control" readonly style="background: #f8f9fa; color: #555;">
            </div>

            <div class="form-group">
                <label for="ban_reason">Lý do khóa (Sẽ hiện khi user đăng nhập): <span style="color:red">*</span></label>
                <textarea name="ban_reason" id="ban_reason" class="form-control" rows="3" required placeholder="Vd: Vi phạm ngôn từ, Spam, Lừa đảo..."></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeBanModal()">Hủy</button>
                <button type="submit" class="btn-confirm">Xác nhận Khóa</button>
            </div>
        </form>
    </div>
</div>

<script src="/baitaplon/public/js/exportExcel.js"></script>
<script src="/baitaplon/public/js/SearchNameReport.js"></script>
