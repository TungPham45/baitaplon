<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân</title>
    <link rel="stylesheet" href="/baitaplon/public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container wide profile-card">
        <div style="text-align: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
            <div style="position: relative; display: inline-block;">
                <?php 
                    $avatarPath = !empty($user['avatar']) 
                        ? '/baitaplon/public/uploads/avatars/' . $user['avatar'] 
                        : '/baitaplon/public/uploads/avatars/default.png';
                ?>
                <img src="<?php echo $avatarPath; ?>" 
                     alt="Avatar" 
                     style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 4px solid #4f46e5; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </div>
            <h2 style="margin-top: 15px; margin-bottom: 5px;"><?php echo $user['hoten']; ?></h2>
            <p style="color: #667eea; font-weight: 600; font-size: 1.1rem;">
                <i class="fas fa-star" style="color: #f6ad55;"></i> 
                Đánh giá: <?php echo number_format($user['danhgia'], 1); ?> / 5.0
            </p>
        </div>

        <div class="info-row">
            <span class="info-label"><i class="fas fa-user-circle"></i> Tên tài khoản:</span>
            <span class="info-value"><?php echo $user['username']; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="fas fa-envelope"></i> Email:</span>
            <span class="info-value"><?php echo $user['email']; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="fas fa-phone"></i> Số điện thoại:</span>
            <span class="info-value"><?php echo !empty($user['sdt']) ? $user['sdt'] : '<em>Chưa cập nhật</em>'; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="fas fa-map-marker-alt"></i> Địa chỉ:</span>
            <span class="info-value"><?php echo !empty($user['diachi']) ? $user['diachi'] : '<em>Chưa cập nhật</em>'; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><i class="fas fa-id-badge"></i> Loại tài khoản:</span>
            <span class="info-value" style="background: #eef2ff; color: #4f46e5; padding: 2px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                <?php echo $user['role']; ?>
            </span>
        </div>

        <div style="margin-top: 20px;">
            <span class="info-label" style="display: block; margin-bottom: 10px;"><i class="fas fa-quote-left"></i> Giới thiệu:</span>
            <div style="background: #f9fafb; padding: 15px; border-radius: 12px; border-left: 4px solid #4f46e5; color: #4b5563; line-height: 1.6; font-style: italic;">
                <?php echo !empty($user['gioithieu']) ? nl2br($user['gioithieu']) : 'Người dùng này chưa có lời giới thiệu nào.'; ?>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <a href="/baitaplon/Admin/dashboard" class="btn" style="background: #858796; text-decoration: none; text-align: center; flex: 1;">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <a href="/baitaplon/Admin/editProfile" class="btn" style="text-decoration: none; text-align: center; flex: 1;">
                <i class="fas fa-edit"></i> Chỉnh sửa hồ sơ
            </a>
        </div>
    </div>
</body>
</html>