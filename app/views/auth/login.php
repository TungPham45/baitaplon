<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - C2C Market</title>
    <link rel="stylesheet" href="/baitaplon/public/css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <h2>Đăng Nhập</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form action="/baitaplon/Auth/login" method="POST">
            <div class="form-group">
                <label>Tên tài khoản hoặc Email:</label>
                <input type="text" name="username_or_email" required placeholder="admin@test.com">
            </div>
            <div class="form-group">
                <label>Mật khẩu:</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn">Đăng nhập ngay</button>
        </form>
        
        <div class="auth-footer">
            <a href="/baitaplon/Auth/forgotPassword">Quên mật khẩu?</a>
            <span style="color: #ccc; margin: 0 10px;">|</span>
            <a href="/baitaplon/Auth/registerStep1">Đăng ký ngay</a>
        </div>
    </div>
</body>
</html>