<?php
// app/views/Page/detail_sanpham.php


$p = isset($data['product']) ? $data['product'] : null;
$imgs = isset($data['productImages']) ? $data['productImages'] : [];
$attrs = isset($data['productAttributes']) ? $data['productAttributes'] : [];
$viewerId = isset($data['user_id']) ? $data['user_id'] : '';
$userRole = isset($_SESSION['role']) ? trim($_SESSION['role']) : '';

// 2. Xử lý ảnh
$avatarFilename = !empty($p['avatar_user']) ? basename($p['avatar_user']) : 'default.png';
$mainAvatar = "/baitaplon/public/uploads/avatars/" . $avatarFilename;
if (!empty($imgs) && isset($imgs[0]['url_anh'])) {
    $mainImg = "/baitaplon/" . $imgs[0]['url_anh'];
} else {
    $mainImg = !empty($p['anh_dai_dien']) ? "/baitaplon/" . $p['anh_dai_dien'] : 'https://via.placeholder.com/500';
}

// 3. Kiểm tra chỉ Chính chủ (không cho Admin)
$isOwner = false;
if ($p && !empty($viewerId)) {
    if ($viewerId == $p['id_user']) {
        $isOwner = true;
    }
}

// 4. Kiểm tra nếu sản phẩm không tồn tại hoặc bị dừng bán
if (!$p) {
    echo '<div class="alert alert-danger container mt-5">Sản phẩm không tồn tại! <a href="/baitaplon/Home">Về trang chủ</a></div>';
    return;
}

// 5. Kiểm tra trạng thái "Dừng bán"
if ($p['trangthai'] == 'Dừng bán') {
    echo '<script>
        alert("Sản phẩm này đã dừng bán.");
        window.history.back();
    </script>';
    return;
}
?>

