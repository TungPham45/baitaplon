<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chợ Tốt C2C - Trang chủ</title>
    <link rel="stylesheet" href="/baitaplon/public/css/user_layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <?php include_once 'layouts/header.php'; ?>

    <main class="content-container" style="max-width: 1200px; margin: 20px auto; padding: 0 15px;">
        <div class="alert alert-success">
            Chào mừng <strong><?php echo $_SESSION['username']; ?></strong> quay trở lại!
        </div>

        <h2 style="margin: 20px 0; font-size: 1.4rem;"><i class="fas fa-users"></i> Khám phá người dùng khác</h2>

        <div class="user-grid"
            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            <?php if (!empty($otherUsers)): ?>
                <?php foreach ($otherUsers as $u): ?>
                    <a href="/baitaplon/User/profile/<?php echo $u['id_user']; ?>" class="user-item-card"
                        style="text-decoration: none; color: inherit;">
                        <div class="card-white" style="text-align: center; padding: 20px; transition: 0.3s; cursor: pointer;">
                            <img src="/baitaplon/public/uploads/avatars/<?php echo !empty($u['anhdaidien']) ? basename($u['anhdaidien']) : 'default.png'; ?>"
                                style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 2px solid #ffba00;">
                            <h4 style="margin: 5px 0;"><?php echo $u['hoten']; ?></h4>
                            <p style="color: #f6ad55; font-size: 0.85rem; font-weight: bold;">
                                <?php echo number_format($u['danhgia'], 1); ?> ★
                            </p>
                            <p style="font-size: 0.8rem; color: #666;"><i class="fas fa-map-marker-alt"></i>
                                <?php echo $u['diachi'] ?: 'N/A'; ?></p>
                            <span
                                style="display: inline-block; margin-top: 10px; color: #ff8800; font-size: 0.85rem; font-weight: bold;">Xem
                                hồ sơ &raquo;</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Hiện chưa có người dùng nào khác.</p>
            <?php endif; ?>
        </div>
    </main>

</body>

</html>