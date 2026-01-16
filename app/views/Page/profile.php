<?php
// app/views/Page/profile.php

$u = isset($data['profile']) ? $data['profile'] : null;
$rawProducts = isset($data['products']) ? $data['products'] : [];
$reviews = isset($data['reviews']) ? $data['reviews'] : [];
$isOwner = isset($data['isOwner']) ? $data['isOwner'] : false;
$loggedInId = isset($data['user_id']) ? $data['user_id'] : '';

// Xử lý avatar - chỉ lấy tên file từ database nếu có đường dẫn đầy đủ
$avatarFilename = !empty($u['avatar']) ? basename($u['avatar']) : 'default.png';
$avatar = "/baitaplon/public/uploads/avatars/" . $avatarFilename;

if (!$u) {
    echo '<div class="alert alert-warning m-4">Người dùng không tồn tại.</div>';
    return;
}

// Xử lý danh sách sản phẩm hiển thị ban đầu (PHP)
$products = [];
if ($isOwner) {
    $products = $rawProducts;
} else {
    foreach ($rawProducts as $p) {
        // Chỉ lấy sản phẩm đang hiển thị cho khách
        if (isset($p['trangthai']) && $p['trangthai'] === 'Đã duyệt') {
            $products[] = $p;
        }
    }
}

// Tính rating
$avgRating = 0;
$totalReviews = count($reviews);
if ($totalReviews > 0) {
    $sumRating = 0;
    foreach ($reviews as $rv) $sumRating += $rv['rating'];
    $avgRating = round($sumRating / $totalReviews, 1);
}
?>

<link rel="stylesheet" href="/baitaplon/public/css/profile_css.css">

