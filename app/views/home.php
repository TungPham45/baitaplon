<?php
// app/views/home.php
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Trang chủ - DealNow</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/baitaplon/Public/css/style.css?v=<?php echo time(); ?>">
    
    <style>
        body { background: #f1f5f9; font-family: system-ui, -apple-system, sans-serif; color: #0f172a; }
        .navbar { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 10px 0; margin-bottom: 20px; }
        .navbar-brand { font-weight: 700; color: #f59e0b !important; font-size: 1.5rem; }
        
        /* Form tìm kiếm */
        .search-container { display: flex; align-items: center; background: #f8fafc; border: 1px solid #dfe6e9; border-radius: 50px; padding: 5px 15px; width: 100%; max-width: 700px; position: relative; }
        .search-input { border: none; background: transparent; outline: none; width: 100%; padding: 0 10px; }
        
        /* [QUAN TRỌNG] CSS cho danh sách gợi ý địa chỉ */
        .address-suggestions {
            position: absolute; top: 100%; left: 0; right: 0;
            background: #fff; border: 1px solid #ddd; border-radius: 8px;
            max-height: 300px; overflow-y: auto; z-index: 1000;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: none; margin-top: 5px;
        }
        .address-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f1f1f1; display: flex; align-items: center; font-size: 14px; }
        .address-item:hover { background-color: #f8f9fa; color: #f59e0b; }
        .address-item:last-child { border-bottom: none; }
        .address-item i { margin-right: 10px; color: #999; }

        /* Các CSS khác giữ nguyên */
        .dropdown-item-parent { position: relative; }
        .dropdown-item-parent .submenu { display: none; position: absolute; left: 100%; top: 0; margin-top: -1px; background: #fff; border: 1px solid #ddd; border-radius: 0.25rem; min-width: 200px; list-style: none; padding: 5px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .dropdown-item-parent:hover .submenu { display: block; }
        .dropdown-item-parent:hover > .dropdown-item { background-color: #f8f9fa; }
        .submenu li a { padding: 8px 16px; display: block; text-decoration: none; color: #212529; }
        .submenu li a:hover { background-color: #e9ecef; }
        
        .upload-grid { display: flex; flex-wrap: wrap; gap: 10px; }
        .upload-box-btn { width: 100px; height: 100px; border: 2px dashed #ddd; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; background: #f8f9fa; position: relative; }
        .image-item { position: relative; display: inline-block; }
        .btn-remove-img { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; z-index: 10; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm">
    <div class="container">
        <?php
        $currentUserId = isset($data['user_id']) ? $data['user_id'] : '';
        $homeLink = "/baitaplon/Home" . (!empty($currentUserId) ? "/index/" . urlencode($currentUserId) : "");
        ?>
        <a class="navbar-brand me-4" href="<?php echo $homeLink; ?>"><i class="bi bi-shop"></i> DealNow</a>

        <div class="mx-auto flex-grow-1 px-3 d-flex justify-content-center">
            <form class="search-container" method="GET" action="/baitaplon/Home/index" id="searchForm">
                <?php if(!empty($currentUserId)): ?>
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($currentUserId); ?>">
                <?php endif; ?>
                <?php
                $keyword  = isset($data['keyword']) ? $data['keyword'] : '';
                $category = isset($data['category']) ? $data['category'] : '';
                $categoryTree = isset($data['categoryTree']) ? $data['categoryTree'] : [];
                $currentCatName = isset($data['currentCatName']) ? $data['currentCatName'] : 'Danh mục';
                $address  = isset($data['address']) ? $data['address'] : '';
                ?>
                <input type="hidden" name="danhmuc" id="inputDanhmuc" value="<?php echo htmlspecialchars($category); ?>">

                <div class="dropdown">
                    <button class="btn btn-sm fw-bold text-secondary border-0 dropdown-toggle text-truncate" type="button" data-bs-toggle="dropdown" style="max-width: 150px;">
                        <i class="bi bi-list"></i> <span id="catDisplay"><?php echo htmlspecialchars($currentCatName); ?></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="selectCategory('', 'Tất cả danh mục'); return false;">Tất cả danh mục</a></li>
                        <li><hr class="dropdown-divider"></li>

                        <?php if (!empty($categoryTree)): foreach ($categoryTree as $parent): ?>
                            <?php if (!empty($parent['children'])): ?>
                                <li class="dropdown-item-parent">
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" onclick="selectCategory('<?php echo $parent['id_danhmuc']; ?>', '<?php echo $parent['ten_danhmuc']; ?>'); return false;">
                                        <?php echo htmlspecialchars($parent['ten_danhmuc']); ?> <i class="bi bi-chevron-right small"></i>
                                    </a>
                                    <ul class="submenu shadow">
                                        <?php foreach ($parent['children'] as $child): ?>
                                            <li><a class="dropdown-item" href="#" onclick="selectCategory('<?php echo $child['id_danhmuc']; ?>', '<?php echo $child['ten_danhmuc']; ?>'); return false;"><?php echo  htmlspecialchars($child['ten_danhmuc']); ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="#" onclick="selectCategory('<?php echo $parent['id_danhmuc']; ?>', '<?php echo $parent['ten_danhmuc']; ?>'); return false;"><?php echo htmlspecialchars($parent['ten_danhmuc']); ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; endif; ?>
                        
                    </ul>
                </div>
                <div class="vr mx-2"></div>

                <input class="search-input" type="text" name="q" placeholder="Tìm sản phẩm..." value="<?php echo htmlspecialchars($keyword); ?>" style="flex: 1;">
                <div class="vr mx-2"></div>

                <div style="position: relative; width: 180px;">
                    <input class="search-input" type="text" id="nav-address-input" name="diachi" 
                           placeholder="Toàn quốc" autocomplete="off" 
                           value="<?php echo htmlspecialchars($address); ?>">
                    <div id="nav-address-list" class="address-suggestions"></div>
                </div>

                <button class="btn btn-warning rounded-circle ms-2" type="submit" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-search text-white"></i>
                </button>
            </form>
        </div>

        <div class="d-flex align-items-center gap-3">
            <?php if (isset($data['isLoggedIn']) && $data['isLoggedIn']): ?>
                <button class="btn btn-warning fw-bold text-dark btn-sm" data-bs-toggle="modal" data-bs-target="#postModal"><i class="bi bi-plus-lg"></i> Đăng tin</button>
                <a href="/baitaplon/Chat/index/0/<?php echo $currentUserId; ?>" class="text-secondary fs-5"><i class="bi bi-chat-dots-fill"></i></a>
                <div class="dropdown">
                    <a href="#" class="text-secondary fs-5" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i></a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        
                        <?php 
                        // Lấy role từ Session và xử lý khoảng trắng thừa (nếu có)
                        $role = isset($_SESSION['role']) ? trim($_SESSION['role']) : '';
                        if ($role === 'Quản lý'): 
                        ?>
                            <li>
                                <a class="dropdown-item fw-bold text-primary" href="/baitaplon/Admin/dashboard">
                                    <i class="bi bi-speedometer2"></i> Quản lý Web
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item" href="/baitaplon/User/Profile/<?php echo urlencode($currentUserId); ?>">Trang cá nhân</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Đổi mật khẩu</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/baitaplon/Home?logout=1">Đăng xuất</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="/baitaplon/Auth/login" class="btn btn-outline-primary btn-sm fw-bold">Đăng nhập</a>
                <a href="/baitaplon/Auth/registerStep1" class="btn btn-primary btn-sm fw-bold">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container my-4" style="min-height: 60vh;">
    <div class="row">
        <div class="col-12">
            <?php
                if (isset($data["page"]) && $data["page"]) {
                    $pageFile = __DIR__ . "/Page/" . $data["page"] . ".php";
                    if (file_exists($pageFile)) require_once $pageFile;
                    else echo '<div class="alert alert-danger">Không tìm thấy file: ' . htmlspecialchars($data["page"]) . '</div>';
                } else echo '<div class="alert alert-warning">Chưa chọn trang!</div>';
            ?>
        </div>
    </div>
</div>

<footer class="bg-light border-top py-5 mt-5">
    <div class="container">
        <!-- Policies Section -->
        <div class="row mb-5">
            <div class="col-12 d-flex justify-content-center flex-wrap gap-4">
                <a href="#" class="text-decoration-none text-secondary fw-600 small">
                    <i class="bi bi-shield-lock"></i> CHÍNH SÁCH BẢO MẬT
                </a>
                <a href="#" class="text-decoration-none text-secondary fw-600 small">
                    <i class="bi bi-file-text"></i> QUY CHẾ HOẠT ĐỘNG
                </a>
            </div>
        </div>

        <!-- Company Info -->
        <div class="row text-center text-secondary small">
            <div class="col-12">
                <p class="mb-2"><strong>Công ty TNHH DealNow </strong></p>
                <p class="mb-2">Địa chỉ: Số 54 Triều Khúc, phường Thanh Liệt, Hà Nội, Việt Nam</p>
                <p class="mb-2">Chăm sóc khách hàng: Gọi tổng đài DealNow (miễn phí) hoặc Trò chuyện với chúng tôi ngay trên trang Trung tâm trợ giúp</p>
                <p class="mb-2">Chủ Trách Nhiệm Quản Lý Nội Dung: AE Tôi </p>
                <p class="mb-2">Mã số doanh nghiệp: 0123456JQK do Sở Kế hoạch và Đầu tư TP Hà Nội cấp lần đầu ngày 30/02/2025</p>
                <p class="mb-0">&copy; 2025 - Bản quyền thuộc về Công ty TNHH DealNow</p>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="postModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title fw-bold">Đăng tin Bán Hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="postForm" enctype="multipart/form-data">
                    <input type="hidden" name="id_user_posted" value="<?php echo isset($currentUserId) ? $currentUserId : ''; ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Hình ảnh <span class="text-danger">*</span></label>
                        <div class="upload-grid" id="uploadGrid">
                            <div class="upload-box-btn" id="addImgBtn">
                                <span class="fs-1 text-secondary">+</span>
                                <input type="file" id="imageInput" multiple accept="image/*" style="opacity: 0; position: absolute; inset:0; cursor: pointer;">
                            </div>
                        </div>
                        <div class="text-danger small mt-1" id="err-images" style="display:none">Vui lòng chọn ít nhất 1 ảnh.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                            <select id="catLevel1" name="catLevel1" class="form-select"><option value="">-- Chọn danh mục --</option></select>
                            <div class="text-danger small" id="err-catLevel1" style="display:none">Chọn danh mục.</div>
                        </div>
                        <div class="col-md-6 mb-3" id="group-catLevel2" style="display:none;">
                            <label class="form-label fw-bold">Loại sản phẩm <span class="text-danger">*</span></label>
                            <select id="catLevel2" name="catLevel2" class="form-select"><option value="">-- Chọn loại --</option></select>
                            <div class="text-danger small" id="err-catLevel2" style="display:none">Chọn loại sản phẩm.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="VD: iPhone 13 Pro Max...">
                        <div class="text-danger small" id="err-title" style="display:none">Nhập tiêu đề.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" id="price" name="price" class="form-control" placeholder="VD: 15000000">
                        <div class="text-danger small" id="err-price" style="display:none">Nhập giá bán.</div>
                    </div>

                    <div id="dynamic-attributes"></div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả chi tiết <span class="text-danger">*</span></label>
                        <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                        <div class="text-danger small" id="err-description" style="display:none">Mô tả quá ngắn.</div>
                    </div>

                    <div class="mb-3" style="position: relative;">
                        <label class="form-label fw-bold">Khu vực bán <span class="text-danger">*</span></label>
                        <input type="text" id="post-address-input" name="address" class="form-control" 
                               placeholder="Nhập Huyện hoặc Tỉnh (VD: Cầu Giấy)..." autocomplete="off">
                        <div id="post-address-list" class="address-suggestions"></div>
                        <div class="text-danger small" id="err-address" style="display:none">Vui lòng nhập địa chỉ.</div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="postForm" class="btn btn-warning fw-bold">Đăng tin ngay</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Đổi mật khẩu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <input type="hidden" name="user_id" value="<?php echo isset($currentUserId) ? $currentUserId : ''; ?>">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu cũ</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                        <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="alert alert-danger d-none" id="cp-error-msg"></div>
                    <div class="alert alert-success d-none" id="cp-success-msg"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnChangePassword">Đổi mật khẩu</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const changePwdModal = document.getElementById('changePasswordModal');
    const errorMsg = document.getElementById('cp-error-msg');
    const successMsg = document.getElementById('cp-success-msg');
    
    changePwdModal.addEventListener('show.bs.modal', function() {
        errorMsg.classList.add('d-none');
        successMsg.classList.add('d-none');
        document.getElementById('changePasswordForm').reset();
    });
    
    document.getElementById('btnChangePassword').addEventListener('click', function() {
        errorMsg.classList.add('d-none');
        successMsg.classList.add('d-none');
        
        const formData = new FormData(document.getElementById('changePasswordForm'));
        
        fetch('/baitaplon/User/changePasswordAjax', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successMsg.textContent = data.message;
                successMsg.classList.remove('d-none');
                setTimeout(function() {
                    const modal = bootstrap.Modal.getInstance(changePwdModal);
                    modal.hide();
                }, 1500);
            } else {
                errorMsg.textContent = data.message;
                errorMsg.classList.remove('d-none');
            }
        })
        .catch(error => {
            errorMsg.textContent = 'Có lỗi xảy ra. Vui lòng thử lại!';
            errorMsg.classList.remove('d-none');
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function selectCategory(id, name) {
        document.getElementById('inputDanhmuc').value = id;
        document.getElementById('catDisplay').innerText = name;
        document.getElementById('searchForm').submit();
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('open_post_modal') === '1') {
        const modal = new bootstrap.Modal(document.getElementById('postModal'));
        modal.show();
        const url = new URL(window.location); url.searchParams.delete('open_post_modal'); window.history.replaceState({}, '', url);
    }
});
</script>
<script src="/baitaplon/Public/js/home_js.js"></script>

</body>
</html>