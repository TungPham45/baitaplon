<?php
// app/views/Page/profile.php

$u = isset($data['profile']) ? $data['profile'] : null;
$products = isset($data['products']) ? $data['products'] : [];
$reviews = isset($data['reviews']) ? $data['reviews'] : [];
$isOwner = isset($data['isOwner']) ? $data['isOwner'] : false;
$loggedInId = isset($data['user_id']) ? $data['user_id'] : '';

// Xử lý avatar
$avatar = (!empty($u['avatar'])) ? "/baitaplon/" . $u['avatar'] : "https://via.placeholder.com/150?text=User";

if (!$u) {
    echo '<div class="alert alert-warning m-4">Người dùng không tồn tại.</div>';
    return;
}

// === TÍNH TOÁN RATING ===
$avgRating = 0;
$totalReviews = count($reviews);
if ($totalReviews > 0) {
    $sumRating = 0;
    foreach ($reviews as $rv) $sumRating += $rv['rating'];
    $avgRating = round($sumRating / $totalReviews, 1);
} else {
    $avgRating = 0; // Mặc định 0 nếu chưa có ai đánh giá
}
?>

<style>
    /* CSS CHO PROFILE CARD ĐẸP HƠN */
    .profile-card {
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e0e0e0;
        min-height: 520px; /* [MỚI] Tăng chiều cao tối thiểu để bằng với card bên phải */
        display: flex;
        flex-direction: column;
    }
    
    .avatar-container {
        position: relative;
        margin-top: -60px; /* Đẩy lên cao hơn chút */
        margin-bottom: 20px; /* Tăng khoảng cách dưới avatar */
    }
    
    .profile-avatar {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border: 5px solid #fff; /* Viền dày hơn cho nổi */
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Sao đánh giá */
    .rating-stars {
        font-size: 1.6rem;
        color: #ffc107;
        letter-spacing: 3px;
        margin-bottom: 5px;
    }
    
    .rating-score {
        font-weight: 800;
        font-size: 1.2rem;
        color: #333;
    }

    /* Thông tin liên hệ */
    .info-section {
        flex-grow: 1; /* Đẩy nút chỉnh sửa xuống đáy nếu cần */
        padding: 0 20px;
        margin-top: 15px;
    }

    .info-row {
        display: flex;
        align-items: center;
        gap: 15px; /* Tăng khoảng cách giữa icon và chữ */
        margin-bottom: 12px; /* Tăng khoảng cách giữa các dòng */
        color: #555;
        font-size: 1.05rem;
    }
    
    .info-row i {
        color: #0d6efd;
        width: 24px;
        text-align: center;
        font-size: 1.2rem;
    }

    /* Tab navigation bên phải (Giữ nguyên) */
    .nav-tabs .nav-link { color: #666; font-weight: 600; border: none; padding: 12px 20px; }
    .nav-tabs .nav-link.active { color: #0d6efd; border-bottom: 2px solid #0d6efd; background: transparent; }
</style>

<div class="container py-5" style="background-color: #f4f6f9; min-height: 100vh;">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="profile-card shadow-sm h-100"> <div style="height: 100px; background: linear-gradient(135deg, #0d6efd, #0dcaf0);"></div>
                
                <div class="card-body text-center pt-0 d-flex flex-column">
                    <div class="avatar-container">
                        <img src="<?php echo htmlspecialchars($avatar); ?>" class="rounded-circle profile-avatar" alt="Avatar">
                    </div>
                        <br>
                    <h3 class="fw-bold mb-2"><?php echo htmlspecialchars($u['hoten']); ?></h3>
                    
                    <div class="mb-4"> <div class="rating-stars">
                            <?php 
                            $fullStars = floor($avgRating);
                            $halfStar = ($avgRating - $fullStars) >= 0.5;
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $fullStars) echo '<i class="bi bi-star-fill"></i>';
                                elseif ($halfStar && $i == $fullStars + 1) echo '<i class="bi bi-star-half"></i>';
                                else echo '<i class="bi bi-star"></i>';
                            }
                            ?>
                        </div>
                        <div class="text-muted small">
                            <span class="rating-score"><?php echo $avgRating; ?></span> / 5.0 
                            <span class="mx-2">•</span> 
                            (Dựa trên <?php echo $totalReviews; ?> đánh giá)
                        </div>
                    </div>

                    <hr class="my-2 opacity-25 w-75 mx-auto">

                    <div class="info-section text-start">
                        <div class="info-row">
                            <i class="bi bi-telephone"></i>
                            <span><?php echo htmlspecialchars($u['sdt']); ?></span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-geo-alt"></i>
                            <span><?php echo htmlspecialchars($u['diachi']); ?></span>
                        </div>
                        
                        <div class="mt-4"> <strong class="d-block mb-2 text-dark">
                                <i class="bi bi-card-text me-2 text-primary"></i>Giới thiệu:
                            </strong>
                            <p class="text-muted small ps-1" style="line-height: 1.8;"> <?php echo !empty($u['gioithieu']) ? nl2br(htmlspecialchars($u['gioithieu'])) : "Người dùng này chưa viết giới thiệu."; ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($isOwner): ?>
                        <div class="mt-auto pt-4 px-3 pb-3">
                            <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa hồ sơ
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="pb-4"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom pt-3">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">
                                <i class="bi bi-grid-3x3-gap me-1"></i> Tin đăng (<?php echo count($products); ?>)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                                <i class="bi bi-chat-square-text me-1"></i> Đánh giá (<?php echo count($reviews); ?>)
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4" style="min-height: 400px; background: #fff;">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="products">
                            <?php if (empty($products)): ?>
                                <div class="text-center py-5">
                                    <div class="mb-3 text-muted display-1"><i class="bi bi-box-seam"></i></div>
                                    <p class="text-muted">Chưa có tin đăng nào.</p>
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach ($products as $p): ?>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 hover-shadow">
                                            <?php
                                            $img = isset($p['anh_hienthi']) ? "/baitaplon/" . $p['anh_hienthi'] : 'https://via.placeholder.com/300';
                                            $detailLink = "/baitaplon/Home/detail_Sanpham/" . $p['id_sanpham'] . ($loggedInId ? "/".urlencode($loggedInId) : "");
                                            ?>
                                            <div style="height: 180px; overflow: hidden; border-radius: 8px 8px 0 0;">
                                                <img src="<?= htmlspecialchars($img) ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s;">
                                            </div>
                                            <div class="card-body">
                                                <h6 class="card-title text-truncate mb-1 fw-bold"><?php echo htmlspecialchars($p['ten_sanpham']); ?></h6>
                                                <div class="text-danger fw-bold"><?php echo number_format($p['gia']); ?> đ</div>
                                                <small class="text-muted"><?php echo htmlspecialchars($p['ngaydang']); ?></small>
                                                <a href="<?php echo $detailLink; ?>" class="stretched-link"></a>
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
                                    <div class="mb-3 text-muted display-1"><i class="bi bi-star"></i></div>
                                    <p class="text-muted">Chưa có đánh giá nào.</p>
                                </div>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($reviews as $rv): ?>
                                    <div class="p-3 border rounded bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="<?php echo !empty($rv['reviewer_avatar']) ? "/baitaplon/".$rv['reviewer_avatar'] : "https://via.placeholder.com/40"; ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                            <div>
                                                <div class="fw-bold"><?php echo htmlspecialchars($rv['reviewer_name']); ?></div>
                                                <div class="text-warning small">
                                                    <?php for($i=1; $i<=5; $i++) echo ($i <= $rv['rating']) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; ?>
                                                </div>
                                            </div>
                                            <small class="ms-auto text-muted"><?php echo date('d/m/Y', strtotime($rv['created_at'])); ?></small>
                                        </div>
                                        
                                        <?php if(isset($rv['is_transacted']) && $rv['is_transacted']): ?>
                                            <div class="mb-2"><span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-shield-check"></i> Đã giao dịch</span></div>
                                        <?php endif; ?>

                                        <p class="mb-2 text-dark"><?php echo nl2br(htmlspecialchars($rv['comment'])); ?></p>
                                        
                                        <?php if(!empty($rv['images'])): ?>
                                            <div class="d-flex gap-2">
                                                <?php foreach($rv['images'] as $img): ?>
                                                    <img src="/baitaplon/public/<?php echo htmlspecialchars($img); ?>" class="rounded border" width="60" height="60" style="object-fit: cover;">
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($u['id_user']); ?>">

                    <div class="mb-3 text-center">
                        <img src="<?php echo htmlspecialchars($avatar); ?>" class="rounded-circle border mb-2" width="100" height="100" style="object-fit: cover;">
                        <br>
                        <label for="avatar_file" class="form-label small text-primary" style="cursor: pointer;">Thay đổi ảnh đại diện</label>
                        <input type="file" class="form-control form-control-sm" name="avatar_file" id="avatar_file" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" name="hoten" value="<?php echo htmlspecialchars($u['hoten']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" name="sdt" value="<?php echo htmlspecialchars($u['sdt']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" name="diachi" value="<?php echo htmlspecialchars($u['diachi']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giới thiệu bản thân</label>
                        <textarea class="form-control" name="gioithieu" rows="3"><?php echo htmlspecialchars($u['gioithieu']); ?></textarea>
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
<?php endif; ?>