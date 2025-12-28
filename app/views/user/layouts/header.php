<header class="user-header">
    <div class="header-container">
        <div class="header-logo">
            <a href="/baitaplon/Home/index">
                <img src="/baitaplon/public/images/logo.png" alt="Logo">
                <span>C2C MARKET</span>
            </a>
        </div>

        <div class="header-search">
            <form action="/baitaplon/Product/search" method="GET">
                <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </form>
        </div>

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