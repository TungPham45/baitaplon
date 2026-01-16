<div class="card-large">
    <div class="table-header">
        <h2><i class="fas fa-users-cog"></i> Quản lý tài khoản hệ thống</h2>
    </div>
    
    <div class="search-box">
        <form id="searchForm" method="GET" action="/baitaplon/Admin/dashboard">
            <div class="search-row">
                <div class="search-group">
                    <label for="hoten">Họ và tên:</label>
                    <input type="text" id="hoten" name="hoten" placeholder="Nhập họ và tên..." 
                           value="<?php echo isset($_GET['hoten']) ? htmlspecialchars($_GET['hoten']) : ''; ?>">
                </div>
                <div class="search-group">
                    <label for="trangthai">Trạng thái:</label>
                    <select id="trangthai" name="trangthai">
                        <option value="all">Tất cả</option>
                        <option value="Hoạt động" <?php echo (isset($_GET['trangthai']) && $_GET['trangthai'] === 'Hoạt động') ? 'selected' : ''; ?>>Hoạt động</option>
                        <option value="Chờ duyệt" <?php echo (isset($_GET['trangthai']) && $_GET['trangthai'] === 'Chờ duyệt') ? 'selected' : ''; ?>>Chờ duyệt</option>
                        <option value="Bị khóa" <?php echo (isset($_GET['trangthai']) && $_GET['trangthai'] === 'Bị khóa') ? 'selected' : ''; ?>>Bị khóa</option>
                    </select>
                </div>
                <div class="search-group btn-group">
                    <button type="submit" class="btn-search">
                        Tìm kiếm
                    </button>
                    <a href="/baitaplon/Admin/dashboard" class="btn-reset">
                         Tải lại
                    </a>
                    <button type="button" class="btn-export" id="btnExport">
                         Xuất Excel
                    </button>
                </div>
            </div>
        </form>
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
        <tbody id="accountTableBody">
            <?php foreach ($accounts as $acc): ?>
            <tr>
                <td><strong>#<?php echo $acc['id_user']; ?></strong></td>
                <td><?php echo $acc['hoten']; ?></td>
                <td><?php echo $acc['email']; ?></td>
                <td style="letter-spacing: 3px;">********</td>
                <td>
                    <?php 
                    $statusClass = '';
                    $statusText = $acc['trangthai'];
                    if ($statusText === 'Hoạt động') {
                        $statusClass = 'Hoạt-động';
                    } else if ($statusText === 'Chờ duyệt') {
                        $statusClass = 'Chờ-duyệt';
                    } else if ($statusText === 'Khóa' || $statusText === 'Bị khóa') {
                        $statusClass = 'Bị-khóa';
                    }
                    ?>
                    <span class="badge badge-<?php echo $statusClass; ?>">
                        <?php echo $statusText; ?>
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

<script>
document.getElementById('btnExport').addEventListener('click', function() {
    var hoten = document.getElementById('hoten').value;
    var trangthai = document.getElementById('trangthai').value;
    
    var url = '/baitaplon/Admin/exportAccounts?hoten=' + encodeURIComponent(hoten) + '&trangthai=' + encodeURIComponent(trangthai);
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.blob();
        })
        .then(blob => {
            var link = document.createElement('a');
            var url = window.URL.createObjectURL(blob);
            link.href = url;
            link.download = 'danh_sach_tai_khoan_' + new Date().toISOString().slice(0,10) + '.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xuất Excel!');
        });
});
</script>
