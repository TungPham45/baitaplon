<?php
$current_url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'Home/index';
?>
<aside class="sidebar">
    <div class="logo-box">
        <img src="/baitaplon/public/images/logo.png" alt="Logo" class="admin-logo">
        <span>C2C ADMIN</span>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="/baitaplon/Home/index" class="<?php echo (strpos($current_url, 'Home') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>
            <li>
                <a href="/baitaplon/Admin/dashboard" class="<?php echo (isset($active_page) && $active_page == 'user_management') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Quản lý tài khoản
                </a>
            </li>
            <li>
                <a href="/baitaplon/Admin/manageReports" class="<?php echo (strpos($current_url, 'manageReports') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Quản lý báo cáo
                </a>
            </li>
            <li>
                <a href="/baitaplon/Admin/manageProducts" class="<?php echo (strpos($current_url, 'manageProducts') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Quản lý sản phẩm
                </a>
            </li>
        </ul>
    </nav>
</aside>