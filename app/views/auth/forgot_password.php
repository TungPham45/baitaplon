<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="/baitaplon/public/css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <h2>QUÊN MẬT KHẨU</h2>
        <p style="text-align: center; color: #666;">Nhập email tài khoản để nhận mã xác thực OTP.</p>
        
        <?php if(isset($error)): ?>
            <div class=\"alert alert-error\"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="/baitaplon/Auth/forgotPassword" method="POST">
            <div class="form-group">
                <label>Email đăng ký:</label>
                <input type="email" name="email" required placeholder="example@gmail.com">
            </div>
            <button type="submit" class="btn">Gửi mã OTP</button>
        </form>
        <div class="auth-footer">
            <a href="/baitaplon/Auth/login">Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>