<div class="container mt-5 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/baitaplon/Home">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/baitaplon/Home?danhmuc=<?= $p['id_danhmuc'] ?>"><?= htmlspecialchars($p['ten_danhmuc']) ?></a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($p['ten_sanpham']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0">
                <div class="main-image-box mb-3 text-center border rounded p-2" style="background: #f8f9fa;">
                   <img id="mainImage" src="<?= htmlspecialchars($mainImg) ?>" class="img-fluid" style="max-height: 400px; object-fit: contain;">
                </div>
                <div class="d-flex overflow-auto gap-2">
                    <?php foreach ($imgs as $index => $img): ?>
                        <img src="/baitaplon/<?= htmlspecialchars($img['url_anh']) ?>" 
                             class="img-thumbnail thumb-img <?= ($index === 0) ? 'active' : '' ?>" 
                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" 
                             onclick="changeImage(this)">
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2 class="fw-bold mb-3"><?= htmlspecialchars($p['ten_sanpham']) ?></h2>
            
            <?php if ($p['trangthai'] == 'Đã bán'): ?>
                <div class="alert alert-secondary fw-bold text-center">
                    <i class="bi bi-bag-check-fill"></i> SẢN PHẨM ĐÃ BÁN
                </div>
            <?php elseif ($p['trangthai'] == 'Chờ duyệt'): ?>
                <div class="alert alert-warning fw-bold text-center">
                    <i class="bi bi-hourglass-split"></i> TIN ĐANG CHỜ DUYỆT
                </div>
            <?php elseif ($p['trangthai'] == 'Từ chối'): ?>
                <div class="alert alert-danger fw-bold text-center">
                    <i class="bi bi-x-circle"></i> TIN BỊ TỪ CHỐI
                    <?php if (!empty($p['lydotuchoi'])): ?>
                        <br><small class="text-muted">Lý do: <?= htmlspecialchars($p['lydotuchoi']) ?></small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <p class="text-muted">
                <i class="bi bi-clock"></i> Ngày đăng: <?= date('d/m/Y', strtotime($p['ngaydang'])) ?>
                <span class="mx-2">|</span>
                <i class="bi bi-eye"></i> Lượt xem: <?= $p['luot_xem'] ?>
            </p>

            <h1 class="text-danger fw-bold mb-4"><?= number_format($p['gia'], 0, ',', '.') ?> đ</h1>

            <div class="card bg-light mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?= htmlspecialchars($mainAvatar) ?>" class="rounded-circle me-3" width="60" height="60">
                        <div>
                            <h6 class="mb-0 fw-bold text-uppercase">
                                Người bán: <a href="/baitaplon/User/Profile/<?= $p['id_user'] ?>" class="text-decoration-none text-dark hover-name"><?= htmlspecialchars($p['hoten']) ?></a>
                            </h6>
                            <small class="text-muted"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($p['khu_vuc_ban']) ?></small>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <?php if ($isOwner): ?>
                            <div class="p-2 border rounded bg-white text-center mb-2">
                                <small class="text-primary fw-bold">QUẢN LÝ TIN ĐĂNG NÀY</small>
                            </div>

                            <button type="button" class="btn btn-warning fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#editProductModal">
                                <i class="bi bi-pencil-square"></i> Sửa bài đăng
                            </button>

                            <?php if ($p['trangthai'] !== 'Đã bán'&& $p['trangthai'] !== 'Chờ duyệt'): ?>
                                <a href="/baitaplon/PostController/markSold/<?= $p['id_sanpham'] ?>" 
                                   class="btn btn-secondary fw-bold"
                                   onclick="return confirm('Xác nhận đánh dấu ĐÃ BÁN? Tin sẽ bị ẩn khỏi danh sách tìm kiếm.');">
                                    <i class="bi bi-check-circle-fill"></i> Đánh dấu Đã bán
                                </a>
                            <?php endif; ?>

                            <a href="/baitaplon/PostController/delete/<?= $p['id_sanpham'] ?>" 
                               class="btn btn-danger fw-bold"
                               onclick="return confirm('CẢNH BÁO: Bạn có chắc muốn xóa vĩnh viễn tin này không?');">
                                <i class="bi bi-trash-fill"></i> Xóa bài viết
                            </a>

                        <?php else: ?>
                            <a href="tel:<?= htmlspecialchars($p['sdt']) ?>" class="btn btn-success fw-bold">
                                <i class="bi bi-telephone-fill"></i> GỌI NGAY: <?= htmlspecialchars($p['sdt']) ?>
                            </a>
                            <?php if (!empty($data['isLoggedIn'])): ?>
                                <form action="/baitaplon/Chat/start/<?= $p['id_user'] ?>" method="POST" style="display: grid;">
                                    <input type="hidden" name="product_id_post" value="<?= $p['id_sanpham'] ?>">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-chat-dots-fill"></i> Chat với người bán
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="/baitaplon/Login" class="btn btn-outline-primary" onclick="return confirm('Bạn cần đăng nhập để chat. Chuyển đến trang đăng nhập?');">
                                    <i class="bi bi-chat-dots-fill"></i> Chat với người bán
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <strong><i class="bi bi-shield-check"></i> Lưu ý:</strong> Hãy kiểm tra kỹ sản phẩm trước khi giao dịch.
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold text-uppercase">Mô tả chi tiết</div>
                <div class="card-body">
                    <div class="content-desc"><?= nl2br(htmlspecialchars($p['mota'])) ?></div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($attrs)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold text-uppercase">Thông số</div>
                <div class="card-body">
                    <div class="attributes-list">
                        <?php foreach ($attrs as $attr): ?>
                            <div class="attribute-item">
                                <span class="attr-name"><?= htmlspecialchars($attr['ten_thuoctinh']) ?>:</span>
                                <span class="attr-value"><?= htmlspecialchars($attr['giatri']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if ($isOwner): ?>
    <script>
    // Dữ liệu danh mục hiện tại
    const CURRENT_PRODUCT = {
        catLevel1: "<?= isset($p['id_parent']) ? $p['id_parent'] : '' ?>", // Cần Model trả về id_parent
        catLevel2: "<?= $p['id_danhmuc'] ?>"
    };

    const OLD_ATTRIBUTES = <?= json_encode($attrs); ?>;
</script>

<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">CHỈNH SỬA TIN ĐĂNG</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/baitaplon/PostController/update/<?= $p['id_sanpham'] ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề tin <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($p['ten_sanpham']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($p['gia']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Danh mục chính</label>
                            <select id="edit_catLevel1" name="catLevel1" class="form-select">
                                <option value="">Đang tải...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Danh mục con</label>
                            <select id="edit_catLevel2" name="catLevel2" class="form-select">
                                <option value="">-- Vui lòng chọn danh mục chính --</option>
                            </select>
                        </div>
                    </div>

                    <div id="edit-dynamic-attributes" class="p-3 mb-3 bg-light border rounded">
                        <div class="text-muted small text-center">Vui lòng chọn danh mục để hiện thông số...</div>
                    </div>

                    <div class="mb-3" style="position: relative;">
                        <label class="form-label fw-bold">Khu vực bán</label>
                        <input type="text" id="edit-address-input" name="address" class="form-control" 
                               value="<?= htmlspecialchars($p['khu_vuc_ban']) ?>" autocomplete="off">
                        <div id="edit-address-list" class="address-suggestions"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($p['mota']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary fw-bold">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const editCat1 = document.getElementById('edit_catLevel1');
    const editCat2 = document.getElementById('edit_catLevel2');
    const dynamicDiv = document.getElementById('edit-dynamic-attributes');
    const apiUrl = '/baitaplon/CategoriesController'; 

    // --- HÀM 1: Load Danh Mục Cha ---
    async function loadParentCategories() {
        const res = await fetch(apiUrl + '/getParentCategories');
        const data = await res.json();
        
        editCat1.innerHTML = '<option value="">-- Chọn danh mục --</option>';
        if(!data.error) {
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id_danhmuc;
                opt.textContent = item.ten_danhmuc;
                // Tự động select danh mục cha cũ
                if (item.id_danhmuc == CURRENT_PRODUCT.catLevel1) {
                    opt.selected = true;
                }
                editCat1.appendChild(opt);
            });
            
            // Nếu đã có cha, load tiếp con
            if (CURRENT_PRODUCT.catLevel1) {
                loadSubCategories(CURRENT_PRODUCT.catLevel1, true); // true = lần đầu load
            }
        }
    }

    // --- HÀM 2: Load Danh Mục Con ---
    async function loadSubCategories(parentId, isFirstLoad = false) {
        editCat2.innerHTML = '<option value="">Đang tải...</option>';
        
        const res = await fetch(apiUrl + `/getSubCategories/${parentId}`);
        const data = await res.json();
        
        editCat2.innerHTML = '<option value="">-- Chọn danh mục con --</option>';
        if (data.length > 0) {
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id_danhmuc;
                opt.textContent = item.ten_danhmuc;
                // Tự động select danh mục con cũ
                if (isFirstLoad && item.id_danhmuc == CURRENT_PRODUCT.catLevel2) {
                    opt.selected = true;
                }
                editCat2.appendChild(opt);
            });

            // Nếu đây là lần load đầu tiên và đã chọn con -> Load luôn thuộc tính cũ
            if (isFirstLoad && CURRENT_PRODUCT.catLevel2) {
                loadAttributes(CURRENT_PRODUCT.catLevel2, true);
            }
        }
    }

    // --- HÀM 3: Load và Điền Thuộc Tính ---
    async function loadAttributes(catId, isFirstLoad = false) {
        dynamicDiv.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Đang tải thông số...</div>';
        
        const res = await fetch(apiUrl + `/getAttributes?id_danhmuc=${catId}`);
        const attributes = await res.json();
        
        dynamicDiv.innerHTML = '';
        if (attributes.length === 0) {
            dynamicDiv.innerHTML = '<div class="text-muted small">Danh mục này không có thông số đặc biệt.</div>';
            return;
        }

        attributes.forEach(attr => {
            const div = document.createElement('div');
            div.className = 'mb-3';
            
            const label = document.createElement('label');
            label.className = 'form-label fw-bold';
            label.textContent = attr.ten_thuoctinh;
            div.appendChild(label);
            
            // Tìm giá trị cũ của thuộc tính này (nếu có)
            let oldValue = '';
            let oldOptionId = '';
            
            if (isFirstLoad && OLD_ATTRIBUTES.length > 0) {
                // OLD_ATTRIBUTES trả về từ PHP có dạng: [{id_thuoctinh: "1", giatri: "...", id_option: "..."}]
                const found = OLD_ATTRIBUTES.find(a => a.id_thuoctinh == attr.id_thuoctinh);
                if (found) {
                    oldValue = found.giatri;   // Giá trị text (cho input)
                    oldOptionId = found.id_option; // ID option (cho select)
                }
            }

            let inputEl;
            if (attr.options && attr.options.length > 0) {
                // Loại Select Box
                inputEl = document.createElement('select');
                inputEl.className = 'form-select';
                inputEl.name = `thuoctinh[${attr.id_thuoctinh}]`; // Gửi dạng mảng
                inputEl.innerHTML = `<option value="">-- Chọn ${attr.ten_thuoctinh} --</option>`;
                
                attr.options.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.id_option;
                    option.textContent = opt.gia_tri_option;
                    
                    // Kiểm tra select giá trị cũ
                    if (isFirstLoad && opt.id_option == oldOptionId) {
                        option.selected = true;
                    }
                    inputEl.appendChild(option);
                });
            } else {
                // Loại Input Text
                inputEl = document.createElement('input');
                inputEl.className = 'form-control';
                inputEl.type = 'text';
                inputEl.name = `thuoctinh[${attr.id_thuoctinh}]`;
                inputEl.placeholder = `Nhập ${attr.ten_thuoctinh}...`;
                
                if (isFirstLoad) {
                    inputEl.value = oldValue;
                }
            }
            
            div.appendChild(inputEl);
            dynamicDiv.appendChild(div);
        });
    }

    // --- SỰ KIỆN ---
    // 1. Thay đổi cha -> load con
    editCat1.addEventListener('change', function() {
        if(this.value) loadSubCategories(this.value, false);
        else editCat2.innerHTML = '<option value="">-- Chọn danh mục con --</option>';
    });

    // 2. Thay đổi con -> load thuộc tính mới (trống)
    editCat2.addEventListener('change', function() {
        if(this.value) loadAttributes(this.value, false);
        else dynamicDiv.innerHTML = '';
    });

    loadParentCategories();

    // Autocomplete địa chỉ
    if (typeof setupAddressAutocomplete === "function") {
        setupAddressAutocomplete('edit-address-input', 'edit-address-list');
    }
});
</script>

<?php endif; ?>

<script>
    function changeImage(element) {
        document.getElementById('mainImage').src = element.src;
        let thumbs = document.querySelectorAll('.thumb-img');
        thumbs.forEach(img => img.classList.remove('active', 'border-primary'));
        element.classList.add('active', 'border-primary');
    }
</script>

<style>
    .thumb-img.active { border: 2px solid #0d6efd !important; opacity: 0.7; }
    .thumb-img:hover { transform: scale(1.05); transition: 0.3s; }
    .attributes-list { background: #f9f9f9; padding: 12px; border-radius: 4px; }
    .attribute-item { padding: 8px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; }
    .attr-name { font-weight: bold; color: #333; }
</style>