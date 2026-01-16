<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị hệ thống - C2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/baitaplon/public/js/script.js"></script>
    <script src="/baitaplon/public/js/exportExcel.js"></script>
</body>
</html>