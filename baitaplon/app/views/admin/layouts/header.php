<header class="top-navbar">
    <div class="function-name">
        <h3><?php echo isset($functionTitle) ? $functionTitle : 'Bảng điều khiển'; ?></h3>
    </div>
    
    <div class="profile-section">
        <div class="profile-info">   
            <img src="/baitaplon/public/uploads/avatars/default.png" alt="Avatar" class="avatar-small">
            <span><?php echo $_SESSION['username']; ?></span>
        </div>
        <div class="profile-dropdown">
            <a href="/baitaplon/Admin/profile"><i class="fas fa-user"></i> Hồ sơ cá nhân</a>
            <a href="#"><i class="fas fa-key"></i> Đổi mật khẩu</a>
            <hr>
            <a href="/baitaplon/Auth/logout" class="logout-link"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </div>
    </div>
</header>