<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị hệ thống - C2C</title>
    <link rel="stylesheet" href="/baitaplon/public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include_once __DIR__ . '/layouts/sidebar.php'; ?>

        <div class="main-layout">
            <?php include_once __DIR__ . '/layouts/header.php'; ?>

            <main class="content-area">
                <?php 
                    if (isset($contentView) && file_exists($contentView)) {
                        include_once $contentView;
                    } else {
                        echo '<div class="welcome-card"><h3>Chào mừng bạn đến với trang quản trị</h3></div>';
                    }
                ?>
            </main>
        </div>
    </div>
    <script src="/baitaplon/public/js/script.js"></script>
</body>
</html>