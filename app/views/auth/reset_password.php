<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    <link rel="stylesheet" href="/baitaplon/public/css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="step-indicator">Bước 3/3</div>
        <h2>MẬT KHẨU MỚI</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="/baitaplon/Auth/resetPassword" method="POST">
            <div class="form-group">
                <label>Mật khẩu mới</label>
                <input type="password" name="new_password" required placeholder="Tối thiểu 6 ký tự">
            </div>
            <div class="form-group">
                <label>Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" required placeholder="Nhập lại mật khẩu mới">
            </div>
            <button type="submit" class="btn">Cập nhật mật khẩu</button>
        </form>
    </div>
</body>
</html>