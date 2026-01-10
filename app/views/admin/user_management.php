<div class="card-large">
    <div class="table-header">
        <h2><i class="fas fa-users-cog"></i> Quản lý tài khoản hệ thống</h2>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ và tên</th>
                <th>Email</th>
                <th>Mật khẩu</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($accounts as $acc): ?>
            <tr>
                <td><strong>#<?php echo $acc['id_user']; ?></strong></td>
                <td><?php echo $acc['hoten']; ?></td>
                <td><?php echo $acc['email']; ?></td>
                <td style="letter-spacing: 3px;">********</td>
                <td>
                    <span class="badge badge-<?php echo str_replace(' ', '-', $acc['trangthai']); ?>">
                        <?php echo $acc['trangthai']; ?>
                    </span>
                </td>
                <td>
                    <button class="btn-detail" onclick="openModal('<?php echo $acc['id_user']; ?>')">
                        <i class="fas fa-info-circle"></i> Xem chi tiết
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="accountModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header-custom">
            <h4>Chi tiết hồ sơ người dùng</h4>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>
        <div id="modalBody" class="modal-body-custom">
            </div>
    </div>
</div>