<header class="user-header">
    <div class="header-container">
        <div class="header-logo">
            <a href="/baitaplon/Home/index">
                <img src="/baitaplon/public/images/logo.png" alt="Logo">
                <span>C2C MARKET</span>
            </a>
        </div>

        <div class="header-search" style="flex: 1; max-width: 800px; margin: 0 20px;">
    <?php
    // Hứng dữ liệu cho bộ lọc
    $keyword  = isset($data['keyword']) ? $data['keyword'] : '';
    $categoryTree = isset($data['categoryTree']) ? $data['categoryTree'] : []; // Cần truyền từ Controller
    $currentCatName = isset($data['currentCatName']) ? $data['currentCatName'] : 'Danh mục';
    $address  = isset($data['address']) ? $data['address'] : '';
    ?>
    
    <form action="/baitaplon/Product/search" method="GET" id="searchForm" class="d-flex align-items-center gap-1" style="display: flex; gap: 5px; width: 100%;">
        
        <input type="hidden" name="danhmuc" id="inputDanhmuc" value="<?php echo isset($data['category']) ? $data['category'] : ''; ?>">
        <input type="hidden" name="diachi" id="nav-input-diachi" value="<?php echo htmlspecialchars($address); ?>">

        <div class="dropdown" style="position: relative;">
            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="height: 40px; white-space: nowrap;">
                <span id="catDisplay"><?php echo htmlspecialchars($currentCatName); ?></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="position: absolute; z-index: 1000; background: #fff; border: 1px solid #ccc; list-style: none; padding: 0; display: none;">
                <li><a class="dropdown-item" href="#" onclick="selectCategory('', 'Tất cả danh mục'); return false;" style="display: block; padding: 8px 15px; text-decoration: none; color: #333;">Tất cả danh mục</a></li>
                
                <?php foreach ($categoryTree as $parent): ?>
                    <?php if (!empty($parent['children'])): ?>
                        <li class="dropdown-item-parent" style="position: relative;">
                            <a class="dropdown-item" href="#" onclick="selectCategory('<?php echo $parent['id_danhmuc']; ?>', '<?php echo $parent['ten_danhmuc']; ?>'); return false;" style="display: block; padding: 8px 15px; text-decoration: none; color: #333; font-weight: bold;">
                                <?php echo htmlspecialchars($parent['ten_danhmuc']); ?> &raquo;
                            </a>
                            <ul class="submenu" style="display: none; position: absolute; left: 100%; top: 0; background: #fff; border: 1px solid #ccc; width: 200px; list-style: none; padding: 0;">
                                <?php foreach ($parent['children'] as $child): ?>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="selectCategory('<?php echo $child['id_danhmuc']; ?>', '<?php echo $child['ten_danhmuc']; ?>'); return false;" style="display: block; padding: 8px 15px; text-decoration: none; color: #333;">
                                            <?php echo htmlspecialchars($child['ten_danhmuc']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li>
                            <a class="dropdown-item" href="#" onclick="selectCategory('<?php echo $parent['id_danhmuc']; ?>', '<?php echo $parent['ten_danhmuc']; ?>'); return false;" style="display: block; padding: 8px 15px; text-decoration: none; color: #333;">
                                <?php echo htmlspecialchars($parent['ten_danhmuc']); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>

        <input type="text" name="keyword" class="form-control" placeholder="Tìm sản phẩm..." value="<?php echo htmlspecialchars($keyword); ?>" style="flex: 1; height: 40px; padding: 0 10px; border: 1px solid #ccc;">

        <div class="dropdown" id="nav-location-dropdown" style="position: relative;">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="navLocationBtn" data-bs-toggle="dropdown" aria-expanded="false" style="height: 40px; max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; background: #fff; border: 1px solid #ccc;">
                <?php echo !empty($address) ? htmlspecialchars($address) : 'Toàn quốc'; ?>
            </button>
            
            <div class="dropdown-menu p-2 shadow mt-1" aria-labelledby="navLocationBtn" style="width: 300px; position: absolute; right: 0; z-index: 1000; background: #fff; border: 1px solid #ccc; display: none; padding: 10px;">
                <div class="input-group mb-2" style="display: flex; margin-bottom: 10px;">
                    <input type="text" class="form-control" id="nav-search-box" placeholder="Nhập Huyện hoặc Tỉnh..." autocomplete="off" style="width: 100%; padding: 5px;">
                </div>
                <div class="list-group list-group-flush" id="nav-location-list" style="max-height: 300px; overflow-y: auto;">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 15px; background: #f59e0b; color: #fff; border: none; cursor: pointer;">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<style>
    .dropdown-item-parent:hover .submenu { display: block !important; }
    /* Hiển thị dropdown Bootstrap nếu chưa có JS bootstrap */
    .dropdown:hover .dropdown-menu { display: block; } 
</style>
        <div class="header-extras">
            <div class="user-profile-section">
                <div class="user-info-trigger" onclick="toggleDropdown()">
                    <img src="/baitaplon/public/uploads/avatars/<?php echo !empty($user['avatar']) ? $user['avatar'] : 'default.png'; ?>" alt="User" class="avatar-circle">
                    <span><?php echo $_SESSION['username']; ?></span>
                    <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                </div>
                
                <div id="userDropdown" class="user-dropdown-content">
                    <a href="/baitaplon/User/profile">
                        <i class="fas fa-user-circle"></i> Thông tin cá nhân
                    </a>
                    <a href="/baitaplon/User/changePassword">
                        <i class="fas fa-key"></i> Đổi mật khẩu
                    </a>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
                    <a href="/baitaplon/Auth/logout" style="color: #e74a3b;">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </div>
            </div>
            
            <a href="/baitaplon/Product/post" class="btn-post">
                <i class="fas fa-edit"></i> ĐĂNG TIN
            </a>
        </div>
    </div>
</header>
<script src="/baitaplon/Public/js/home_js.js"></script>
<script>
function toggleDropdown() {
    document.getElementById("userDropdown").classList.toggle("show");
}

// Đóng dropdown nếu nhấn ra ngoài
window.onclick = function(event) {
    if (!event.target.closest('.user-profile-section')) {
        var dropdowns = document.getElementsByClassName("user-dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
</script>