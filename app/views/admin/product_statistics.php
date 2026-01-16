<?php
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$seller = isset($_GET['seller']) ? $_GET['seller'] : '';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Thống kê sản phẩm
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Bộ lọc -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="/baitaplon/Admin/productStatistics" id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Bộ lọc sản phẩm</label>
                                        <div class="dropdown">
                                            <input type="text" class="form-control" id="filterInput"
                                                   placeholder="Chọn các tiêu chí lọc..."
                                                   readonly data-bs-toggle="dropdown">
                                            <div class="dropdown-menu p-3" style="min-width: 500px;">
                                                <!-- Trạng thái -->
                                                <div class="mb-3">
                                                    <h6 class="fw-bold mb-2">
                                                        <i class="fas fa-info-circle me-1"></i>Trạng thái
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input filter-checkbox" type="checkbox"
                                                                       id="status_all" value="all" name="status[]">
                                                                <label class="form-check-label" for="status_all">
                                                                    Tất cả
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input filter-checkbox" type="checkbox"
                                                                       id="status_duyet" value="Đã duyệt" name="status[]">
                                                                <label class="form-check-label" for="status_duyet">
                                                                    Đang bán
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input filter-checkbox" type="checkbox"
                                                                       id="status_cho" value="Chờ duyệt" name="status[]">
                                                                <label class="form-check-label" for="status_cho">
                                                                    Chưa duyệt
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input filter-checkbox" type="checkbox"
                                                                       id="status_dung" value="Dừng bán" name="status[]">
                                                                <label class="form-check-label" for="status_dung">
                                                                    Dừng bán
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input filter-checkbox" type="checkbox"
                                                                       id="status_ban" value="Đã bán" name="status[]">
                                                                <label class="form-check-label" for="status_ban">
                                                                    Đã bán
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Danh mục -->
                                                <div class="mb-3">
                                                    <h6 class="fw-bold mb-2">
                                                        <i class="fas fa-tags me-1"></i>Danh mục
                                                    </h6>
                                                    <div class="row">
                                                        <?php if (isset($categoryTree) && !empty($categoryTree)): ?>
                                                            <?php foreach ($categoryTree as $parent): ?>
                                                                <div class="col-md-6 mb-2">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input filter-checkbox category-checkbox"
                                                                               type="checkbox" id="cat_<?= $parent['id'] ?>"
                                                                               value="<?= $parent['id'] ?>" name="categories[]">
                                                                        <label class="form-check-label fw-bold" for="cat_<?= $parent['id'] ?>">
                                                                            <?= htmlspecialchars($parent['name']) ?>
                                                                        </label>
                                                                    </div>
                                                                    <?php if (!empty($parent['children'])): ?>
                                                                        <div class="ms-3">
                                                                            <?php foreach ($parent['children'] as $child): ?>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input filter-checkbox subcategory-checkbox"
                                                                                           type="checkbox" id="subcat_<?= $child['id'] ?>"
                                                                                           value="<?= $child['id'] ?>" name="categories[]">
                                                                                    <label class="form-check-label small" for="subcat_<?= $child['id'] ?>">
                                                                                        <?= htmlspecialchars($child['name']) ?>
                                                                                    </label>
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Nút áp dụng trong dropdown -->
                                                <div class="d-flex gap-2 mt-3">
                                                    <button type="button" class="btn btn-primary btn-sm" id="applyFilters">
                                                        <i class="fas fa-check"></i> Áp dụng
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm" id="clearFilters">
                                                        <i class="fas fa-times"></i> Xóa tất cả
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <label class="form-label">Tháng</label>
                                        <select name="month" class="form-select">
                                            <option value="">Tất cả</option>
                                            <?php for($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?= $i ?>" <?= (isset($_GET['month']) && $_GET['month'] == $i) ? 'selected' : '' ?>>
                                                    <?= $i ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-1">
                                        <label class="form-label">Năm</label>
                                        <select name="year" class="form-select">
                                            <option value="">Tất cả</option>
                                            <?php
                                            $currentYear = date('Y');
                                            for($i = $currentYear; $i >= $currentYear - 5; $i--):
                                            ?>
                                                <option value="<?= $i ?>" <?= (isset($_GET['year']) && $_GET['year'] == $i) ? 'selected' : '' ?>>
                                                    <?= $i ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Lọc
                                            </button>
                                            <a href="/baitaplon/Admin/productStatistics" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Xóa lọc
                                            </a>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" id="exportExcel" class="btn btn-success w-100">
                                            <i class="fas fa-file-excel"></i> Xuất Excel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Bảng thống kê -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="productsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Mã SP</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Danh mục</th>
                                    <th>Người bán</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đăng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <br>Không có sản phẩm nào phù hợp
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <a href="/baitaplon/Home/detail_Sanpham/<?= $product['id_sanpham'] ?>/<?= $_SESSION['user_id'] ?>"
                                                   class="text-decoration-none fw-bold text-primary">
                                                    <?= htmlspecialchars($product['id_sanpham']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($product['anh_hienthi'] && file_exists($_SERVER['DOCUMENT_ROOT'] . '/baitaplon/' . $product['anh_hienthi'])): ?>
                                                        <img src="/baitaplon/<?= htmlspecialchars($product['anh_hienthi']) ?>"
                                                             class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <span title="<?= htmlspecialchars($product['ten_sanpham']) ?>">
                                                        <?= htmlspecialchars(substr($product['ten_sanpham'], 0, 50)) ?>
                                                        <?= strlen($product['ten_sanpham']) > 50 ? '...' : '' ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="fw-bold text-danger">
                                                <?= number_format($product['gia']) ?> đ
                                            </td>
                                            <td>
                                                <?php
                                                // Hiển thị tên danh mục hiện tại (con)
                                                echo htmlspecialchars($product['ten_danhmuc'] ?? 'N/A');
                                                ?>
                                            </td>
                                            <td>
                                                <span title="ID: <?= $product['id_user'] ?>">
                                                    <?= htmlspecialchars($product['nguoi_ban']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                $displayText = '';
                                                switch ($product['trangthai']) {
                                                    case 'Đã duyệt':
                                                        $statusClass = 'badge bg-success';
                                                        $displayText = 'Đang bán';
                                                        break;
                                                    case 'Chờ duyệt':
                                                        $statusClass = 'badge bg-warning text-dark';
                                                        $displayText = 'Chưa duyệt';
                                                        break;
                                                    case 'Dừng bán':
                                                        $statusClass = 'badge bg-danger';
                                                        $displayText = 'Dừng bán';
                                                        break;
                                                    case 'Đã bán':
                                                        $statusClass = 'badge bg-secondary';
                                                        $displayText = 'Đã bán';
                                                        break;
                                                    default:
                                                        $statusClass = 'badge bg-light text-dark';
                                                        $displayText = $product['trangthai'];
                                                }
                                                ?>
                                                <span class="<?= $statusClass ?>">
                                                    <?= htmlspecialchars($displayText) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($product['ngaydang'])) ?>
                                            </td>
                                            <td>
                                                <?php if ($product['trangthai'] === 'Đã duyệt'): ?>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger stop-selling-btn"
                                                            data-id="<?= $product['id_sanpham'] ?>"
                                                            data-name="<?= htmlspecialchars($product['ten_sanpham']) ?>"
                                                            title="Dừng bán sản phẩm">
                                                        <i class="fas fa-stop"></i> Dừng bán
                                                    </button>
                                                <?php else: ?>
                                                    <?php
                                                    // Hiển thị lý do dừng bán hoặc trạng thái
                                                    switch ($product['trangthai']) {
                                                        case 'Chờ duyệt':
                                                            echo '<span class="text-warning"><i class="fas fa-clock"></i> Chưa duyệt</span>';
                                                            break;
                                                        case 'Dừng bán':
                                                            // Trích xuất lý do dừng bán từ cột mota
                                                            $reason = 'Đã dừng bán';
                                                            if (isset($product['mota']) && !empty($product['mota'])) {
                                                                // Tìm pattern [Lý do dừng bán: ...]
                                                                if (preg_match('/\[Lý do dừng bán: ([^\]]+)\]/', $product['mota'], $matches)) {
                                                                    $reason = htmlspecialchars(trim($matches[1]));
                                                                }
                                                            }
                                                            echo '<span class="text-danger" title="' . $reason . '"><i class="fas fa-stop-circle"></i> Đã dừng</span>';
                                                            break;
                                                        case 'Đã bán':
                                                            echo '<span class="text-secondary"><i class="fas fa-check-circle"></i> Đã bán</span>';
                                                            break;
                                                        default:
                                                            echo '<span class="text-muted">Không xác định</span>';
                                                    }
                                                    ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal dừng bán sản phẩm -->
<div class="modal fade" id="stopSellingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dừng bán sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn dừng bán sản phẩm:</p>
                <p class="fw-bold text-primary" id="stopProductName"></p>
                <div class="mb-3">
                    <label for="stopReason" class="form-label">Lý do dừng bán:</label>
                    <textarea class="form-control" id="stopReason" rows="3" placeholder="Nhập lý do dừng bán..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmStopSelling">Xác nhận dừng bán</button>
            </div>
        </div>
    </div>
</div>

<script src="/baitaplon/public/js/product_static_js.js"></script>
