<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu - C2C Market</title>
    <link rel="stylesheet" href="/baitaplon/public/css/user_layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include_once 'layouts/header.php'; ?>

    <div class="password_container">
        <div class="profile-content-wrapper" style="justify-content: center;">
            <div style="background: #fff; padding: 30px; border-radius: 8px; width: 100%; max-width: 500px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h2 style="text-align: center; color: var(--chotot-orange);"><i class="fas fa-shield-alt"></i> Đổi mật khẩu</h2>
                
                <?php if(!empty($error)): ?>
                    <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 4px; margin-bottom: 15px;"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($success)): ?>
                    <div style="background: #d1fae5; color: #059669; padding: 10px; border-radius: 4px; margin-bottom: 15px;"><?php echo $success; ?></div>
                <?php endif; ?>

                <form action="/baitaplon/User/changePassword" method="POST">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Mật khẩu hiện tại</label>
                        <input type="password" name="old_password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Mật khẩu mới</label>
                        <input type="password" name="new_password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Xác nhận mật khẩu mới</label>
                        <input type="password" name="confirm_password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <button type="submit" style="background: var(--chotot-orange); color: #fff; border: none; width: 100%; padding: 12px; border-radius: 4px; font-weight: bold; cursor: pointer;">Cập nhật mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>