<div class="container py-5" style="background-color: #f4f6f9; min-height: 100vh;">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="profile-card shadow-sm h-100">
                <div class="cover-photo"></div>
                
                <div class="card-body pt-0 d-flex flex-column">
                    <div class="avatar-container">
                        <img src="<?= htmlspecialchars($avatar) ?>" class="profile-avatar" alt="Avatar">
                    </div>
                    
                    <div class="text-center mb-3">
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($u['hoten']) ?></h4>

                        <!-- Thống kê sản phẩm -->
                        <div class="text-muted small mb-2">
                            <span class="me-3">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                Đã bán: <?= $soldCount ?? 0 ?>
                            </span>
                            <span>
                                <i class="bi bi-box-seam text-primary"></i>
                                Tổng sản phẩm: <?= $totalActiveProducts ?? 0 ?>
                            </span>
                        </div>

                        <div class="text-warning small mb-1">
                            <?php
                            $fullStars = floor($avgRating);
                            $halfStar = ($avgRating - $fullStars) >= 0.5;
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $fullStars) echo '<i class="bi bi-star-fill"></i>';
                                elseif ($halfStar && $i == $fullStars + 1) echo '<i class="bi bi-star-half"></i>';
                                else echo '<i class="bi bi-star"></i>';
                            }
                            ?>
                            <span class="text-muted ms-1">(<?= $totalReviews ?> đánh giá)</span>
                        </div>
                    </div>

                    <hr class="my-2 opacity-25 w-75 mx-auto">

                    <div class="info-section">
                        <div class="info-row">
                            <i class="bi bi-telephone"></i>
                            <span><?= htmlspecialchars($u['sdt']) ?></span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-geo-alt"></i>
                            <span><?= htmlspecialchars($u['diachi']) ?></span>
                        </div>
                        
                        <div class="mt-3">
                            <strong class="d-block mb-1 text-dark small text-uppercase">
                                <i class="bi bi-card-text me-1 text-primary"></i>Giới thiệu:
                            </strong>
                            <p class="text-muted small ps-1 mb-0" style="line-height: 1.6;">
                                <?= !empty($u['gioithieu']) ? nl2br(htmlspecialchars($u['gioithieu'])) : "Chưa có giới thiệu." ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($isOwner): ?>
                        <div class="mt-3 px-3">
                            <button type="button" class="btn btn-outline-primary w-100 rounded-pill fw-bold py-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa hồ sơ
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom pt-3">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">
                                <i class="bi bi-grid-3x3-gap me-1"></i> Tin đăng (<?= count($products) ?>)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                                <i class="bi bi-chat-square-text me-1"></i> Đánh giá (<?= count($reviews) ?>)
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4" style="min-height: 400px; background: #fff;">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="products">
                            
                            <?php if ($isOwner): ?>
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 p-3 bg-light rounded border">
                                    
                                    <form action="" method="GET" class="d-flex align-items-center gap-2">
                                        <label class="fw-bold text-secondary small text-nowrap">
                                            <i class="bi bi-funnel-fill"></i> Lọc tin:
                                        </label>
                                        
                                        <?php
                                        // Lấy trạng thái hiện tại trên URL
                                        $curr = isset($_GET['trang_thai']) ? $_GET['trang_thai'] : '';
                                        ?>

                                        <select name="trang_thai" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                                            <option value="all" <?= ($curr == 'all') ? 'selected' : '' ?>>Tất cả</option>

                                            <option value="Đã duyệt" <?= ($curr == 'Đã duyệt') ? 'selected' : '' ?>>Đang bán</option>

                                            <option value="Đã bán" <?= ($curr == 'Đã bán') ? 'selected' : '' ?>>Đã bán</option>

                                            <option value="Chờ duyệt" <?= ($curr == 'Chờ duyệt') ? 'selected' : '' ?>>Chưa duyệt</option>
                                            
                                            <option value="Từ chối" <?= ($curr == 'Từ chối') ? 'selected' : '' ?>>Từ chối</option>
                                            
                                            <option value="Dừng bán" <?= ($curr == 'Dừng bán') ? 'selected' : '' ?>>Dừng bán</option>
                                        </select>
                                    </form>

                                    <button id="btnExportExcel" class="btn btn-success btn-sm fw-bold mt-2 mt-md-0">
                                        <i class="bi bi-file-earmark-spreadsheet-fill"></i> Xuất Excel
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if (empty($products)): ?>
                                <div class="text-center py-5">
                                    <div class="mb-3 text-muted display-4"><i class="bi bi-box-seam"></i></div>
                                    <p class="text-muted">Không tìm thấy tin đăng nào.</p>
                                </div>
                            <?php else: ?>
                                <div class="row g-3" id="productList">
                                    <?php foreach ($products as $p): ?>
                                        <?php
                                            // Logic trạng thái cho JS và Badge
                                            $jsStatus = 'hienthi'; 
                                            if ($p['trangthai'] == 'Đã bán') $jsStatus = 'daban';
                                            if ($p['trangthai'] == 'Chờ duyệt') $jsStatus = 'choduyet';
                                            if ($p['trangthai'] == 'Dừng bán') $jsStatus = 'dungban'; // Gán trạng thái dừng bán
                                            if ($p['trangthai'] == 'Từ chối') $jsStatus = 'tuchoi';
                                            
                                            $img = isset($p['anh_hienthi']) ? "/baitaplon/" . $p['anh_hienthi'] : 'https://via.placeholder.com/300';
                                            $detailLink = "/baitaplon/Home/detail_Sanpham/" . $p['id_sanpham'] . ($loggedInId ? "/".urlencode($loggedInId) : "");
                                        ?>
                                        
                                        <div class="col-sm-6 col-lg-4 product-item-wrapper">
                                            <div class="card h-100 shadow-sm border hover-shadow position-relative">
                                                
                                                <?php if($isOwner): ?>
                                                    <?php if($jsStatus == 'daban'): ?>
                                                        <span class="status-badge bg-sold">ĐÃ BÁN</span>
                                                    
                                                    <?php elseif($jsStatus == 'choduyet'): ?>
                                                        <span class="status-badge bg-pending">CHỜ DUYỆT</span>
                                                    
                                                    <?php elseif($jsStatus == 'tuchoi'): ?>
                                                        <span class="status-badge bg-danger">TỪ CHỐI</span>
                                                    
                                                    <?php elseif($jsStatus == 'dungban'): ?>
                                                        <span class="status-badge bg-danger text-uppercase text-truncate" style="max-width: 90%;">
                                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                                            <?= htmlspecialchars($p['mota']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <div style="height: 160px; overflow: hidden; border-radius: 6px 6px 0 0;">
                                                    <img src="<?= htmlspecialchars($img) ?>" class="w-100 h-100" style="object-fit: cover;">
                                                </div>
                                                <div class="card-body p-3">
                                                    <h6 class="card-title text-truncate mb-1 fw-bold" title="<?= htmlspecialchars($p['ten_sanpham']) ?>">
                                                        <?= htmlspecialchars($p['ten_sanpham']) ?>
                                                    </h6>
                                                    <div class="text-danger fw-bold mb-1"><?= number_format($p['gia']) ?> đ</div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted" style="font-size: 0.75rem;">
                                                            <?= date('d/m/Y', strtotime($p['ngaydang'])) ?>
                                                        </small>
                                                        <a href="<?= $detailLink ?>" class="btn btn-sm btn-outline-primary py-0" style="font-size: 0.8rem;">Xem</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="reviews">
                             <?php if (empty($reviews)): ?>
                                <div class="text-center py-5">
                                    <div class="mb-3 text-muted display-4"><i class="bi bi-chat-square-text"></i></div>
                                    <p class="text-muted">Chưa có đánh giá nào.</p>
                                </div>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($reviews as $rv): ?>
                                    <div class="p-3 border rounded bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="<?= !empty($rv['reviewer_avatar']) ? "/baitaplon/public/uploads/avatars/".basename($rv['reviewer_avatar']) : "/baitaplon/public/uploads/avatars/default.png" ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($rv['reviewer_name']) ?></div>
                                                <div class="text-warning small">
                                                    <?php for($i=1; $i<=5; $i++) echo ($i <= $rv['rating']) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; ?>
                                                </div>
                                            </div>
                                            <small class="ms-auto text-muted"><?= date('d/m/Y', strtotime($rv['created_at'])) ?></small>
                                        </div>
                                        <p class="mb-2 text-dark"><?= nl2br(htmlspecialchars($rv['comment'])) ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($isOwner): ?>
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="/baitaplon/User/Update" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Chỉnh sửa thông tin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_user" value="<?= htmlspecialchars($u['id_user']) ?>">
                        <div class="mb-3 text-center">
                            <img src="<?= htmlspecialchars($avatar) ?>" class="rounded-circle border mb-2" width="100" height="100" style="object-fit: cover;">
                            <br>
                            <label for="avatar_file" class="form-label small text-primary pointer">Thay đổi ảnh</label>
                            <input type="file" class="form-control form-control-sm" name="avatar_file" id="avatar_file" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" name="hoten" value="<?= htmlspecialchars($u['hoten']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SĐT</label>
                            <input type="text" class="form-control" name="sdt" value="<?= htmlspecialchars($u['sdt']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" name="diachi" value="<?= htmlspecialchars($u['diachi']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giới thiệu</label>
                            <textarea class="form-control" name="gioithieu" rows="3"><?= htmlspecialchars($u['gioithieu']) ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="/baitaplon/public/js/xuatSanPham.js"></script>
<?php endif; ?>