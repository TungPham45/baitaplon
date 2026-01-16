<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân</title>
    <link rel="stylesheet" href="/baitaplon/public/css/admin.css">
    <link rel="stylesheet" href="/baitaplon/public/css/profile_css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-container">
        <?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <div class="main-layout">
            <?php include_once __DIR__ . '/../layouts/header.php'; ?>

            <main class="content-area admin-profile-view">
                <div class="auth-container wide profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar-large">
                            <?php
                            $avatarFilename = !empty($user['avatar']) ? basename($user['avatar']) : 'default.png';
                            $avatarPath = '/baitaplon/public/uploads/avatars/' . htmlspecialchars($avatarFilename);
                            ?>
                            <img src="<?php echo $avatarPath; ?>" alt="Avatar">
                        </div>
                        <h2 class="profile-name"><?php echo htmlspecialchars($user['hoten']); ?></h2>
                        <p class="profile-rating">
                            <i class="fas fa-star"></i>
                            Đánh giá: <?php echo number_format($user['danhgia'] ?? 0, 1); ?> / 5.0
                        </p>
                    </div>

                    <div class="profile-info-section">
                        <div class="info-row">
                            <i class="fas fa-user-circle"></i>
                            <span class="info-label">Tên tài khoản:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-envelope"></i>
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-phone"></i>
                            <span class="info-label">Số điện thoại:</span>
                            <span class="info-value <?php echo empty($user['sdt']) ? 'empty' : ''; ?>">
                                <?php echo !empty($user['sdt']) ? htmlspecialchars($user['sdt']) : 'Chưa cập nhật'; ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="info-label">Địa chỉ:</span>
                            <span class="info-value <?php echo empty($user['diachi']) ? 'empty' : ''; ?>">
                                <?php echo !empty($user['diachi']) ? htmlspecialchars($user['diachi']) : 'Chưa cập nhật'; ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-id-badge"></i>
                            <span class="info-label">Loại tài khoản:</span>
                            <span class="info-value">
                                <span class="role-badge"><?php echo htmlspecialchars($user['role']); ?></span>
                            </span>
                        </div>
                    </div>

                    <div class="bio-section">
                        <span class="bio-label"><i class="fas fa-quote-left"></i> Giới thiệu:</span>
                        <div class="bio-content">
                            <?php echo !empty($user['gioithieu']) ? nl2br(htmlspecialchars($user['gioithieu'])) : 'Người dùng này chưa có lời giới thiệu nào.'; ?>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="/baitaplon/Admin/dashboard" class="btn btn-secondary-custom">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <a href="/baitaplon/Admin/editProfile" class="btn btn-primary-custom">
                            <i class="fas fa-edit"></i> Chỉnh sửa hồ sơ
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>